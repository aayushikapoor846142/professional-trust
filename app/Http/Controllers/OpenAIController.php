<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAIService;

class OpenAIController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function generateForm(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string'
        ]);

        $response = $this->openAIService->generateFormQuestions($request->input('prompt'));
        \Log::info("------------------------------------ API RESPONSE ------------------------------------");
        \Log::info($response);
        $contents = $response['choices'][0]['message']['content'] ?? array();
        preg_match_all('/\{(?:[^{}]|(?R))*\}/s', $contents, $matches);
        $data = $matches[0][0];
        $data_arr = json_decode($data,true);
        // \Log::info("------------------------------------ DATA ARRAY RESPONSE ------------------------------------");
        // \Log::info($data_arr);
        // \Log::info("------------------------------------ Form Json RESPONSE ------------------------------------");

        $sample_json = formJsonSample();
    // pre($data_arr);
        $json_sample = array();
        foreach($sample_json as $js){
            $json_sample[$js['fields']] = $js;
        }
        // pre(json_encode(json_decode($data)));
        $form_json = array();
        foreach($data_arr['questions'] as $json){
            // pre($json);
            $field_format = array();
            if($json['type'] == 'text'){
                $field_format = $json_sample['textInput'];
                $field_format['settings']['label'] = $json['question'];
                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                $field_format['index'] = randomNumber();
                $form_json[] = $field_format;
            }
            if($json['type'] == 'number'){
                $field_format = $json_sample['numberInput'];
                $field_format['settings']['label'] = $json['question'];
                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                $form_json[] = $field_format;
            }
            if($json['type'] == 'radio'){
                $field_format = $json_sample['radio'];
                $field_format['settings']['label'] = $json['question'];
                $field_format['settings']['options'] = $json['options'];
                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                $form_json[] = $field_format;
            }
            if($json['type'] == 'checkbox'){
                $field_format = $json_sample['checkbox'];
                $field_format['settings']['label'] = $json['question'];
                $field_format['settings']['options'] = $json['options'];
                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                $form_json[] = $field_format;
            }
            if($json['type'] == 'dropdown'){
                $field_format = $json_sample['dropDown'];
                $field_format['settings']['label'] = $json['question'];
                $field_format['settings']['options'] = $json['options'];
                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                $form_json[] = $field_format;
            }
            if($json['type'] == 'email'){
                $field_format = $json_sample['emailInput'];
                $field_format['settings']['label'] = $json['question'];
                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                $form_json[] = $field_format;
            }
            if($json['type'] == 'textarea'){
                $field_format = $json_sample['textarea'];
                $field_format['settings']['label'] = $json['question'];
                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                $form_json[] = $field_format;
            }
            if($json['type'] == 'date'){
                $field_format = $json_sample['dateInput'];
                $field_format['settings']['label'] = $json['question'];
                $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
                $form_json[] = $field_format;
            }
        }
        // \Log::info($form_json);
        $res['original_response'] = $data_arr;
        $res['formatted_response'] = $form_json;
        return response()->json($res);
    }

    public function generateVisaCase(Request $request)
    {
        $request->validate([
            'prompt' => 'required',
        ]);

        $userPrompt = $request->input('prompt');
        $caseDescription = $this->openAIService->generateVisaCaseDescription($userPrompt);

        return response()->json(['case_description' => $caseDescription]);
    }

    public function startConversation(Request $request)
    {
        $request->validate(['prompt' => 'required|string']);

        $prompt = $request->input('prompt');
        $questions = $this->openAIService->generateQuestions($prompt);

        return response()->json(['questions' => $questions]);
    }

    // Process user response and continue the conversation
    public function processResponse(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
            'questions' => 'required|array'
        ]);

        $answers = $request->input('answers');
        $questions = $request->input('questions');
        
        $caseDescription = $this->openAIService->generateCaseDescription($answers,$questions);

        return response()->json(['case_description' => $caseDescription]);
    }

    public function generateCaseProposal(Request $request)
    {
        $description = $request->input('description');
        echo $description;
        $proposal = $this->openAIService->generateCaseProposal($description);

        return response()->json(['status'=>true,'proposal' => $proposal]);
    }
}
