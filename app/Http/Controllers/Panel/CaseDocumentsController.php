<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\CaseDocumentComment;
use Illuminate\Http\Request;
use App\Models\ProfessionalServices;
use View;
use App\Models\Cases;
use Illuminate\Support\Facades\Validator;
use App\Models\CaseComment;
use App\Models\CaseWithProfessionals;
use App\Models\Forms;
use App\Models\ProfessionalCaseRequests;
use App\Models\User;
use App\Models\ProfessionalRequestNote;
use App\Models\DocumentsFolder;
use App\Models\CaseFolders;
use App\Models\CaseDocuments;
use App\Models\CaseEncryptedDocument;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\OtpVerify;
use App\Models\TempUser;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\StaffCases;
use Illuminate\Validation\Rule;
use App\Models\CaseDocumentCommentRead;
use App\Services\CaseDocumentService;

class CaseDocumentsController extends Controller
{
    protected $caseDocumentService;

    public function __construct(CaseDocumentService $caseDocumentService)
    {
        $this->caseDocumentService = $caseDocumentService;
    }

    public function documents($case_id)
    {
        $case_record = CaseWithProfessionals::where("unique_id", $case_id)
            ->with(['services', 'subServices', 'subServicesTypes', 'userAdded'])->first();

        $staff_cases = StaffCases::where('case_id',$case_record)->where('staff_id',auth()->user()->id)->first();
      
        // $viewData['document_folders'] = DocumentsFolder::whereIn('id', explode(',', $case_record->subServicesTypes->document_folders))->where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();
        

        $viewData['other_document_folders'] = CaseFolders::where('case_id', $case_record->id)
            ->where(function ($query) use ($case_record) {
                $query->where('added_by', $case_record->professional_id)
                    ->orWhere('added_by', $case_record->client_id);
            })
            ->orderBy('sort_order', 'asc')
            ->get();
        $viewData['pageTitle'] = "My Cases List";
        $viewData['case_record'] = $case_record;
        $viewData['case_id'] = $case_id;

        return view('admin-panel.08-cases.case-with-professionals.documents.document-folders', $viewData);
    }

    public function documentsAddFolder($case_id)
    {
        $data = $this->caseDocumentService->addFolder($case_id, request()->all());
        $view = view("admin-panel.08-cases.case-with-professionals.documents.add-folder", $data);
        $response['contents'] = $view->render();
        $response['status'] = true;
        return response()->json($response);
    }


    public function documentsSaveFolder(Request $request, $case_id)
    {
        $result = $this->caseDocumentService->saveFolder($case_id, $request->all());
        return response()->json($result);
    }

    public function showDocuments($type, $case_id, $folder_id)
    {

        $case = CaseWithProfessionals::where('unique_id', $case_id)->first();

        if ($type == 'default') {
            $folder = DocumentsFolder::where('unique_id', $folder_id)->where('added_by', auth()->user()->id)->first();
        } else {
            $folder = CaseFolders::where('unique_id', $folder_id)->where('added_by', auth()->user()->id)->orderBy('sort_order', 'asc')->first();
        }

        $case_documents = CaseDocuments::where('case_id', $case->id)->where('folder_id', $folder->id)->whereIn('user_id', [$case->client_id, auth()->user()->id])->orderBy('id', 'desc')->get();

        $viewData['pageTitle'] = "";
        $viewData['type'] = $type;
        $viewData['case_id'] = $case_id;
        $viewData['folder_id'] = $folder_id;
        $viewData['case_documents'] = $case_documents;
        return view('admin-panel.08-cases.case-with-professionals.documents.documents', $viewData);
    }

    public function uploadDocument(Request $request)
    {
        $file = $request->file;
        $case_id = $request->case_id;
        $newName = $this->caseDocumentService->uploadDocument($file, $case_id);
        $response['status'] = true;
        $response['filename'] = $newName;
        $response['message'] = "Record added successfully";
        return response()->json($response);
    }

     public function saveDocument(Request $request)
    {
        $result = $this->caseDocumentService->saveDocument($request);
        return response()->json($result, $result['code'] ?? 200);
    }

    public function deleteDocument($id)
    {
        $result = $this->caseDocumentService->deleteDocument($id);
        return redirect()->back()->with("success", $result['message']);
    }

    public function downloadDocument(Request $request)
    {
        $filekey = $request->file;
        $caseId = $request->case_id;
        $result = $this->caseDocumentService->downloadDocument($caseId, $filekey);
        return awsFileDownload($result['filePath']);
    }

