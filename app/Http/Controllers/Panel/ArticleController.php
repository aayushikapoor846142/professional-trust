<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ArticleType;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use View;
use App\Models\Article;
use App\Services\FeatureCheckService;

class ArticleController extends Controller
{
    protected $featureCheckService;

    public function __construct(FeatureCheckService $featureCheckService)
    {
        $this->featureCheckService = $featureCheckService;
    }

    /**
     * Display the list of Action.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        $articleFeatureStatus = $this->featureCheckService->canAddArticle($user->id);
        
        $viewData['pageTitle'] = "Articles";
        $viewData['articleFeatureStatus'] = $articleFeatureStatus;
        $viewData['canAddArticle'] = $articleFeatureStatus['allowed'];
        return view('admin-panel.09-utilities.articles.lists', $viewData);
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
        $records = Article::where(function ($query) use ($search) {
                if ($search != '') {
                    $query->where("name", "LIKE", "%" . $search . "%");
                }
            })
            ->with(['userAdded'])
            ->visibleToUser(auth()->user()->id)
             ->orderBy($sortColumn, $sortDirection)
            ->paginate();

        $viewData['records'] = $records;
        $contents = view('admin-panel.09-utilities.articles.ajax-list', $viewData)->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        return response()->json($response);
    }

    /**
     * Show the form for creating a new action.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add()
    {
        $viewData['pageTitle'] = "Add Article";
        $viewData['categories'] = Category::get();
        $viewData['article_types'] = ArticleType::get();
        return view('admin-panel.09-utilities.articles.add', $viewData);
    }

    /**
     * Format validation errors for JSON response.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return array
     */
    private function formatValidationErrors($validator)
    {
        $error = $validator->errors()->toArray();
        $errMsg = [];
        foreach ($error as $key => $err) {
            $errMsg[$key] = $err[0];
        }
        return $errMsg;
    }

    /**
     * Handle file upload logic for articles.
     *
     * @param string $fileName
     * @param string $sourceDir
     * @return array|null
     */
    private function handleArticleFileUpload($fileName, $sourceDir = 'uploads/temp/')
    {
        if ($fileName != '') {
            $sourcePath = public_path($sourceDir . $fileName);
            $uploadPath = articleDir();
            $response = mediaUploadApi("upload-file", $sourcePath, $uploadPath, $fileName);
            if (isset($response['status']) && $response['status'] == 'success') {
                \File::delete($sourcePath);
                return $response;
            } else {
                return null;
            }
        }
        return null;
    }

    /**
     * Remove files from the article storage.
     *
     * @param array $files
     * @return void
     */
    private function removeArticleFiles(array $files)
    {
        foreach ($files as $img) {
            Article::removeFiles($img);
        }
    }

    /**
     * Handle attachments merging and removal logic for update.
     *
     * @param string $currentFiles
     * @param array|null $prevFiles
     * @param string|null $newAttachments
     * @return string
     */
    private function handleAttachments($currentFiles, $prevFiles, $newAttachments)
    {
        $files = '';
        $current_files = $currentFiles ? explode(",", $currentFiles) : [];
        $prev_files = $prevFiles ?? [];
        if (!empty($current_files)) {
            if (!empty($prev_files)) {
                $removed_files = array_diff($current_files, $prev_files);
                if (!empty($removed_files)) {
                    $this->removeArticleFiles($removed_files);
                }
                if ($newAttachments) {
                    $files = implode(",", $prev_files) . "," . $newAttachments;
                } else {
                    $files = implode(",", $prev_files);
                }
            } else {
                $this->removeArticleFiles($current_files);
                if ($newAttachments) {
                    $files = $newAttachments;
                } else {
                    $files = '';
                }
            }
        } else {
            $files = $newAttachments;
        }
        return $files;
    }

