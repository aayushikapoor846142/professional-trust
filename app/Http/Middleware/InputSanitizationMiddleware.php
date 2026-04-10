<?php 
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;            
use HTMLPurifier;
use HTMLPurifier_Config;
use DOMDocument;

class InputSanitizationMiddleware
{
    public function handle($request, Closure $next)
    {
        if (\Auth::check()) {
            if (\Auth::user()->last_activity_log != '') {
                $dateTimeString = \Auth::user()->last_activity_log;
                $dateTimeObject = new \DateTime($dateTimeString);
                $now = new \DateTime();

                $difference = $now->diff($dateTimeObject);

                $minutes = ($difference->days * 24 * 60) + ($difference->h * 60) + $difference->i;

                /*if($minutes > 15){
                    User::where("id",auth()->user()->id)->update(['last_activity_log'=>date("Y-m-d H:i:s")]);
                    \Auth::logout();
                    return redirect("/login")->with('error',"last activity redirect");
                }*/
            } else {
                User::where("id", auth()->user()->id)->update(['last_activity_log' => date("Y-m-d H:i:s")]);
            }
        }

        // Define allowed file extensions for each file input (dynamic)
        $allowedExtensions = ["png", "jpeg", "jpg", "pdf", "doc", "docx", "xls", "xlsx", "csv","mp3","mp4"];
        $postData = $request->all();
        foreach ($postData as $key => $value) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                if(is_array($file)){
                    $files = $request->file($key);
                    foreach($files as $file){
                        $extension = strtolower($file->getClientOriginalExtension());
                        if (empty($allowedExtensions) || !in_array($extension, $allowedExtensions)) {
                            if ($request->ajax()) {
                                $response['status'] = false;
                                $errMsg = [];
                                $errMsg[$key] = 'Invalid file extension. Allowed extensions are: ' . implode(', ', $allowedExtensions);

                                $response['message'] = $errMsg;
                                return response()->json($response);
                            } else {
                                return redirect()->back()->with("error", 'Invalid file extension. Allowed extensions are: ' . implode(', ', $allowedExtensions));
                            }
                        }

                    }
                }else{
                    if ($file) {
                        $extension = strtolower($file->getClientOriginalExtension());
                        if (empty($allowedExtensions) || !in_array($extension, $allowedExtensions)) {
                            if ($request->ajax()) {
                                $response['status'] = false;
                                $errMsg = [];
                                $errMsg[$key] = 'Invalid file extension. Allowed extensions are: ' . implode(', ', $allowedExtensions);

                                $response['message'] = $errMsg;
                                return response()->json($response);
                            } else {
                                return redirect()->back()->with("error", 'Invalid file extension. Allowed extensions are: ' . implode(', ', $allowedExtensions));
                            }
                        }
                    }
                }
                
            }
        }

        // Define HTMLPurifier configuration
        $disallowedTags = ['script', 'form'];
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.ForbiddenElements', implode(',', $disallowedTags));
        $purifier = new HTMLPurifier($config);

        $invalidInputs = [];
        array_walk_recursive($postData, function (&$item, $key) use ($request, $purifier, &$invalidInputs, $disallowedTags) {
            if (!$request->hasFile($key)) {
                if ($this->containsPhpTags($item)) {
                    $invalidInputs[$key] = 'Invalid input value. PHP tags are not allowed.';
                } else if ($this->isHtml($item)) {
                    $cleanedItem = $purifier->purify($item);
					// $item = $cleanedItem;
                     if ($cleanedItem !== $item) {
                        $invalidTags = $this->getInvalidTags($item, $disallowedTags);
                        $invalidInputs[$key] = 'HTML is not allowed in Input. ' . implode(', ', $invalidTags);
                    } else {
                        $item = $cleanedItem;
                    }
                }
            }
        });

        if (!empty($invalidInputs)) {
              if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'error_type' => 'validation',
                    'message' => $invalidInputs
                ]);
            } else {
                return redirect()->back()->withErrors($invalidInputs);
            }
        }

        $request->merge($postData);

        return $next($request);
    }

    protected function isHtml($string)
    {
        return preg_match("/<\/?[a-z][\s\S]*>/i", $string);
    }

    protected function containsPhpTags($string)
    {
        return preg_match('/<\?php[\s\S]*?\?>/i', $string);
    }

    protected function getInvalidTags($html, $disallowedTags)
    {
        $invalidTags = [];
        $dom = new DOMDocument;
        @$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        foreach ($disallowedTags as $tag) {
            $elements = $dom->getElementsByTagName($tag);
            if ($elements->length > 0) {
                $invalidTags[] = $tag;
            }
        }
        return $invalidTags;
    }
}

?>