<?php

namespace App\Services;

use App\Models\CaseStages;
use App\Models\CaseWithProfessionals;
use App\Models\Forms;
use App\Models\DocumentsFolder;
use App\Models\CaseFolders;
use App\Models\CaseSubStages;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentLinkParameter;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class CaseSubStageService
{
    // Add methods for add, save, edit, update, delete, markAsComplete, updateSorting, and signature generation
    // Each method should encapsulate the business logic from the controller
    // Do not handle request/response or view rendering here
    // Accept parameters as needed and return data/objects for the controller to use

    public function saveSubStage(array $data, $stage_id, $user)
    {
        // Validation should be done in controller
        $form_json = "";
        $form_id = null;
        $folder_id = null;
        if($data["stage_type"] == "fill-form"){
            $form_id = $data["form_id"];
            $form = Forms::where('id',$data["form_id"])->first();
            $form_json = $form ? $form->fg_field_json : null;
        } else if($data["stage_type"] == "case-document") {
            $folder_id = json_encode($data["folder"]);
        }

        $stage = CaseStages::where("unique_id",$stage_id)->first();
        if (!$stage) {
            return ["status" => false, "message" => ["stage_id" => "Stage not found"]];
        }
        $case_id = $stage->case_id;

        $nextSubStage = CaseSubStages::where('case_id',$stage->case_id)
            ->where('stage_id',$stage->id)
            ->orderBy('sort_order', 'asc')
            ->where(function($q){
                $q->where('status','pending')->orWhere('status','in-progress');
            })
            ->first();

        $status = "pending";
        if(empty($nextSubStage)){
            $status="in-progress";
        }

        $case = CaseWithProfessionals::where('id',$case_id)->first();
        if (!$case) {
            return ["status" => false, "message" => ["case_id" => "Case not found"]];
        }

        $caseSubStages = CaseSubStages::where('stage_id',$stage->id)->where('case_id',$case_id)->orderBy('id','desc')->first();
        $sort_order = 1;
        if(!empty($caseSubStages)){
            $sort_order = $caseSubStages->sort_order + 1;
        }
        $object = new CaseSubStages();
        $object->case_id = $case_id;
        $object->client_id = $case->client_id;
        $object->user_id = $user->id;
        $object->unique_id = randomNumber();
        $object->stage_id = $stage->id;
        $object->name = $data["name"];
        $object->sort_order = $sort_order;
        $object->form_json = $form_json;
        $object->stage_type = $data["stage_type"];
        $object->status = $status;
        $object->fees = $data["fees"] ?? null;
        $object->description = $data["description"] ?? null;
        $object->added_by = $user->id;
        $object->form_id = $form_id;
        $object->folder_id = $folder_id;
        $object->save();

        if($data["stage_type"] == "payment"){
            $latest = Invoice::latest()->first();
            $userModel = User::where('id',$case->client_id)->first();
            if (!$userModel) {
                return ["status" => false, "message" => ["client_id" => "User not found"]];
            }
            $invoice = new Invoice();
            $invoice->unique_id = randomNumber();
            $invoice->invoice_number = $latest ? $latest->invoice_number + 1 : 1;
            $invoice->tax = 0;
            $invoice->sub_total = $data["fees"];
            $invoice->total_amount = $data["fees"];
            $invoice->currency = 'CAD';
            $invoice->user_id = $userModel->id;
            $invoice->first_name = $userModel->first_name;
            $invoice->last_name = $userModel->last_name;
            $invoice->email = $userModel->email;
            $invoice->country_code = $userModel->country_code;
            $invoice->phone_no = $userModel->phone_no;
            $invoice->invoice_type = 'sub-stages-fees';
            $invoice->reference_id = $object->id;
            $invoice->payment_status = 'pending';
            $invoice->added_by = $user->id;
            $invoice->invoice_date = date('Y-m-d');
            $invoice->due_date = date('Y-m-d', strtotime('+1 week'));
            $invoice->notes = "Invoice Created for Case:".$case->case_title.' Stage:'.$stage->name.' Sub Stage:'.$object->name;
            $invoice->discount = 0;
            $invoice->save();

            InvoiceItem::create([
                'particular' => $data["payment_description"],
                'amount' => $data["fees"],
                'invoice_id' => $invoice->id
            ]);

            $token = randomString();
            $params = [
                'user_id' => \Crypt::encryptString($invoice->user_id),
                'token' => \Crypt::encryptString($token),
                'invoice_id' => \Crypt::encryptString($invoice->id),
                'transaction_id' => \Crypt::encryptString(0),
            ];

            $paymentLinkParam = new PaymentLinkParameter;
            $paymentLinkParam->user_id = encryptVal($invoice->user_id);
            $paymentLinkParam->token = encryptVal($token);
            $paymentLinkParam->invoice_id = encryptVal($invoice->id);
            $paymentLinkParam->transaction_id = encryptVal(0);
            $paymentLinkParam->added_by = $user->id;
            $paymentLinkParam->signature = $this->generateSignature($params);
            $paymentLinkParam->save();
        }
        return ["status" => true, "message" => "Record added successfully"];
    }

    public function getAddSubStageData($stage_id, $user)
    {
        $stage = CaseStages::where('unique_id', $stage_id)->first();
        if (!$stage) return null;
        return [
            'forms' => Forms::where('added_by', $user->id)->get(),
            'default_documents' => DocumentsFolder::where('user_id', $user->id)->get(),
            'custom_documents' => CaseFolders::where('added_by', $user->id)->where('case_id', $stage->case_id)->get(),
            'pageTitle' => 'Add Case Sub Stage',
            'stage_id' => $stage_id,
        ];
    }

    public function getEditSubStageData($id, $user)
    {
        $subStage = CaseSubStages::where('unique_id', $id)->first();
        if (!$subStage) return null;
        $stage = CaseStages::where('id', $subStage->stage_id)->first();
        $invoice = Invoice::where('reference_id', $subStage->id)->first();
        $invoiceItem = $invoice ? InvoiceItem::where('invoice_id', $invoice->id)->first() : null;
        return [
            'pageTitle' => 'Edit Case Sub Stage',
            'record' => $subStage,
            'forms' => Forms::where('added_by', $user->id)->get(),
            'default_documents' => DocumentsFolder::where('user_id', $user->id)->get(),
            'custom_documents' => $stage ? CaseFolders::where('added_by', $user->id)->where('case_id', $stage->case_id)->get() : [],
            'invocieItem' => $invoiceItem,
        ];
    }

    private function findPaymentLinkParameterByInvoiceId($invoiceId, $userId = null) {
        $query = PaymentLinkParameter::query();
        if ($userId) {
            $query->where('added_by', $userId);
        }
        // Optionally, filter by created_at or other fields if available
        $candidates = $query->get();
        return $candidates->first(function ($record) use ($invoiceId) {
            try {
                return \Crypt::decrypt($record->invoice_id) == $invoiceId;
            } catch (\Exception $e) {
                return false;
            }
        });
    }

    public function updateSubStage(array $data, $id, $user)
    {
        $form_json = "";
        $fees = NULL;
        $form_id = NULL;
        $folder_id = NULL;
        if($data["stage_type"] == "fill-form"){
            $form_id = $data["form_id"];
            $form = Forms::where('id',$data["form_id"])->first();
            $form_json = $form ? $form->fg_field_json : null;
        }
        else if($data["stage_type"] == "case-document"){
            $folder_id = json_encode($data["folder"]);
        }else if($data["stage_type"] == "payment"){
            $fees = $data["fees"];
        }
        $object = CaseSubStages::where('unique_id',$id)->first();
        if (!$object) return ["status" => false, "message" => ["id" => "SubStage not found"]];
        $invoice = Invoice::where('reference_id',$object->id)->first();
        $stage_id = $object->stage_id;
        $stage = CaseStages::where("id",$stage_id)->first();
        $case_id = $stage ? $stage->case_id : null;
        $case = $case_id ? CaseWithProfessionals::where('id',$case_id)->first() : null;
        $object->stage_id = $stage_id;
        $object->name = $data["name"];
        $object->stage_type = $data["stage_type"];
        $object->client_id = $case ? $case->client_id : null;
        $object->user_id = $user->id;
        $object->form_json = $form_json;
        $object->fees = $fees;
        $object->description = $data["description"] ?? null;
        $object->form_id = $form_id;
        $object->folder_id = $folder_id;
        $object->save();
        if($object->stage_type == "payment"){
            if ($invoice) {
                Invoice::where('reference_id',$object->id)->update(['total_amount' => $data["fees"]]);
                InvoiceItem::where('invoice_id',$invoice->id)->update(['particular' => $data["payment_description"]]);
            }
        }else{
            if ($invoice) {
                Invoice::where('reference_id',$object->id)->delete();
                InvoiceItem::where('invoice_id',$invoice->id)->delete();
                $invoiceId = $invoice->id;
                $record = $this->findPaymentLinkParameterByInvoiceId($invoiceId, $user->id);
                if ($record) {
                    $record->delete();
                }
            }
        }
        return ["status" => true, "message" => "Record updated successfully"];
    }

    public function deleteSubStage($id)
    {
        $sub_stages = CaseSubStages::where('unique_id',$id)->first();
        if (!$sub_stages) return false;
        if($sub_stages->stage_type == "payment"){
            $invoice = Invoice::where('reference_id',$sub_stages->id)->first();
            if ($invoice) {
                InvoiceItem::where('invoice_id',$invoice->id)->delete();
                $invoice->delete();
                $invoiceId = $invoice->id;
                $record = $this->findPaymentLinkParameterByInvoiceId($invoiceId, $sub_stages->added_by);
                if ($record) {
                    $record->delete();
                }
            }
        }
        CaseSubStages::deleteRecord($sub_stages->id);
        return true;
    }

    public function markSubStageAsComplete($id)
    {
        $sub_stage = CaseSubStages::where('unique_id',$id)->first();
        if (!$sub_stage) return ["status" => false, "message" => "SubStage not found"];
        $nextSubStage = CaseSubStages::where('id','>',$sub_stage->id)->where('status','!=','complete')->first();
        if(!empty($nextSubStage)){
            $nextSubStage->status = 'in-progress';
            $nextSubStage->save();
        }
        CaseSubStages::where('unique_id',$id)->update(['status' => 'complete']);
        return ["status" => true, "message" => "Record updated successfully"];
    }

    public function updateSubStageSorting(array $subStageIds)
    {
        if (is_array($subStageIds)) {
            foreach ($subStageIds as $index => $id) {
                CaseSubStages::where('unique_id', $id)->update(['sort_order' => $index + 1]);
            }
            return ['status' => 'success', 'message' => 'Order updated successfully'];
        }
        return ['status' => 'error', 'message' => 'Invalid data'];
    }

    private function generateSignature($params)
    {
        ksort($params); // Sort parameters
        $string = http_build_query($params);
        return hash_hmac('sha256', $string, apiKeys('STRIPE_SECRET'));
    }

    // --- Helper Methods ---
    private function getStageByUniqueId($stage_id) {
        return CaseStages::where('unique_id', $stage_id)->first();
    }

    private function getCaseById($case_id) {
        return CaseWithProfessionals::where('id', $case_id)->first();
    }

    private function getUserById($user_id) {
        return User::where('id', $user_id)->first();
    }

    private function getFormJson($form_id) {
        $form = Forms::where('id', $form_id)->first();
        return $form ? $form->fg_field_json : null;
    }

    private function deleteInvoiceAndPaymentLink($invoice) {
        if ($invoice) {
            InvoiceItem::where('invoice_id', $invoice->id)->delete();
            $invoice->delete();
            $invoiceId = $invoice->id;
            $record = PaymentLinkParameter::get()->first(function ($record) use ($invoiceId) {
                return \Crypt::decrypt($record->invoice_id) == $invoiceId;
            });
            if ($record) {
                $record->delete();
            }
        }
    }

    private function getNextSortOrder($stage_id, $case_id) {
        $caseSubStages = CaseSubStages::where('stage_id', $stage_id)
            ->where('case_id', $case_id)
            ->orderBy('id', 'desc')
            ->first();
        return $caseSubStages ? $caseSubStages->sort_order + 1 : 1;
    }

    private function createInvoice($data, $case, $stage, $object, $user) {
        $latest = Invoice::latest()->first();
        $userModel = $this->getUserById($case->client_id);
        $invoice = new Invoice();
        $invoice->unique_id = randomNumber();
        $invoice->invoice_number = $latest ? $latest->invoice_number + 1 : 1;
        $invoice->tax = 0;
        $invoice->sub_total = $data["fees"];
        $invoice->total_amount = $data["fees"];
        $invoice->currency = 'CAD';
        $invoice->user_id = $userModel->id;
        $invoice->first_name = $userModel->first_name;
        $invoice->last_name = $userModel->last_name;
        $invoice->email = $userModel->email;
        $invoice->country_code = $userModel->country_code;
        $invoice->phone_no = $userModel->phone_no;
        $invoice->invoice_type = 'sub-stages-fees';
        $invoice->reference_id = $object->id;
        $invoice->payment_status = 'pending';
        $invoice->added_by = $user->id;
        $invoice->invoice_date = date('Y-m-d');
        $invoice->due_date = date('Y-m-d', strtotime('+1 week'));
        $invoice->notes = "Invoice Created for Case:" . $case->case_title . ' Stage:' . $stage->name . ' Sub Stage:' . $object->name;
        $invoice->discount = 0;
        $invoice->save();
        return $invoice;
    }
} 