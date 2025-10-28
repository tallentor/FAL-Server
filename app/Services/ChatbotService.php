<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    private $client;
    private $apiKey;
    private $baseUrl;
    private $modelName;
    private $responses;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('services.gemini.api_key');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';
        $this->modelName = 'gemini-2.5-pro';

        // Fallback responses for when API is unavailable
        $this->responses = [
           
        ];
    }

    public function getResponse(string $userMessage, array $conversationHistory = []): string
    {
        // Try Gemini API first
        if ($this->apiKey) {
            try {
                return $this->generateChatResponse($userMessage, $conversationHistory);
            } catch (\Exception $e) {
                Log::error('Gemini API failed, falling back to static responses: ' . $e->getMessage());
            }
        }

        // Fallback to static responses
        return $this->getStaticResponse($userMessage);
    }

    private function generateChatResponse($userMessage, $conversationHistory = [])
    {
        try {
            $contents = $this->prepareConversationContents($userMessage, $conversationHistory);

            $url = $this->baseUrl . $this->modelName . ':generateContent?key=' . $this->apiKey;

            $payload = [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ],
                'tools' => [
                    [
                        'googleSearch' => (object)[]
                    ]
                ],
                'systemInstruction' => [
                    'parts' => [
                        [
                            'text' => 'You have access to Google Search for current information when needed.'
                        ]
                    ]
                ]
            ];

            $response = $this->client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
                'timeout' => 30,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                return $responseData['candidates'][0]['content']['parts'][0]['text'];
            }

            return "I apologize, but I'm having trouble processing your request at the moment. Please try again.";

        } catch (RequestException $e) {
            Log::error('Gemini API Request Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function prepareConversationContents($userMessage, $conversationHistory = [])
    {
        $systemPrompt = 'You are an AI assistant. Your role is to help customers with basic inquiries.

        Important Rules:
        - Always keep your answers short and clear (no more than 20 words).
        - Do not explain in detail. Be direct and to the point.
        - If you don\'t know the answer, politely say: "Please contact directly for more help."
        - Your tone should be professional, friendly, and helpful.
        - Never generate prices unless specified in training data.

        If you do not know the answer to a user s question, do not attempt to guess or provide alternatives. Instead, politely respond only with the exact phrase: "Please contact directly for more help."

        Additional Training Examples:
        ';
        
        $contents = [

            [
                'role' => 'user',
                'parts' => [
                    [
                        'text' => $systemPrompt
                    ]
                ]
            ],
            [
                'role' => 'model',
                'parts' => [
                    [
                        'text' => 'Hello! I\'m here to help with you. How can I assist you today?'
                    ]
                ]
            ]
            
        ];


        $additionalPrompts = $this->getAdditionalPrompts();
        foreach ($additionalPrompts as $prompt) {
            $contents[] = [
                'role' => 'user',
                'parts' => [
                    [
                        'text' => $prompt->prompt_text
                    ]
                ]
            ];
        }

        foreach ($conversationHistory as $message) {
            $contents[] = [
                'role' => $message['role'],
                'parts' => [
                    [
                        'text' => $message['content']
                    ]
                ]
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [
                [
                    'text' => $userMessage
                ]
            ]
        ];

        return $contents;
    }


    private function getAdditionalPrompts()
    {
        return \App\Models\SystemPrompt::where('is_active', true)->get(['prompt_text']);
    }

    private function getStaticResponse(string $userMessage): string
    {
        $userMessage = strtolower(trim($userMessage));

        if ($this->containsGreeting($userMessage)) {
            return "Hello! I'm the Find a Lawyer AI assistant. How can I help you today?";
        }

        foreach ($this->responses as $category => $data) {
            foreach ($data['keywords'] as $keyword) {
                if (strpos($userMessage, strtolower($keyword)) !== false) {
                    return $data['response'];
                }
            }
        }

        return "Please contact directly for more help. Visit our website for more information.";
    }

    private function containsGreeting(string $message): bool
    {
        $greetings = ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening'];

        foreach ($greetings as $greeting) {
            if (strpos($message, $greeting) !== false) {
                return true;
            }
        }

        return false;
    }
}
