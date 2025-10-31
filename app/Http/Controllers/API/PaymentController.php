<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\LawyerProfile;
use App\Http\Controllers\Controller;

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
        // $lawyer_fee = $lawyerProfile->fee; // fee from profile
        $lawyer_fee = "500.00"; // fee from profile
        $system_fee = $this->system_fee;
        $total_amount = $lawyer_fee + $system_fee;

        // Create order
        $order = Order::create([
            'user_id' => $user->id,
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

        $payment_data = [
            'merchant_id' => $merchant_id,
            'return_url' => $return_url,
            'cancel_url' => $cancel_url,
            'notify_url' => $notify_url,
            'order_id' => $order->id,
            'items' => 'Lawyer Hearing Fee',
            'currency' => 'LKR',
            'amount' => $total_amount,
            'first_name' => $user->name,
            'last_name' => '', // optional
            'email' => $user->email,
            'phone' => $user->phone ?? '0770000000',
            'address' => $user->address ?? 'N/A',
            'city' => 'Colombo',
            'country' => 'Sri Lanka',
        ];

        $query = http_build_query($payment_data);
        $checkout_url = 'https://sandbox.payhere.lk/pay/checkout?' . $query;

        return response()->json([
            'success' => true,
            'message' => 'Order created',
            'order' => $order,
            'checkout_url' => $checkout_url
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
}