    public function deleteMultipleDocuments(Request $request)
    {
        $ids = $request->input("file_ids");
        $result = $this->caseDocumentService->deleteMultipleDocuments($ids);
        \Session::flash('success', $result['message']);
        return response()->json($result);
    }


    public function renameDocument($id, Request $request)
    {

        $case_document = CaseDocuments::where('unique_id', $id)->first();

        $viewData['record'] = $case_document;
        $viewData['old_file_name'] =  $request->query('old_file_name');
        $viewData['pageTitle'] = 'Rename File Name';
        $view = View::make('admin-panel.08-cases.case-with-professionals.documents.rename-file-name', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
    }

    public function saveRenameDocument(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        $oldFileName = trim($request->input('old_file_name'));
        $newFileName = $request->input('name');
        $result = $this->caseDocumentService->renameDocument($id, $oldFileName, $newFileName);
        $status = $result['status'] ? 200 : 400;
        return response()->json($result, $status);
    }

    public function deleteDocumentFolder(Request $request, $id)
    {

        $caseFolder = CaseFolders::where('unique_id', $id)->first();
        $case = CaseWithProfessionals::where('id', $caseFolder->case_id)->first();
        $case_documents = CaseDocuments::where('folder_id', $caseFolder->id)->get();

        if ($case_documents->isEmpty()) {
            CaseFolders::deleteRecord($caseFolder->id);
            return redirect()->back()->with("success", "folder deleted successfully.");
        }

        foreach ($case_documents as $document) {
            $awsPath = config('awsfilepath.cases') . '/' . $case->unique_id . '/' . $document->file_name;
            $deleteStatus = awsDeleteFile($awsPath);
            if ($deleteStatus) {
                $document->delete();
                CaseDocuments::deleteRecord($case->id, $case->unique_id, $document->file_name);
                CaseFolders::deleteRecord($caseFolder->id);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Error deleting file from AWS: ' . $document->file_name
                ]);
            }
        }
        return redirect()->back()->with("success", "Record deleted successfully");
    }

    public function documentsEditFolder($id)
    {
        $viewData['pageTitle'] = "Edit Folder";
        $record = CaseFolders::where('unique_id', $id)->first();
        $viewData['case_id'] = $record->case_id;
        $viewData['record'] = $record;
        $view = view("admin-panel.08-cases.case-with-professionals.documents.edit-folder", $viewData);
        $response['contents'] = $view->render();
        $response['status'] = true;
        return response()->json($response);
    }


    public function documentsUpdateFolder(Request $request, $id)
    {
        $result = $this->caseDocumentService->updateFolder($id, $request->all());
        return response()->json($result);
    }


    public function   reorderFolders(Request $request)
    {
        $result = $this->caseDocumentService->reorderFolders($request->caseId, $request->groupId);
        return response()->json($result);
    }

    public function downloadMultipleDocument(Request $request)
    {
        $uniqueIds = explode(",", $request->input("files"));
        $uploadDir = public_path('uploads/cases');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $zipFileName = randomNumber(10) . ".zip";
        $zipFilePath = $uploadDir . '/' . $zipFileName;

        $zip = new ZipArchive;
        $zipCreated = false;

        if ($zip->open($zipFilePath, ZipArchive::CREATE) === true) {
            foreach ($uniqueIds as $uniqueId) {

                $documents = CaseDocuments::where('unique_id', $uniqueId)->get();

                foreach ($documents as $document) {
                    $case = CaseWithProfessionals::where('id', $document->case_id)->first();
                    if ($document->document_type == 'default') {
                        $folder = DocumentsFolder::where('id', $document->folder_id)->first();
                    } else {
                        $folder = CaseFolders::where('id', $document->folder_id)->first();
                    }

                    if (!$case) {
                        \Log::warning("Case not found for document");
                        continue;
                    }
                    $fileKey = config('awsfilepath.cases') . '/' . $case->unique_id . '/' . $document->file_name;
                    $fileContent = awsFileDownload($fileKey, true);
                    if (!$fileContent) {
                        \Log::warning("File content not found for: " . $fileKey);
                        continue;
                    }
                    $folder = $folder->name ?? 'other';
                    $zip->addFromString($folder . '/' . basename($document->file_name), $fileContent);
                    $zipCreated = true;
                }
            }

            $zip->close();
        } else {
            return response()->json(['error' => 'Failed to create ZIP archive.'], 500);
        }

        if (!$zipCreated || !file_exists($zipFilePath)) {
            return response()->json(['error' => 'ZIP file could not be created or is empty.'], 500);
        }

        if (ob_get_length()) {
            ob_end_clean();
        }
        $result = $this->caseDocumentService->downloadMultipleDocuments($uniqueIds);
        if ($result['status']) {
            return response()->download($result['zipFilePath'], $result['zipFileName'])->deleteFileAfterSend(true);
        } else {
            return response()->json(['error' => $result['message']], 500);
        }
    }

    public function linkToGroups(Request $request)
    {
        $result = $this->caseDocumentService->linkToGroups($request->folder_type, $request->target_group, $request->input('moved_ids'));
        return response()->json($result);
    }

    public function encryptDocuments(Request $request)
    {

        // $documentIds = $request->input('documents', []);
        $documentIds = explode(',', $request->input('documents'));
        if (empty($documentIds)) {
            return response()->json(['status' => false, 'message' => 'Please select at least one document to encrypt.']);
        }

        $timestamp = time();
        $doc_ids = [];
        $case = null;

        foreach ($documentIds as $documents) {
            $record = CaseDocuments::where('unique_id', $documents)->first();
            $case = CaseWithProfessionals::where('id', $record->case_id)->first();
            if (!$record) continue;
            $doc_ids[] = $record->id;
            $filenames = explode(',', $record->file_name);
            foreach ($filenames as $file) {
                awsFileDownloadFolder(config('awsfilepath.cases') . '/' . $case->unique_id . '/' . $file, $timestamp);
            }
        }

        $folderToZip = storage_path("app/public/aws-files/encrypted/{$timestamp}");
        $zipFilePath = storage_path("app/public/aws-files/encrypted/{$timestamp}.zip");

        if (!file_exists(dirname($zipFilePath))) {
            mkdir(dirname($zipFilePath), 0777, true);
        }

        $password = randomString(12);

        if (zipAndEncryptFolder($folderToZip, $zipFilePath, $password)) {

            awsFileUpload(config('awsfilepath.encrypted_documents') . "/{$timestamp}.zip", $zipFilePath);
            array_map('unlink', glob(pattern: "$folderToZip/*.*"));
            if (is_dir($folderToZip)) {
                rmdir($folderToZip);
            }

            if (file_exists($zipFilePath)) {
                unlink($zipFilePath);
            }

            $encryptedDoc = new CaseEncryptedDocument();
            $encryptedDoc->case_id   = $case->id ?? '';
            $encryptedDoc->folder_id = "{$timestamp}.zip";
            $encryptedDoc->password  = encryptVal($password) ?? '';
            $encryptedDoc->no_of_files = count($documentIds);
            $encryptedDoc->folder_name = $request->folder_name;
            $encryptedDoc->added_by = \Auth::user()->id;
            $encryptedDoc->save();

            $documents = CaseDocuments::whereIn("id", $doc_ids)->get();

            foreach ($documents as $document) {
                $document->is_encrypted = 1;
                $document->case_encrypted_documents_id = $encryptedDoc->id;
                $document->save();
            }
            $user = User::where('id', $record->added_by)->first();

            $mailData = [
                'user' => $user,
                'password' => $password,
                'caseTitle' => $case->case_title ?? ''
            ];

            $view = \View::make('emails.encryption-key', $mailData);
            $message = $view->render();

            $parameter = [
                'to' => $user->email,
                'to_name' => $user->first_name . ' ' . $user->last_name,
                'message' => $message,
                'subject' => siteSetting("company_name") . ": Your Password for Documents",
                'view' => 'emails.encryption-key',
                'data' => $mailData,
            ];

            sendMail($parameter);

            return response()->json([
                'status' => true,
                'message' => "Files are encrypted.Zip is password protected Password has been sent to your email."
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Encryption failed"
            ]);
        }
    }


    public function encryptedDocuments($case_id)
    {
        $case_record = CaseWithProfessionals::where("unique_id", $case_id)
            ->with(['services', 'subServices', 'subServicesTypes', 'userAdded'])->first();
                
     
        $viewData['encrypted_document_folders'] = CaseEncryptedDocument::where('case_id', $case_record->id)->where('added_by', auth()->user()->id)->orderBy('id', 'desc')->get();


        $viewData['pageTitle'] = "Encrypted Documents List";
        $viewData['case_record'] = $case_record;
        $viewData['case_id'] = $case_id;

        return view('admin-panel.08-cases.case-with-professionals.encrypted-documents.document-folders', $viewData);
    }

    public function decryptDocumentPopup(Request $request)
    {

        $documentId = $request->input('document_ids', []);

        if (empty($documentId)) {
            return response()->json(['status' => false, 'message' => 'Please select at least one document to encrypt.']);
        }

        $documentIds = json_decode($documentId, true);

        $encryptedDocument = null;
        foreach ($documentIds as $docId) {
            $document = CaseDocuments::where('unique_id', $docId)->first();

            $encryptedDocument =  CaseEncryptedDocument::where('id', $document->case_encrypted_documents_id)->first();
        }

        $viewData['documentIds'] = $documentIds;
        $viewData['encryptedDocument'] = $encryptedDocument;

        $viewData['pageTitle'] = 'Enter Password';
        $view = View::make('admin-panel.08-cases.case-with-professionals.encrypted-documents.decrypt-document-form', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
    }


    public function showEncryptedDocuments($type, $case_id, $folder_id)
    {

        $case = CaseWithProfessionals::where('unique_id', $case_id)->first();

        if ($type == 'default') {
            $folder = DocumentsFolder::where('unique_id', $folder_id)->where('added_by', auth()->user()->id)->first();
        } else {
            $folder = CaseFolders::where('unique_id', $folder_id)->where('added_by', auth()->user()->id)->first();
        }

        $case_documents = CaseDocuments::where('case_id', $case->id)->where('folder_id', $folder->id)->whereIn('user_id', [$case->client_id, auth()->user()->id])->orderBy('id', 'desc')->get();

        $viewData['pageTitle'] = "";
        $viewData['type'] = $type;
        $viewData['case_id'] = $case_id;
        $viewData['folder_id'] = $folder_id;
        $viewData['case_documents'] = $case_documents;
        return view('admin-panel.08-cases.case-with-professionals.encrypted-documents.documents', $viewData);
    }

    public function decryptDocuments(Request $request)
    {
        $documentIds = explode(',', $request->input('documentIds'));
        $decryptionKey = $request->input('decryption_key');
        $result = $this->caseDocumentService->decryptDocuments($documentIds, $decryptionKey);
        return response()->json($result);
    }

    public function downloadZip(Request $request)
    {
        $filekey = $request->zip_id;
        $result = $this->caseDocumentService->downloadZip($filekey);
        return awsFileDownload($result['filePath']);
    }

    public function sendEncryptionOtp($id, Request $request)
    {
        $data = $this->caseDocumentService->sendEncryptionOtp($id);
        $view = View::make('admin-panel.08-cases.case-with-professionals.encrypted-documents.document-verify-otp', $data);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
    }

    public function encryptionVerifyOtp(Request $request)
    {
        $result = $this->caseDocumentService->encryptionVerifyOtp($request->all());
        return response()->json($result);
    }

    public function loginSendOtp(Request $request)
    {
        $token = $request->input('token');
        $type = $request->input('type');
        $result = $this->caseDocumentService->loginSendOtp($token, $type);
        return response()->json($result);
    }

    public function showFilePreview($case_id, $folder_id, $document_id)
    {
        try {
            $data = $this->caseDocumentService->getFilePreviewData($case_id, $folder_id, $document_id);
            if (!$data['isPreviewable']) {
                return awsFileDownload($data['fileKey']);
            }
            if (!$data['previewUrl']) {
                return redirect()->back()->with('error', 'Unable to generate preview URL');
            }
            $data['pageTitle'] = 'Preview';
            $view =  view('admin-panel.08-cases.case-with-professionals.documents.file-preview', $data);
            $contents = $view->render();
            $response['status'] = true;
            $response['contents'] = $contents;
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

    public function showCaseDocumentPreview($case_id, $folder_id, $document_id)
    {
        try {
            $data = $this->caseDocumentService->getCaseDocumentPreviewData($case_id, $folder_id, $document_id);
            $data['pageTitle'] = 'Preview';
            $view = view('admin-panel.08-cases.case-with-professionals.documents.case-document-preview', $data);
            $contents = $view->render();
            $response['status'] = true;
            $response['contents'] = $contents;
            $response['files_arr'] = json_decode($data['files_arr'], true);
            $response['current_file_index'] = $data['current_file_index'];
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

    public function saveDocumentComment(Request $request){
        $result = $this->caseDocumentService->saveDocumentComment($request->all(), $request->file('attachment'));
        return response()->json($result);
    }

    public function fetchDocumentComments(Request $request){
        $result = $this->caseDocumentService->fetchDocumentComments($request->document_id);
        $view = view("admin-panel.08-cases.case-with-professionals.documents.document-comments", ['comments' => $result['comments']])->render();
            $response['contents'] = $view;
            $response['status'] = true;
            return response()->json($response);
    }

    public function documentCommentDelete($comment_id,$document_id){
        $result = $this->caseDocumentService->documentCommentDelete($comment_id, $document_id);
        return response()->json($result);
    }
}
