<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\LawyerProfile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private $system_fee = 5000; // Fixed system fee

    // Step 1: Create order and return PayHere checkout URL
   public function createPayment(Request $request)
{
    $request->validate([
        'lawyer_id' => 'required|exists:users,id',
    ]);

    $user = $request->user(); // Authenticated customer

    $lawyerProfile = LawyerProfile::where('user_id', $request->lawyer_id)->firstOrFail();
    
    // Fees
    $lawyer_fee = "500.00"; // or $lawyerProfile->fee
    $system_fee = $this->system_fee;
    $total_amount = $lawyer_fee + $system_fee;

    // ✅ Fix: user_id should be the customer’s id, not the lawyer’s
    $order = Order::create([
        'user_id' => $lawyerProfile->user->id, // customer
        'lawyer_id' => $request->lawyer_id,
        'lawyer_profile_id' => $lawyerProfile->id,
        'lawyer_fee' => $lawyer_fee,
        'system_fee' => $system_fee,
        'total_amount' => $total_amount,
        'status' => 'pending',
    ]);

    // PayHere payment data
    $merchant_id = env('PAYHERE_MERCHANT_ID');
    $return_url = route('payhere.payment.success', $order->id);
    $cancel_url = route('payhere.payment.cancel', $order->id);
    $notify_url = route('payhere.payment.ipn');

    // ✅ Important: payment info should belong to the *customer*, not lawyer
    $payment_data = [
        'merchant_id' => $merchant_id,
        'return_url' => $return_url,
        'cancel_url' => $cancel_url,
        'notify_url' => $notify_url,
        'order_id' => $order->id,
        'items' => 'Lawyer Hearing Fee',
        'currency' => 'LKR',
        'amount' => $total_amount,
        'first_name' => $lawyerProfile->user->name,
        'last_name' => '',
        'email' => $lawyerProfile->user->email,
        'phone' => $lawyerProfile->user->phone ?? '0770000000',
        'address' => $lawyerProfile->user->address ?? 'N/A',
        'city' => 'Colombo',
        'country' => 'Sri Lanka',
    ];

    return response()->json([
        'success' => true,
        'message' => 'Order created',
        'order' => $order,
        'payment_data' => $payment_data,
    ]);
}


    // Step 2: Payment success
    public function paymentSuccess($orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->status = 'paid';
        $order->save();

        return response()->json(['success' => true, 'message' => 'Payment successful', 'order' => $order]);
    }

    // Step 3: Payment cancel
    public function paymentCancel($orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->status = 'failed';
        $order->save();

        return response()->json(['success' => false, 'message' => 'Payment cancelled']);
    }

    // Step 4: IPN callback
    public function handleIPN(Request $request)
    {
        $merchant_secret = env('PAYHERE_SECRET');
        $order_id = $request->order_id;
        $status_code = $request->status_code;
        $md5sig = $request->md5sig;

        $order = Order::find($order_id);
        if (!$order) return response('Order Not Found', 404);

        $local_md5sig = strtoupper(md5($order_id . env('PAYHERE_MERCHANT_ID') . $status_code . number_format($request->payhere_amount, 2, '.', '') . $request->payhere_currency . strtoupper(md5($merchant_secret))));

        if ($md5sig === $local_md5sig && $status_code == 2) {
            $order->status = 'paid';
            $order->save();
            return response('IPN OK', 200);
        }

        return response('IPN Failed', 400);
    }

    // Step 5: Get PayHere OAuth Access Token
    private function getPayHereAccessToken()
    {
        $app_id = env('PAYHERE_APP_ID');
        $app_secret = env('PAYHERE_APP_SECRET');
        
        // Base64 encode credentials
        $credentials = base64_encode($app_id . ':' . $app_secret);
        
        $base_url = env('PAYHERE_MODE', 'sandbox') === 'live' 
            ? 'https://www.payhere.lk' 
            : 'https://sandbox.payhere.lk';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $credentials,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post($base_url . '/merchant/v1/oauth/token', [
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'] ?? null;
            }

            Log::error('PayHere OAuth Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('PayHere OAuth Exception: ' . $e->getMessage());
            return null;
        }
    }

    // Step 6: Refund payment
    public function refundPayment(Request $request, $orderId)
    {
        $request->validate([
            'payment_id' => 'required',
            'refund_amount' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string|max:255',
        ]);

        $order = Order::findOrFail($orderId);

        // Check if order is eligible for refund
        if ($order->status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Only paid orders can be refunded',
            ], 400);
        }

        if ($order->status === 'refunded' || $order->status === 'refund_processing') {
            return response()->json([
                'success' => false,
                'message' => 'Order has already been refunded or is being processed',
            ], 400);
        }

        // Get access token
        $access_token = $this->getPayHereAccessToken();
        
        if (!$access_token) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to authenticate with PayHere',
            ], 500);
        }

        $base_url = env('PAYHERE_MODE', 'sandbox') === 'live' 
            ? 'https://www.payhere.lk' 
            : 'https://sandbox.payhere.lk';

        // Determine refund amount (full or partial)
        $refund_amount = $request->refund_amount ?? $order->total_amount;
        
        if ($refund_amount > $order->total_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Refund amount cannot exceed order total',
            ], 400);
        }

        try {
            // Call PayHere Refund API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
            ])->post($base_url . '/merchant/v1/payment/refund', [
                'order_id' => $order->id,
                'payment_id' => $request->payment_id,
                'amount' => number_format($refund_amount, 2, '.', ''),
                'reason' => $request->reason ?? 'Customer requested refund',
            ]);

            if ($response->successful()) {
                $refund_data = $response->json();
                
                // Update order status
                $order->status = $refund_amount == $order->total_amount 
                    ? 'refund_processing' 
                    : 'partial_refund_processing';
                $order->refund_amount = $refund_amount;
                $order->refund_reason = $request->reason;
                $order->refunded_at = now();
                $order->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Refund initiated successfully',
                    'order' => $order,
                    'refund_data' => $refund_data,
                ]);
            }

            // Handle error response
            $error_data = $response->json();
            Log::error('PayHere Refund Error: ' . $response->body());

            return response()->json([
                'success' => false,
                'message' => 'Refund request failed',
                'error' => $error_data['msg'] ?? 'Unknown error',
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('PayHere Refund Exception: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing refund',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Step 7: Check refund status
    public function checkRefundStatus($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Get access token
        $access_token = $this->getPayHereAccessToken();
        
        if (!$access_token) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to authenticate with PayHere',
            ], 500);
        }

        $base_url = env('PAYHERE_MODE', 'sandbox') === 'live' 
            ? 'https://www.payhere.lk' 
            : 'https://sandbox.payhere.lk';

        try {
            // Call PayHere Retrieval API to check payment status
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
            ])->get($base_url . '/merchant/v1/payment/search', [
                'order_id' => $order->id,
            ]);

            if ($response->successful()) {
                $payment_data = $response->json();
                
                // Update order status based on payment status
                if (isset($payment_data['data'][0]['status_code'])) {
                    $status_code = $payment_data['data'][0]['status_code'];
                    
                    // Status codes: -2 (refunded), 2 (success), 0 (pending), -1 (canceled), -3 (chargeback)
                    if ($status_code == -2) {
                        $order->status = 'refunded';
                        $order->save();
                    }
                }

                return response()->json([
                    'success' => true,
                    'order' => $order,
                    'payment_status' => $payment_data,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment status',
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('PayHere Status Check Exception: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while checking status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}