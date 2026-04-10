<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentsFolder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use View;

class DocumentsFolderController extends Controller
{
 
    public function __construct()
    {
        // Constructor method for initializing middleware or other components if needed
    }

    /**
     * Display the list of Role.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $viewData['pageTitle'] = "Documents";
        return view('admin-panel.08-cases.document-folders.lists', $viewData);
    }

    /**
     * Get the list of Country with pagination and search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAjaxList(Request $request)
    {
      
        $search = $request->input("search");
        $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
        $sortDirection = $request->input('sort_direction', 'asc');

        try {
            $records = DocumentsFolder::where(function ($query) use ($search) {
                    if ($search != '') {
                        $query->where("name", "LIKE", "%" . $search . "%");
                    }
                })
                ->visibleToUser(auth()->user()->id)
                   ->orderBy($sortColumn, $sortDirection)
                ->paginate();

            $viewData['records'] = $records;
            $view = View::make('admin-panel.08-cases.document-folders.ajax-list', $viewData);
            $contents = $view->render();
            $response['contents'] = $contents;
            $response['last_page'] = $records->lastPage();
            $response['current_page'] = $records->currentPage();
            $response['total_records'] = $records->total();
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error_type' => 'exception',
                'message' => 'An error occurred while fetching the records.'
            ]);
        }
    }

    /**
     * Show the form for creating a new Document.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add()
    {
        $viewData['pageTitle'] = 'Add Document Folder';
        $view = view('admin-panel.08-cases.document-folders.add', $viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;
        return response()->json($response);

    }

    /**
     * Store a newly created role in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|input_string|max:255'
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $response['error_type'] = 'validation';
            $response['message'] = $this->formatValidationErrors($validator);
            return response()->json($response);
        }

        try {
            DocumentsFolder::create([
                'unique_id' => randomNumber(),
                'name' => $request->input('name'),
                'slug' => str_slug($request->input('name')),
                'user_id' => \Auth::user()->id,
                'added_by' => \Auth::user()->id,
            ]);

            $response['status'] = true;
            $response['redirect_back'] = baseUrl('document-folders');
            $response['message'] = "Record added successfully";
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['error_type'] = 'exception';
            $response['message'] = 'An error occurred while saving the record.';
        }

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified roles.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $record = $this->findFolderByUniqueId($id);

        $viewData['record'] = $record;
        $viewData['pageTitle'] = "Edit Document Folder";
        $view = view('admin-panel.08-cases.document-folders.edit', $viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;
        return response()->json($response);
    }

    /**
     * Update the specified country in the database.
     *
     * @param string $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $object = $this->findFolderByUniqueId($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|input_string|max:255',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $response['message'] = $this->formatValidationErrors($validator);
            return response()->json($response);
        }

        try {
            DocumentsFolder::where('id', $object->id)->update([
                'name' => $request->input('name'),
                'slug' => str_slug($request->input('name'))
            ]);

            $response['status'] = true;
            $response['redirect_back'] = baseUrl('document-folders');
            $response['message'] = "Record updated successfully";
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['error_type'] = 'exception';
            $response['message'] = 'An error occurred while updating the record.';
        }

        return response()->json($response);
    }

    /**
     * Remove the specified country from the database.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteSingle($id)
    {
        $record = $this->findFolderByUniqueId($id);
      
        try {
            DocumentsFolder::deleteRecord($record->id);
            return redirect()->back()->with("success", "Record deleted successfully");
        } catch (\Exception $e) {
            return redirect()->back()->with("error", "An error occurred while deleting the record.");
        }
    }

    /**
     * Remove multiple Country from the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMultiple(Request $request)
    {
        $ids = explode(",", $request->input("ids"));
        $hasError = false;
        try {
            for ($i = 0; $i < count($ids); $i++) {
                $act = $this->findFolderByUniqueId($ids[$i]);
                if ($this->checkEditable($act)) {
                    DocumentsFolder::deleteRecord($act->id);
                }
            }
            $response['status'] = true;
            \Session::flash('success', 'Records deleted successfully');
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['error_type'] = 'exception';
            $response['message'] = 'An error occurred while deleting the records.';
        }
        return response()->json($response);
    }

    private function formatValidationErrors($validator)
    {
        $error = $validator->errors()->toArray();
        $errMsg = [];
        foreach ($error as $key => $err) {
            $errMsg[$key] = $err[0];
        }
        return $errMsg;
    }

    private function findFolderByUniqueId($id)
    {
        return DocumentsFolder::where('unique_id', $id)->first();
    }

    private function checkEditable($record)
    {
        return $record && $record->isEditableBy(auth()->id());
    }
}
