<?php

namespace App\Services;

use App\Models\CaseDocuments;
use App\Models\CaseWithProfessionals;
use App\Models\DocumentsFolder;
use App\Models\CaseFolders;
use App\Models\CaseEncryptedDocument;
use App\Models\User;
use App\Models\CaseDocumentComment;
use App\Models\CaseDocumentCommentRead;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use ZipArchive;
use Carbon\Carbon;

class CaseDocumentService
{
    /**
     * Handle document upload logic.
     */
    public function uploadDocument($file, $case_id)
    {
        try {
            if (!$file || !$case_id) {
                return false;
            }
            $fileName = $file->getClientOriginalName();
            $newName = mt_rand(1, 99999) . "-" . $fileName;
            $uploadPath = caseDocumentsDir($case_id);
            if ($file->move($uploadPath, $newName)) {
                $res = awsFileUpload(config('awsfilepath.cases') . "/" . $case_id . '/' . $newName, $uploadPath . '/' . $newName);
                unlink($uploadPath . '/' . $newName);
                return $newName;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Save a new document record and handle file upload.
     */
    public function saveDocument(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required',
                'folder_id' => 'required',
                'case_id' => 'required',
            ]);
            if ($validator->fails()) {
                $errorMessages = $validator->errors()->toArray();
                $formattedErrors = '';
                foreach ($errorMessages as $field => $errors) {
                    $formattedErrors .= $errors[0] . "<br>";
                }
                return [
                    'status' => false,
                    'error_type' => 'validation',
                    'message' => $formattedErrors,
                    'code' => 400
                ];
            }
            $file = $request->file;
            if (!$file) {
                DB::rollBack();
                return [
                    'status' => false,
                    'error_type' => 'error',
                    'message' => 'Please select file for upload',
                    'code' => 400
                ];
            }
            $newName = $this->uploadDocument($file, $request->case_id);
            if (!$newName) {
                DB::rollBack();
                return [
                    'status' => false,
                    'error_type' => 'error',
                    'message' => 'File upload failed',
                    'code' => 400
                ];
            }
            $case = CaseWithProfessionals::where('unique_id', $request->case_id)->first();
            if (!$case) {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => 'Case not found',
                    'code' => 404
                ];
            }
            $folder = $request->type == 'default'
                ? DocumentsFolder::where('unique_id', $request->folder_id)->first()
                : CaseFolders::where('unique_id', $request->folder_id)->first();
            if (!$folder) {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => 'Folder not found',
                    'code' => 404
                ];
            }
            $case_document = new CaseDocuments;
            $case_document->unique_id = randomNumber();
            $case_document->case_id = $case->id;
            $case_document->folder_id = $folder->id;
            $case_document->document_type = $request->type;
            $case_document->user_id = Auth::user()->id;
            $case_document->file_name = $newName;
            $case_document->added_by = Auth::user()->id;
            $case_document->save();
            DB::commit();
            return [
                'status' => true,
                'message' => 'Record file uploaded successfully',
                'code' => 200
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'code' => 400
            ];
        }
    }

    /**
     * Delete a document and its files.
     */
    public function deleteDocument($unique_id)
    {
        DB::beginTransaction();
        try {
            $case_document = CaseDocuments::where('unique_id', $unique_id)->first();
            if (!$case_document) {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => 'Document not found.'
                ];
            }
            $case = CaseWithProfessionals::where('id', $case_document->case_id)->first();
            if (!$case) {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => 'Case not found.'
                ];
            }
            $fileNames = explode(',', $case_document->file_name);
            foreach ($fileNames as $file) {
                $file = trim($file);
                $filePath = config('awsfilepath.cases') . '/' . $case->unique_id . '/' . $file;
                awsDeleteFile($filePath);
            }
            CaseDocuments::deleteRecord($case_document->id, $case->unique_id, $case_document->file_name);
            DB::commit();
            return [
                'status' => true,
                'message' => 'Record deleted successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete multiple documents by unique_ids.
     */
    public function deleteMultipleDocuments($ids)
    {
        DB::beginTransaction();
        try {
            foreach ($ids as $unique_id) {
                $case_document = CaseDocuments::where('unique_id', $unique_id)->first();
                if ($case_document) {
                    $case = CaseWithProfessionals::where('id', $case_document->case_id)->first();
                    if ($case) {
                        $fileNames = explode(',', $case_document->file_name);
                        foreach ($fileNames as $file) {
                            $file = trim($file);
                            $filePath = config('awsfilepath.cases') . '/' . $case->unique_id . '/' . $file;
                            awsDeleteFile($filePath);
                        }
                        CaseDocuments::deleteRecord($case_document->id, $case->unique_id, $case_document->file_name);
                    }
                }
            }
            DB::commit();
            return [
                'status' => true,
                'message' => 'Records deleted successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Rename a document file.
     */
    public function renameDocument($unique_id, $oldFileName, $newName)
    {
        try {
            $case_document = CaseDocuments::where('unique_id', $unique_id)->first();
            if (!$case_document) {
                return [
                    'status' => false,
                    'message' => 'Document not found.'
                ];
            }
            $case = CaseWithProfessionals::where('id', $case_document->case_id)->first();
            if (!$case) {
                return [
                    'status' => false,
                    'message' => 'Case not found.'
                ];
            }
            $extension = pathinfo($oldFileName, PATHINFO_EXTENSION);
            $newFileName = $newName . '.' . $extension;
            $awsPath = config('awsfilepath.cases') . '/' . $case->unique_id . '/';
            $result = awsRenameFile($awsPath . $oldFileName, $awsPath . $newFileName);
            if (!$result['status']) {
                return [
                    'status' => false,
                    'message' => 'A file with the new name already exists.'
                ];
            }
            $fileNamesArray = explode(',', $case_document->file_name);
            $updatedFileNames = array_map(function ($filename) use ($oldFileName, $newFileName) {
                return trim($filename) === $oldFileName ? $newFileName : trim($filename);
            }, $fileNamesArray);
            $case_document->file_name = implode(',', $updatedFileNames);
            $case_document->save();
            return [
                'status' => true,
                'message' => 'File name updated successfully'
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Add a new folder to a case.
     */
    public function addFolder($case_id, $data)
    {
        $cases = CaseWithProfessionals::where('unique_id', $case_id)->first();
        $caseFolders = CaseFolders::where('case_id', $cases->id)->pluck('name')->toArray();
        $folders = DocumentsFolder::where('user_id', Auth::user()->id)->whereNotIn('name', $caseFolders)->orderBy('id', 'desc')->get();
        return [
            'document_folders' => $folders,
            'cases' => $cases,
            'case_id' => $case_id,
            'pageTitle' => 'Add Folder',
        ];
    }

    /**
     * Save a new folder to a case.
     */
    public function saveFolder($case_id, $data)
    {
        $validator = Validator::make($data, [
            'select_option' => 'required|in:predefined,new',
            'folder' => 'required_if:select_option,predefined',
            'name' => [
                'required_if:select_option,new',
                'unique:case_folders,name',
            ],
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->toArray();
            $errMsg = array();
            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            return [
                'status' => false,
                'message' => $errMsg
            ];
        }
        $case = CaseWithProfessionals::where('unique_id', $case_id)->first();
        $object = new CaseFolders();
        $object->case_id = $case->id;
        if ($data['select_option'] == 'predefined') {
            $object->name = $data['folder'];
        } else {
            $object->name = $data['name'];
        }
        $object->slug = str_slug($data['name'] ?? $object->name);
        $object->description = htmlentities($data['description'] ?? '');
        $object->is_hidden = $data['is_hidden'] ?? 0;
        $object->added_by = Auth::user()->id;
        $object->save();
        return [
            'status' => true,
            'message' => 'Folder added successfully'
        ];
    }

    /**
     * Update a folder's details.
     */
    public function updateFolder($id, $data)
    {
        $caseFolder = CaseFolders::where('unique_id', $id)->first();
        $validator = Validator::make($data, [
            'name' => 'required|unique:case_folders,name,' . $caseFolder->id,
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->toArray();
            $errMsg = array();
            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            return [
                'status' => false,
                'message' => $errMsg
            ];
        }
        $caseFolder->name = $data['name'];
        $caseFolder->slug = str_slug($data['name']);
        $caseFolder->description = htmlentities($data['description'] ?? '');
        $caseFolder->is_hidden = $data['is_hidden'] ?? 0;
        $caseFolder->added_by = Auth::user()->id;
        $caseFolder->save();
        return [
            'status' => true,
            'message' => 'Data Updated successfully'
        ];
    }

    /**
     * Delete a folder and its documents.
     */
    public function deleteFolder($id)
    {
        DB::beginTransaction();
        try {
            $caseFolder = CaseFolders::where('unique_id', $id)->first();
            if (!$caseFolder) {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => 'Folder not found.'
                ];
            }
            $case = CaseWithProfessionals::where('id', $caseFolder->case_id)->first();
            if (!$case) {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => 'Case not found.'
                ];
            }
            $case_documents = CaseDocuments::where('folder_id', $caseFolder->id)->get();
            if ($case_documents->isEmpty()) {
                CaseFolders::deleteRecord($caseFolder->id);
                DB::commit();
                return [
                    'status' => true,
                    'message' => 'Folder deleted successfully.'
                ];
            }
            foreach ($case_documents as $document) {
                $awsPath = config('awsfilepath.cases') . '/' . $case->unique_id . '/' . $document->file_name;
                $deleteStatus = awsDeleteFile($awsPath);
                if ($deleteStatus) {
                    $document->delete();
                    CaseDocuments::deleteRecord($case->id, $case->unique_id, $document->file_name);
                    CaseFolders::deleteRecord($caseFolder->id);
                } else {
                    DB::rollBack();
                    return [
                        'status' => false,
                        'message' => 'Error deleting file from AWS: ' . $document->file_name
                    ];
                }
            }
            DB::commit();
            return [
                'status' => true,
                'message' => 'Record deleted successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Reorder folders in a case.
     */
    public function reorderFolders($caseId, $groupId)
    {
        if (is_array($groupId)) {
            foreach ($groupId as $index => $id) {
                CaseFolders::where('unique_id', $id)->update(['sort_order' => $index + 1]);
            }
            return [
                'status' => 'success',
                'message' => 'Order updated successfully'
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Invalid data'
        ];
    }

    /**
     * Download multiple documents as a zip file.
     * (Returns zip file path and name, controller should handle response)
     */
    public function downloadMultipleDocuments($uniqueIds)
    {
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
                    $folder = $document->document_type == 'default'
                        ? DocumentsFolder::where('id', $document->folder_id)->first()
                        : CaseFolders::where('id', $document->folder_id)->first();
                    if (!$case) continue;
                    $fileKey = config('awsfilepath.cases') . '/' . $case->unique_id . '/' . $document->file_name;
                    $fileContent = awsFileDownload($fileKey, true);
                    if (!$fileContent) continue;
                    $folderName = $folder->name ?? 'other';
                    $zip->addFromString($folderName . '/' . basename($document->file_name), $fileContent);
                    $zipCreated = true;
                }
            }
            $zip->close();
        } else {
            return [
                'status' => false,
                'message' => 'Failed to create ZIP archive.'
            ];
        }
        if (!$zipCreated || !file_exists($zipFilePath)) {
            return [
                'status' => false,
                'message' => 'ZIP file could not be created or is empty.'
            ];
        }
        return [
            'status' => true,
            'zipFilePath' => $zipFilePath,
            'zipFileName' => $zipFileName
        ];
    }

    /**
     * Link/move documents to a folder.
     */
    public function linkToGroups($folder_type, $target_group, $moved_ids)
    {
        $folder = $folder_type == 'default'
            ? DocumentsFolder::where('unique_id', $target_group)->first()
            : CaseFolders::where('unique_id', $target_group)->first();
        if (!$folder) {
            return [
                'status' => false,
                'message' => 'Target folder not found.'
            ];
        }
        DB::beginTransaction();
        try {
            foreach ($moved_ids as $documentId) {
                CaseDocuments::where('unique_id', $documentId)
                    ->update([
                        'folder_id' => $folder->id,
                        'document_type' => $folder_type
                    ]);
            }
            DB::commit();
            return [
                'status' => true,
                'message' => 'Documents moved successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Reorder documents in a folder.
     */
    public function reorderDocuments($folder_type, $target_group, $moved_ids)
    {
        $folder = $folder_type == 'default'
            ? DocumentsFolder::where('unique_id', $target_group)->first()
            : CaseFolders::where('unique_id', $target_group)->first();
        if (!$folder) {
            return [
                'status' => false,
                'message' => 'Target folder not found.'
            ];
        }
        if (is_array($moved_ids) && $target_group) {
            DB::beginTransaction();
            try {
                foreach ($moved_ids as $index => $uniqueId) {
                    CaseDocuments::where('unique_id', $uniqueId)->update([
                        'folder_id' => $folder->id,
                        'sort_order' => $index + 1,
                    ]);
                }
                DB::commit();
                return [
                    'status' => true,
                    'message' => 'Documents reordered successfully'
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
        return [
            'status' => false,
            'message' => 'Invalid data'
        ];
    }

    /**
     * Encrypt documents (returns status and password, controller should handle email/view logic).
     */
    public function encryptDocuments($documentIds, $folder_name)
    {
        if (empty($documentIds)) {
            return [
                'status' => false,
                'message' => 'Please select at least one document to encrypt.'
            ];
        }
        $timestamp = time();
        $doc_ids = [];
        $case = null;
        DB::beginTransaction();
        try {
            foreach ($documentIds as $documents) {
                $record = CaseDocuments::where('unique_id', $documents)->first();
                if (!$record) continue;
                $case = CaseWithProfessionals::where('id', $record->case_id)->first();
                if (!$case) continue;
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
                $encryptedDoc->folder_name = $folder_name;
                $encryptedDoc->added_by = Auth::user()->id;
                $encryptedDoc->save();
                $documents = CaseDocuments::whereIn("id", $doc_ids)->get();
                foreach ($documents as $document) {
                    $document->is_encrypted = 1;
                    $document->case_encrypted_documents_id = $encryptedDoc->id;
                    $document->save();
                }
                DB::commit();
                return [
                    'status' => true,
                    'password' => $password,
                    'encrypted_document_id' => $encryptedDoc->id,
                    'message' => "Files are encrypted. Zip is password protected. Password has been generated."
                ];
            } else {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => "Encryption failed"
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Decrypt documents (returns status, controller should handle view/response logic).
     */
    public function decryptDocuments($documentIds, $decryptionKey)
    {
        if (empty($documentIds) || !is_array($documentIds)) {
            return [
                'status' => false,
                'message' => 'No valid documents selected.'
            ];
        }
        DB::beginTransaction();
        try {
            foreach ($documentIds as $docId) {
                $document = CaseDocuments::where('unique_id', $docId)->first();
                if (!$document) {
                    DB::rollBack();
                    return [
                        'status' => false,
                        'message' => 'Document not found.'
                    ];
                }
                $encryptedDocument = CaseEncryptedDocument::find($document->case_encrypted_documents_id);
                if (!$encryptedDocument) {
                    DB::rollBack();
                    return [
                        'status' => false,
                        'message' => 'Encrypted document record not found.'
                    ];
                }
                if (decryptVal($encryptedDocument->password) == $decryptionKey) {
                    $document->is_encrypted = 0;
                    $document->case_encrypted_documents_id = 0;
                    $document->save();
                    $remaining = CaseDocuments::where('case_encrypted_documents_id', $encryptedDocument->id)
                        ->where('is_encrypted', 1)
                        ->count();
                    $encryptedDocument->no_of_files = $remaining;
                    $encryptedDocument->save();
                } else {
                    DB::rollBack();
                    return [
                        'status' => false,
                        'message' => 'Invalid decryption key.'
                    ];
                }
            }
            DB::commit();
            return [
                'status' => true,
                'message' => 'Files have been successfully decrypted.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Add a comment to a document.
     */
    public function saveDocumentComment($data, $file = null)
    {
        if (empty($data['comment']) && !$file) {
            return [
                'status' => false,
                'message' => 'Comment or attachment is required'
            ];
        }
        DB::beginTransaction();
        try {
            $case_document = CaseDocuments::where('id', $data['document_id'])->first();
            if (!$case_document) {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => 'Document not found.'
                ];
            }
            $parent_id = 0;
            if (!empty($data['parent_id'])) {
                $parent_case_document = CaseDocumentComment::where('unique_id', $data['parent_id'])->first();
                if (!$parent_case_document) {
                    DB::rollBack();
                    return [
                        'status' => false,
                        'message' => 'Parent comment not found.'
                    ];
                }
                $parent_id = $parent_case_document->id;
            }
            $object = new CaseDocumentComment();
            $object->added_by = Auth::user()->id;
            $object->case_id = $data['case_id'];
            $object->folder_id = $data['folder_id'];
            $object->document_id = $data['document_id'];
            $object->parent_id = $parent_id;
            $object->message = $data['comment'] ?? '';
            if ($file) {
                $destinationPath = public_path('uploads/temp');
                $extension = $file->getClientOriginalExtension() ?: 'png';
                $newName = mt_rand() . "." . $extension;
                if ($file->move($destinationPath, $newName)) {
                    $sourcePath = public_path('uploads/temp/' . $newName);
                    $upload_path = caseDocumentCommentDir($case_document->unique_id);
                    $response = mediaUploadApi("upload-file", $sourcePath, $upload_path, $newName);
                    if (isset($response['status']) && $response['status'] == 'success') {
                        \File::delete($sourcePath);
                        $object->attachments = $newName;
                    } else {
                        DB::rollBack();
                        return ['status' => false, 'message' => "Failed uploading attachment. Try again."];
                    }
                } else {
                    DB::rollBack();
                    return ['status' => false, 'message' => "Some issue while upload. Try again"];
                }
            }
            $object->save();
            $readObject = new CaseDocumentCommentRead();
            $readObject->user_id = Auth::user()->id;
            $readObject->case_document_id = $object->document_id;
            $readObject->case_document_comment_id = $object->id;
            $readObject->is_read = 0;
            $readObject->save();
            DB::commit();
            return [
                'status' => true,
                'message' => 'Comment added successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Fetch comments for a document.
     */
    public function fetchDocumentComments($document_id)
    {
        $comments = CaseDocumentComment::with(['commentReply', 'commentRead'])->where('document_id', $document_id)->get();
        CaseDocumentCommentRead::where('case_document_id', $document_id)->where('user_id', '!=', Auth::user()->id)->update(['is_read' => 1]);
        return [
            'status' => true,
            'comments' => $comments
        ];
    }

    /**
     * Delete a comment from a document.
     */
    public function documentCommentDelete($comment_id, $document_id)
    {
        CaseDocumentComment::deleteRecord($comment_id, $document_id);
        return [
            'status' => true,
            'message' => 'Comment deleted successfully'
        ];
    }

    /**
     * Download a document from AWS.
     */
    public function downloadDocument($caseId, $filekey)
    {
        $filePath = config('awsfilepath.cases') . '/' . $caseId . '/' . $filekey;
        return [
            'filePath' => $filePath
        ];
    }

    /**
     * Download an encrypted zip from AWS.
     */
    public function downloadZip($zip_id)
    {
        $filePath = config('awsfilepath.encrypted_documents') . '/' . $zip_id;
        return [
            'filePath' => $filePath
        ];
    }

    /**
     * Send encryption OTP to user.
     */
    public function sendEncryptionOtp($encryptedDocumentId)
    {
        $encryptedDocument =  CaseEncryptedDocument::where('unique_id', $encryptedDocumentId)->first();
        $case = CaseWithProfessionals::where('id', $encryptedDocument->case_id)->first();
        $User = User::where('id', $encryptedDocument->added_by)->first();
        $otp_object = sendEncryptionOtp($User->email, "emails.encryption-otp-mail");
        $otpVerify  = OtpVerify::where('unique_id', $otp_object->unique_id)->first();
        $otpLocation  = json_decode($otpVerify->user_location, true);
        return [
            'otpVerify' => $otpVerify,
            'user_token' => $User->unique_id,
            'token' => $otp_object->unique_id,
            'user' => $User,
            'encryptedDocument' => $encryptedDocument,
            'timezone' => $otpLocation['timezone'] ?? 'UTC',
            'send_otp_url' => baseUrl('case-with-professionals/send-login-otp'),
            'verify_otp_url' => baseUrl('case-with-professionals/verify-encryption-login-otp'),
            'pageTitle' => 'Enter the Otp you receive on your email',
        ];
    }

    /**
     * Verify encryption OTP and send encryption key email.
     */
    public function encryptionVerifyOtp($data)
    {
        $validator = Validator::make($data, [
            'otp1' => 'required|numeric|digits:1',
            'otp2' => 'required|numeric|digits:1',
            'otp3' => 'required|numeric|digits:1',
            'otp4' => 'required|numeric|digits:1',
            'otp5' => 'required|numeric|digits:1',
            'otp6' => 'required|numeric|digits:1',
        ], [
            'otp6.required' => 'The OTP field is required.',
        ]);
        if ($validator->fails()) {
            return [
                'status' => false,
                'error_type' => 'validation',
                'message' => ['otp6' => 'The OTP field is required.']
            ];
        }
        $otpInputs = [
            $data['otp1'], $data['otp2'], $data['otp3'], $data['otp4'], $data['otp5'], $data['otp6']
        ];
        $otp = implode('', $otpInputs);
        $verify_otp = OtpVerify::where("email", $data["email"])
            ->where("unique_id", $data["otp_token"])
            ->where("otp", $otp)
            ->first();
        $encryptedDocument =  CaseEncryptedDocument::where('id', $data["case_encrypted_id"])->first();
        if (!empty($verify_otp)) {
            $expiry_time = $verify_otp->otp_expiry_time;
            $current_time = Carbon::now();
            if ($current_time < $expiry_time) {
                if ($verify_otp->otp == $otp) {
                    $user = User::where('id', $encryptedDocument->added_by)->first();
                    $mailData = [
                        'user' => $user,
                        'password' => decryptVal($encryptedDocument->password),
                        'caseTitle' => $encryptedDocument->case->case_title ?? ''
                    ];
                    $view = \View::make('emails.encryption-key', $mailData);
                    $message = $view->render();
                    $parameter = [
                        'to' => $user->email,
                        'to_name' => $user->first_name . ' ' . $user->last_name,
                        'message' => $message,
                        'subject' => siteSetting("company_name") . ": Your Encryption Key for Documents",
                        'view' => 'emails.encryption-key',
                        'data' => $mailData,
                    ];
                    sendMail($parameter);
                    return [
                        'status' => true,
                        'success' => "Email Send with Encryption Key Successfully",
                        'message' => "Email Send with Encryption Key Successfully"
                    ];
                } else {
                    return [
                        'status' => false,
                        'message' => 'Login token invalid'
                    ];
                }
            } else {
                return [
                    'status' => false,
                    'message' => 'Otp has been expired'
                ];
            }
        } else {
            return [
                'status' => false,
                'message' => 'Otp is not valid'
            ];
        }
    }

    /**
     * Resend encryption OTP.
     */
    public function loginSendOtp($token, $type = null)
    {
        $verifyOtp = OtpVerify::where('unique_id', $token)->first();
        if ($verifyOtp) {
            $attempt = $verifyOtp->resend_attempt;
            if ($attempt < 2) {
                $otp_object = sendEncryptionOtp($verifyOtp->email, "emails.encryption-otp-mail", $type);
                return [
                    'status' => true,
                    'message' => 'OTP sent successfully.'
                ];
            } else {
                TempUser::where("email", $verifyOtp->email)->delete();
                OtpVerify::where("email", $verifyOtp->email)->delete();
                return [
                    'status' => false,
                    'redirect_back' => url('login'),
                    'message' => 'Maximum OTP verification attempts reached'
                ];
            }
        } else {
            return [
                'status' => false,
                'message' => 'OTP record not found.'
            ];
        }
    }

    /**
     * Get file preview data (returns preview URL and type info).
     */
    public function getFilePreviewData($case_id, $folder_id, $document_id)
    {
        $case_document = CaseDocuments::where('unique_id', $document_id)->first();
        $case_folder = CaseFolders::where("unique_id", $folder_id)->first();
        $case_documents = CaseDocuments::where("folder_id", $case_folder->id)->get();
        $fileKey = $case_document->file_name;
        $fileKey = urldecode($fileKey);
        $extension = strtolower(pathinfo($fileKey, PATHINFO_EXTENSION));
        $filename = basename($fileKey);
        $previewableTypes = [
            'images' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'],
            'documents' => ['pdf'],
            'videos' => ['mp4', 'webm', 'ogg'],
            'audio' => ['mp3', 'wav', 'ogg'],
            'text' => ['txt', 'csv', 'log', 'json', 'xml', 'html', 'css', 'js'],
            'office' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']
        ];
        $isPreviewable = false;
        $fileType = '';
        foreach ($previewableTypes as $type => $extensions) {
            if (in_array($extension, $extensions)) {
                $isPreviewable = true;
                $fileType = $type;
                break;
            }
        }
        $file = config('awsfilepath.cases') . "/" . $case_id . "/" . $fileKey;
        $expiration = '+2 hours';
        $previewUrl = awsFilePreviewUrl($file, $extension, $expiration, true);
        $viewerUrls = [];
        if ($fileType === 'office') {
            $viewerUrls['office'] = 'https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode($previewUrl);
            $viewerUrls['google'] = 'https://docs.google.com/viewer?url=' . urlencode($previewUrl) . '&embedded=true';
        }
        return [
            'fileKey' => $fileKey,
            'filename' => $filename,
            'extension' => $extension,
            'previewUrl' => $previewUrl,
            'viewerUrls' => $viewerUrls,
            'fileType' => $fileType,
            'isPreviewable' => $isPreviewable,
            'case_documents' => $case_documents
        ];
    }

    /**
     * Get case document preview data (returns preview URLs and file info array).
     */
    public function getCaseDocumentPreviewData($case_id, $folder_id, $document_id)
    {
        $case = CaseWithProfessionals::where("unique_id", $case_id)->first();
        $case_document = CaseDocuments::where('unique_id', $document_id)->first();
        $case_folder = CaseFolders::where("unique_id", $folder_id)->first();
        $case_documents = CaseDocuments::where("folder_id", $case_folder->id)->get();
        $fileKey = $case_document->file_name;
        $fileKey = urldecode($fileKey);
        $extension = strtolower(pathinfo($fileKey, PATHINFO_EXTENSION));
        $filename = basename($fileKey);
        $previewableTypes = [
            'images' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'],
            'documents' => ['pdf'],
            'videos' => ['mp4', 'webm', 'ogg'],
            'audio' => ['mp3', 'wav', 'ogg'],
            'text' => ['txt', 'csv', 'log', 'json', 'xml', 'html', 'css', 'js'],
            'office' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']
        ];
        $isPreviewable = false;
        $fileType = '';
        foreach ($previewableTypes as $type => $extensions) {
            if (in_array($extension, $extensions)) {
                $isPreviewable = true;
                $fileType = $type;
                break;
            }
        }
        $file = config('awsfilepath.cases') . "/" . $case_id . "/" . $fileKey;
        $expiration = '+2 hours';
        $previewUrl = awsFilePreviewUrl($file, $extension, $expiration, true);
        $files_arr = array();
        $current_file_index = 0;
        foreach($case_documents as $index => $document) {
            $temp = array();
            $docExtension = strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION));
            $docFilePath = config('awsfilepath.cases') . "/" . $case_id . "/" . $document->file_name;
            $doc_url = awsFilePreviewUrl($docFilePath, $docExtension, $expiration, true);
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff'];
            $isImage = in_array($docExtension, $imageExtensions);
            $temp['id'] = $document->id;
            $temp['unique_id'] = $document->unique_id;
            $temp['case_id'] = $case->id;
            $temp['folder_id'] = $document->folder_id;
            $temp['name'] = $document->file_name;
            $temp['type'] = ($isImage) ? 'image' : $docExtension;
            $temp['size'] = 'N/A';
            $temp['url'] = $doc_url;
            $temp['download_url'] = baseUrl('case-with-professionals/download-documents?case_id='.$case_id."&file=".$document->file_name);
            if($docExtension == 'pdf') {
                try {
                    $file_data = awsFileEncoded($docFilePath);
                    $pdf_thumb = mediaUploadBaseCode("pdf-thumbnail", $file_data['data'], 'pdf-images', $document->file_name);
                    $temp['thumbnail'] = $pdf_thumb['thumbnail_base64'] ?? null;
                } catch (\Exception $e) {
                    $temp['thumbnail'] = null;
                }
            } elseif ($isImage) {
                $temp['thumbnail'] = $doc_url;
            } else {
                $temp['thumbnail'] = null;
            }
            $temp['comments'] = array();
            $files_arr[] = $temp;
            if ($document->unique_id == $document_id) {
                $current_file_index = $index;
            }
        }
        return [
            'fileKey' => $fileKey,
            'filename' => $filename,
            'extension' => $extension,
            'previewUrl' => $previewUrl,
            'fileType' => $fileType,
            'case_id' => $case_id,
            'case_documents' => $case_documents,
            'files_arr' => json_encode($files_arr),
            'current_file_index' => $current_file_index
        ];
    }
} 