    /**
     * Store a newly created action in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:articles,name|input_string|input_sanitize',
            'slug' => 'required|max:255|unique:articles,slug',
            'category' =>'required',
            'article_type' =>'required',
            'description' =>'required',
            'summary' =>'required|input_sanitize',
            'seo_title' => 'required|input_sanitize',
            'seo_keywords' => 'required|input_sanitize',
            'seo_description' => 'required|input_sanitize',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $response['message'] = $this->formatValidationErrors($validator);
            return response()->json($response);
        }
        if($request->images != ''){
            $uploadResponse = $this->handleArticleFileUpload($request->images);
            if(!$uploadResponse){
                return response()->json(['status'=>false,'message' => "Failed uploading image. Try again."]);
            }
        }
        $article = Article::create([
            'unique_id' => randomNumber(),
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('slug')),
            'category_id' => $request->input('category'),
            'article_type_id' => $request->input('article_type'),
            'description' => htmlentities($request->input('description')),
            'summary' => $request->input('summary'),
            'images' => $request->input('images'),
            'files' => $request->input('attachments'),
            'reading_time' => $request->input('reading_time'),
            'show_on_home' => $request->input('show_on_home')??0,
            'is_featured' => $request->input('is_featured')??0,
            'added_by' => \Auth::user()->id,
            'status' => 'pending',
        ]);

        $parameters = [
            'module_type' => 'article', 
            'page_route' => 'articles.detail',  
            'reference_id' => $article->id,  
            'meta_title' => $request->input('seo_title'),
            'meta_keywords' => $request->input('seo_keywords'),
            'meta_description' => $request->input('seo_description'),
            'added_by' => \Auth::id(),  
        ];
        
       saveSeoDetails($parameters);

        // Save article usage to UserPlanFeatureHistory
        try {
            $result = $this->featureCheckService->savePlanFeature(
                'articles', 
                \Auth::user()->id, 
                1, // action type: add
                1, // count: 1 article
                [
                    'article_id' => $article->id,
                    'article_title' => $article->name,
                    'article_slug' => $article->slug
                ]
            );

            // Log the result for debugging
            \Log::info('savePlanFeature result for article creation', [
                'user_id' => \Auth::user()->id,
                'article_id' => $article->id,
                'result' => $result
            ]);

            if (!$result['success']) {
                \Log::error('Failed to save plan feature usage for article creation', [
                    'user_id' => \Auth::user()->id,
                    'article_id' => $article->id,
                    'error' => $result['message']
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Exception in savePlanFeature for article creation', [
                'user_id' => \Auth::user()->id,
                'article_id' => $article->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        $response['status'] = true;
        $response['redirect_back'] = baseUrl('articles');
        $response['message'] = "Record added successfully";

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified action.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $record =  Article::select(['id', 'unique_id', 'name', 'slug', 'category_id', 'article_type_id', 'images', 'files', 'description', 'reading_time', 'show_on_home', 'is_featured', 'added_by', 'status', 'created_at'])
            ->with(['seoDetails'])
            ->where('unique_id',$id)
            ->first();

        if (!$record) {
            abort(403);
        }
     

        $viewData['record'] = $record;
        $viewData['pageTitle'] = "Edit Article";
        $viewData['categories'] = Category::get();
        $viewData['article_types'] = ArticleType::get();
        return view('admin-panel.09-utilities.articles.edit', $viewData);
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
        $object = Article::select(['id', 'unique_id', 'name', 'slug', 'category_id', 'article_type_id', 'images', 'files', 'description', 'reading_time', 'show_on_home', 'is_featured', 'added_by', 'status', 'created_at'])
            ->where('unique_id',$id)
            ->first();
        if (!$object) {
            abort(404);
        }
        
        // Add authorization check
        if (! $object->isEditableBy(auth()->id())) {
            abort(403, 'You are not authorized to update this article.');
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|input_sanitize|unique:articles,name,' . $object->id,
            'slug' => 'required|max:255|input_sanitize|unique:articles,slug,' . $object->id,
            'category' =>'required',
            'article_type' =>'required',
            'description' =>'required',
            'summary' =>'required|input_sanitize',
            'seo_title' => 'required|input_sanitize',
            'seo_keywords' => 'required|input_sanitize',
            'seo_description' => 'required|input_sanitize',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $response['message'] = $this->formatValidationErrors($validator);
            return response()->json($response);
        }
        $images = $object->images;
        if($request->images != ''){
            $uploadResponse = $this->handleArticleFileUpload($request->images);
            if($uploadResponse){
                $images = $request->images;
            }
        }
        $files = $this->handleAttachments(
            $object->files,
            $request->prev_files,
            $request->attachments
        );
        Article::where('id',$object->id)->update([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('slug')),
            'category_id' => $request->input('category'),
            'article_type_id' => $request->input('article_type'),
            'files' => $files,
            'images' => $images,
            'description' => htmlentities($request->input('description')),
            'reading_time' => $request->input('reading_time'),
            'summary' => $request->input('summary'),
            'revise_date' => $request->input('revise_date') == 1 ? now() : null,
            'show_on_home' => $request->input('show_on_home')??0,
            'is_featured' => $request->input('is_featured')??0,
        ]);

        $parameters = [
            'reference_id' => $object->id,
            'module_type' => 'article', 
            'page_route' => 'articles.detail',  
            'meta_title' => $request->input('seo_title'),
            'meta_keywords' => $request->input('seo_keywords'),
            'meta_description' => $request->input('seo_description'),
            'added_by' => \Auth::id(),  
        ];
    
        // Save or update SEO details using helper function
        $seoDetail = saveSeoDetails($parameters);
 

        $response['status'] = true;
        $response['redirect_back'] = baseUrl('articles');
        $response['message'] = "Record updated successfully";

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
        $action = Article::select(['id', 'unique_id', 'name', 'slug', 'category_id', 'article_type_id', 'images', 'files', 'description', 'reading_time', 'show_on_home', 'is_featured', 'added_by', 'status', 'created_at'])
            ->with(['seoDetails'])
            ->where('unique_id',$id)
            ->first();
        if (!$action) {
            abort(404);
        }
        if (! $action->isEditableBy(auth()->id())) {
            abort(403);
        }
        $parameters = [
            'reference_id' => $action->id,
            'module_type' => $action->seoDetails ? $action->seoDetails->module_type : null,
        ];
        deleteSeoDetails($parameters);
        Article::deleteRecord($action->id);
        return redirect()->back()->with("success", "Record deleted successfully");
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
        for ($i = 0; $i < count($ids); $i++) {
            $act = Article::where('unique_id',$ids[$i])->first();
            if (!$act) {
                continue;
            }
            // $id = base64_decode($ids[$i]);
            if ($act->isEditableBy(auth()->id())) {
                Article::deleteRecord($act->id);
            }
           
        }
        $response['status'] = true;
        \Session::flash('success', 'Records deleted successfully');
        return response()->json($response);
    }

    public function uploadFiles(Request $request)
    {
        $uploadedFiles = [];
        $response = ['status' => false, 'message' => 'No files uploaded'];
        
        // Handle multiple files
        if ($request->hasFile('file')) {
            $files = $request->file('file');
            
            // If it's a single file, convert to array
            if (!is_array($files)) {
                $files = [$files];
            }
            
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    try {
                        $fileName = $file->getClientOriginalName();
                        $newName = mt_rand(1, 99999) . "-" . $fileName;
                        $uploadPath = articleDir();
                        $sourcePath = $file->getPathName();
                        $api_response = mediaUploadApi("upload-file", $sourcePath, $uploadPath, $newName);
                       
                        if (($api_response['status'] ?? '') === 'success') {
                            $uploadedFiles[] = $newName;
                        }
                    } catch (\Exception $e) {
                        // Log error but continue with other files
                        \Log::error('File upload error: ' . $e->getMessage());
                    }
                }
            }
            
            if (!empty($uploadedFiles)) {
                $response['status'] = true;
                $response['files'] = $uploadedFiles;
                $response['message'] = "Files uploaded successfully";
            } else {
                $response['status'] = false;
                $response['message'] = "Error uploading files";
            }
        }

        return response()->json($response);
    }

    public function imageCropper(Request $request)
    {
        $viewData['pageTitle'] = "Image Cropper"; // Set the page title
        $viewData['crop_url'] = baseUrl('articles/upload-cropped-image');
        $contents = view('components.image-cropper', $viewData)->render();

        // Prepare the JSON response
        $response['contents'] = $contents;
        $response['status'] = true;

        return response()->json($response);
    }
    public function saveCroppedImage(Request $request){
        if ($file = $request->file('file')){
            $extension       = $file->getClientOriginalExtension() ?: 'png';
            $newName        = mt_rand().".".$extension;
            $destinationPath = public_path('uploads/temp');
            if($file->move($destinationPath, $newName)){
                return response()->json(['status'=>true,'message' => "Image cropped successfully.", 'filename' => $newName,'filepath' => url('uploads/temp/'.$newName)]);
            }else{
                return response()->json(['status'=>false,'message' => "Some issue while upload. Try again"]);
            }
        }
    }
}
