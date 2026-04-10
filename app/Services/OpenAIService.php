<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Session;
class OpenAIService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('OPENAI_API_KEY');
    }

    public function generateFormQuestions($prompt)
    {
        $final_prompt = $prompt." This is my sample json of form response {'form_name':'Name of form','questions':[{id:'','type':'','question':'','options',[]}]} options parameter only if type is radio,dropdown or checkbox. If any question need date then set it type as date. Please send only json format response.";
        $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4', // or use 'gpt-3.5-turbo'
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an assistant that generates form questions in JSON format for immigration cases.'],
                    ['role' => 'user', 'content' => $final_prompt]
                ],
                'temperature' => 0.7
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    public function generateVisaCaseDescription($prompt)
    {
        $formattedPrompt = $this->buildPrompt($prompt);
       
        $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a professional immigration consultant generating case descriptions for visa applications. Provide a well-structured and professional case description.'],
                    ['role' => 'user', 'content' => $formattedPrompt],
                ],
                'temperature' => 0.7
            ]
        ]);
        $result = json_decode($response->getBody(), true);
        return $result['choices'][0]['message']['content'] ?? 'Unable to generate case description.';
       
    }

    private function buildPrompt($prompt)
    {
        return "Generate a professional visa application case description based on the following request: \"$prompt\". The response should be structured, concise, and suitable for immigration professionals.";
    }

    public function startConversation($prompt)
    {
        // Store the prompt in session and reset conversation history
        Session::put('conversation', []);
        Session::put('prompt', $prompt);

        return $this->getNextQuestion($prompt);
    }

    public function processUserResponse($userResponse)
    {
        // Get conversation history
        $conversation = Session::get('conversation', []);
        $prompt = Session::get('prompt', '');

        // Append user response to history
        $conversation[] = ['role' => 'user', 'content' => $userResponse];

        // Call OpenAI API to decide next step
        $nextStep = $this->callOpenAI($conversation, 'gpt-4', "Based on the conversation so far, should I ask another question, or is enough information available to generate the case description? If another question is needed, just return the question. Otherwise, return 'GENERATE_DESCRIPTION'.");

        if (stripos($nextStep, 'GENERATE_DESCRIPTION') !== false) {
            // If ready to generate description, call the description generator
            return ['status' => 'generate', 'description' => $this->generateVisaCaseDescription($conversation)];
        } else {
            // Otherwise, ask the next question
            $conversation[] = ['role' => 'assistant', 'content' => $nextStep];
            Session::put('conversation', $conversation);
            return ['status' => 'ask', 'question' => $nextStep];
        }
    }


    public function generateQuestions($prompt)
    {
        // Generate a structured JSON response with relevant questions
        $formattedPrompt = "The user is applying for a visa with the request: \"$prompt\". 
        Generate a JSON array of essential questions to collect all necessary details for processing the visa case. 
        The response format should be: 
        {
            'questions': [
                {'id': 1, 'question': 'What is your full name?'},
                {'id': 2, 'question': 'What is your nationality?'},
                {'id': 3, 'question': 'What is your highest level of education?'},
                {'id': 4, 'question': 'Do you have sufficient financial proof for your studies?'}
            ]
        }.
        Provide only the JSON response.";

        $questions = $this->callOpenAI($formattedPrompt);
        $result = json_decode($questions, true) ?? 'Invalid response.';
        // Store generated questions in session
        Session::put('visa_questions', $result);

        return $questions;
    }

    /**
     * Generate the final case description after receiving all answers.
     */
    public function generateCaseDescription($answers,$questions)
    {
        // Retrieve stored questions
        // Format conversation with questions & answers
        $conversation = [];
        foreach ($questions as $index => $q) {
            $conversation[] = ['question' => $q['question'], 'answer' => $answers[$index] ?? 'Not provided'];
        }

        $formattedPrompt = "Based on the following details, generate a professional visa application case description:\n\n"
            . json_encode($conversation) . 
            "\n\nEnsure the description is structured, concise, and written professionally for immigration professionals.";
        return $this->callOpenAI($formattedPrompt);
    }

    /**
     * Common method to call OpenAI API.
     */
    private function callOpenAI($prompt)
    {
        try {
            $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a visa application expert assisting users.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1000
                ]
            ]);

            return json_decode($response->getBody(), true)['choices'][0]['message']['content'] ?? 'Unable to generate response.';
        } catch (RequestException $e) {
            return 'Error communicating with OpenAI API: ' . $e->getMessage();
        }
    }

    public function generateCaseProposal(string $caseDescription): string
    {

        try {
            $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are an immigration consultant preparing detailed proposals for client cases.'],
                        ['role' => 'user', 'content' => "Create a proposal for the following immigration case:\n\n{$caseDescription}"],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1000
                ]
            ]);
            return json_decode($response->getBody(), true)['choices'][0]['message']['content'] ?? 'Unable to generate response.';
        } catch (RequestException $e) {
            return 'Error communicating with OpenAI API: ' . $e->getMessage();
        }
        // $response = Http::withToken($this->apiKey)
        //     ->post('https://api.openai.com/v1/chat/completions', [
        //         'model' => 'gpt-4', // or 'gpt-3.5-turbo'
        //         'messages' => [
        //             ['role' => 'system', 'content' => 'You are an immigration consultant preparing detailed proposals for client cases.'],
        //             ['role' => 'user', 'content' => "Create a proposal for the following immigration case:\n\n{$caseDescription}"],
        //         ],
        //         'temperature' => 0.7,
        //         'max_tokens' => 1000,
        //     ]);

        // return $response->json('choices.0.message.content');
    }
}
