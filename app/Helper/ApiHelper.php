<?php
use Illuminate\Support\Str;
use Aws\S3\S3Client;
use App\Models\AwsFile;


function mediaUploadBaseCode($api, $base64Content, $uploadPath, $fileName)
{
    $token = apiKeys('MEDIA_TOKEN');
    $url = apiKeys('MEDIA_UPLOAD_URL') . 'api/' . $api;


    
    // Base64 encode the file
    // $base64Content = base64_encode($fileContent);
    
    // Initialize cURL
    $ch = curl_init($url);

    // Prepare data as JSON
    $data = json_encode([
        'file' => $base64Content,
        'file_name' => $fileName,
        'upload_path' => $uploadPath,
        'mime_type' => 'pdf',
        // 'file_size' => $fileSize,
        'encoding' => 'base64'
    ]);

    // Set up headers for JSON
    $headers = [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    // Configure cURL options
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_SSL_VERIFYPEER => false, // Set to true in production
        CURLOPT_TIMEOUT => 300, // 5 minutes for large files
    ]);

    // Execute the cURL request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($response, $headerSize);
    
    // Check for cURL errors
    if (curl_errno($ch)) {
        $errorMessage = curl_error($ch);
        curl_close($ch);

        return [
            'status' => 'failed_api_call',
            'message' => 'cURL error: ' . $errorMessage,
        ];
    }

    // Close cURL session
    curl_close($ch);

    // Process HTTP response
    if ($httpCode >= 200 && $httpCode < 300) {
        $decodedResponse = json_decode($body, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decodedResponse;
        } else {
            return [
                'status' => 'success',
                'message' => 'Upload successful',
                'response' => $body
            ];
        }
    } else {
        // Handle errors based on HTTP status code and response body
        return [
            'status' => false,
            'message' => 'API error: HTTP ' . $httpCode,
            'error' => $body,
        ];
    }
}
function mediaUploadApi($api, $filePath, $uploadPath, $fileName)
{
    $token = apiKeys('MEDIA_TOKEN');
    $url = apiKeys('MEDIA_UPLOAD_URL') . 'api/' . $api;

    // Initialize cURL
    $ch = curl_init($url);

    // Check if the file exists before proceeding
    if (!file_exists($filePath)) {
        return [
            'status' => 'file_not_found',
            'message' => 'File not found: ' . $filePath,
        ];
    }

    // Prepare the file for upload
    $file = new \CURLFile($filePath, mime_content_type($filePath), $fileName);

    // Set up the data for the request
    $data = [
        'file' => $file,
        'upload_path' => $uploadPath,
        'file_name' => $fileName,
    ];

    // Set up headers
    $headers = [
        'Authorization: Bearer ' . $token,
        'Content-Type: multipart/form-data',
    ];

    // Configure cURL options
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);

    // Execute the cURL request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get HTTP response code
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE); // Header size
    $body = substr($response, $headerSize); // Extract response body
    // Check for cURL errors
    if (curl_errno($ch)) {
        $errorMessage = curl_error($ch);
        curl_close($ch);

        return [
            'status' => 'failed_api_call',
            'message' => 'cURL error: ' . $errorMessage,
        ];
    }

    // Close cURL session
    curl_close($ch);

    // Process HTTP response
    if ($httpCode >= 200 && $httpCode < 300) {
        // Decode JSON response if possible
        $decodedResponse = json_decode($body, true);
        return $decodedResponse;
    } else {
        // Handle errors based on HTTP status code and response body
        return [
            'status' => false,
            'message' => 'API error: HTTP ' . $httpCode,
            'error' => $body,
        ];
    }
}


function downloadMediaFile($file_name, $dir, $size = '')
{ // r = regular t = thumb m = medium
    $token = apiKeys('MEDIA_TOKEN');
    $url = apiKeys('MEDIA_UPLOAD_URL') . 'download-file-url?file=' . $file_name . "&file_path=" . $dir . "&t=" . $token;
    if ($size != '') {
        $url .= "&s=" . $size;
    }
    return $url;
}

function mediaDeleteApi($uploadPath, $fileName)
{
    $token = apiKeys('MEDIA_TOKEN');
    $url = apiKeys('MEDIA_UPLOAD_URL') . 'api/uap-media/delete-file';

    // Initialize cURL
    $ch = curl_init($url);


    // Set up the data for the request
    $data = [
        'upload_path' => $uploadPath,
        'file_name' => $fileName,
    ];

    // Set up headers
    $headers = [
        'Authorization: Bearer ' . $token,
        'Content-Type: multipart/form-data',
    ];

    // Configure cURL options
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);

    // Execute the cURL request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get HTTP response code
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE); // Header size
    $body = substr($response, $headerSize); // Extract response body

    // Check for cURL errors
    if (curl_errno($ch)) {
        $errorMessage = curl_error($ch);
        curl_close($ch);

        return [
            'status' => 'failed_api_call',
            'message' => 'cURL error: ' . $errorMessage,
        ];
    }

    // Close cURL session
    curl_close($ch);

    // Process HTTP response
    if ($httpCode >= 200 && $httpCode < 300) {
        // Decode JSON response if possible
        $decodedResponse = json_decode($body, true);
        return $decodedResponse;
    } else {
        return [
            'status' => false,
            'message' => 'API error: HTTP ' . $httpCode,
            'error' => $body,
        ];
    }
}
function mediaPdfThumbPreview($file_url)
{ // r = regular t = thumb m = medium
    $token = apiKeys('MEDIA_TOKEN');
    $url = apiKeys('MEDIA_UPLOAD_URL') . 'pdf-thumbnail?pdfUrl=' . $file_url;
    
    return $url;
}
if (!function_exists("apiCall")) {
    function apiCall($url, $data = array(), $return = false)
    {

        $api_data = \DB::table("api_keys")->where("api_key", 'complaint_api_url')->first();
        $api_url = $api_data->api_value ?? '';

        // return $api_url."/".$url;
        $host = explode('.', request()->getHost());
        $host = $host[0];


        $api_secret = \DB::table("api_keys")->where("api_key", 'api_token')->first();
        $token = $api_secret->api_value ?? '';
        $ch = curl_init($api_url . "/" . $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        if (count($data) > 0) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        $response = curl_exec($ch);
        \Log::info($response);
        // echo $response;
        $info = curl_getinfo($ch);
        curl_close($ch);
        $curl_response = json_decode($response, true);
        if ($return == true) {
            echo $response;
        }
        return $curl_response;
    }
}
if (!function_exists("investgateApiCall")) {
    function investgateApiCall($url, $data = array(), $return = false)
    {
        $api_url = apiKeys('investigate_url');
        $host = explode('.', request()->getHost());
        $host = $host[0];
        $token = apiKeys('investigate_token');
        $ch = curl_init($api_url . "/" . $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        if (count($data) > 0) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        $response = curl_exec($ch);

        // echo $response;
        $info = curl_getinfo($ch);
        curl_close($ch);
        $curl_response = json_decode($response, true);

        if ($return == true) {
            echo $response;
        }
        return $curl_response;
    }
}


if (!function_exists("awsFileUpload")) {
    function awsFileUpload($file_name, $filePath)
    {
        $s3 = new S3Client([
            'region' => apiKeys('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key' => apiKeys('AWS_ACCESS_KEY_ID'),
                'secret' => apiKeys('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
        if (!file_exists($filePath)) {
            echo "File does not exist: " . $filePath;
            return false;
        }
        // $acl = 'public-read';
        $res = $s3->putObject([
            'Bucket' => apiKeys('AWS_BUCKET'),
            'Key' => $file_name,
            'Body' => file_get_contents($filePath),
        ]);
        AwsFile::create(['file_key' => $file_name]);
        return $res;
    }
}

if (!function_exists("awsBackupCode")) {
    function awsBackupCode($file_name, $filePath)
    {
        $s3 = new S3Client([
            'region' => apiKeys('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key' => apiKeys('AWS_ACCESS_KEY_ID'),
                'secret' => apiKeys('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        // Multipart upload for large files
        try {
            $uploader = new MultipartUploader($s3, $filePath, [
                'bucket' => apiKeys('AWS_BUCKET'),
                'key' => $file_name,
            ]);

            $result = $uploader->upload();
            AwsFile::create(['file_key' => $file_name]);
            return $result;
        } catch (MultipartUploadException $e) {
            echo "Upload failed: " . $e->getMessage();
            return false;
        }
        return $res;
    }
}
if (!function_exists("awsDownloadFile")) {
    function awsDownloadFile($fileKey, $original_name)
    {
        try {
            $s3 = new S3Client([
                'region' => apiKeys('AWS_DEFAULT_REGION'),
                'version' => 'latest',
                'credentials' => [
                    'key' => apiKeys('AWS_ACCESS_KEY_ID'),
                    'secret' => apiKeys('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            $bucket = apiKeys('AWS_BUCKET');
            // $key = '55727-31842-test.pdf'; // Replace with the object key

            $command = $s3->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key' => $fileKey,
            ]);
            $command['ResponseContentDisposition'] = 'attachment; filename="' . $original_name . '"'; // Replace 'custom-filename.pdf' with your desired filename

            $presignedUrl = $s3->createPresignedRequest($command, '+5 minutes')->getUri();
            return $presignedUrl;
        } catch (Exception $e) {
            // echo "Error deleting file: " . $e->getMessage();
            return '';
        }
    }
}

if (!function_exists("awsFetchFiles")) {
    function awsFetchFiles()
    {
        try {
            $s3 = new S3Client([
                'region' => apiKeys('AWS_DEFAULT_REGION'),
                'version' => 'latest',
                'credentials' => [
                    'key' => apiKeys('AWS_ACCESS_KEY_ID'),
                    'secret' => apiKeys('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);
            $bucket = apiKeys('AWS_BUCKET');
            $objects = $s3->listObjects([
                'Bucket' => $bucket,
            ]);
            $fileKeys = [];
            foreach ($objects['Contents'] as $object) {
                $fileKey = $object['Key'];
                $url = $s3->getObjectUrl($bucket, $fileKey);
                $fileKeys[] = $url;
            }
            return $fileKeys;
        } catch (Exception $e) {
            // echo "Error deleting file: " . $e->getMessage();
            return false;
        }
    }
}
if (!function_exists("awsDeleteFile")) {
    function awsDeleteFile($fileKey)
    {
        try {
            $s3 = new S3Client([
                'region' => apiKeys('AWS_DEFAULT_REGION'),
                'version' => 'latest',
                'credentials' => [
                    'key' => apiKeys('AWS_ACCESS_KEY_ID'),
                    'secret' => apiKeys('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            $bucket = apiKeys('AWS_BUCKET');

            $s3->deleteObject([
                'Bucket' => $bucket,
                'Key' => $fileKey,
            ]);
            AwsFile::where(['file_key' => $fileKey])->delete();
            return true;
        } catch (Exception $e) {
            // echo "Error deleting file: " . $e->getMessage();
            return false;
        }
    }
}

if (!function_exists("awsFileContent")) {
    function awsFileContent($fileKey)
    {
        try {
            $s3 = new S3Client([
                'region' => apiKeys('AWS_DEFAULT_REGION'),
                'version' => 'latest',
                'credentials' => [
                    'key' => apiKeys('AWS_ACCESS_KEY_ID'),
                    'secret' => apiKeys('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            $bucket = apiKeys('AWS_BUCKET');

            $fileContent = $s3->getObject(['Bucket' => $bucket, 'Key' => $fileKey])['Body'];

            // Add the file content to the zip with the desired filename

            return $fileContent;
        } catch (Exception $e) {
            // echo "Error deleting file: " . $e->getMessage();
            return '';
        }
    }
}

if (!function_exists("awsFilePreview")) {

    function awsFilePreview($fileKey)
    {
        try {
            if ($fileKey != '') {
                $s3 = new S3Client([
                    'region' => apiKeys('AWS_DEFAULT_REGION'),
                    'version' => 'latest',
                    'credentials' => [
                        'key' => apiKeys('AWS_ACCESS_KEY_ID'),
                        'secret' => apiKeys('AWS_SECRET_ACCESS_KEY'),
                    ],
                ]);

                $bucket = apiKeys('AWS_BUCKET');

                $result = $s3->getObject([
                    'Bucket' => $bucket,
                    'Key' => $fileKey,
                ]);

                $fileContent = $result['Body']->getContents();  // Correctly get the content as a string
                $extension = pathinfo($fileKey, PATHINFO_EXTENSION);
                $filename = time() . "." . $extension;
                $localDirectory = storage_path('app/public/aws-files');

                if (!is_dir($localDirectory)) {
                    mkdir($localDirectory, 0755, true);
                }

                $localPath = $localDirectory . '/' . $filename;

                // Save the file content locally and check for errors
                if (file_put_contents($localPath, $fileContent) === false) {
                    throw new Exception("Failed to write file to $localPath");
                }
                file_put_contents($localPath, $fileContent); // Save the file content locally

                // Generate a URL to the stored file
                $url = url('storage/app/public/aws-files/' . $filename);
                return $url;
            } else {
                return '';
            }
        } catch (AwsException $e) {
            echo "Error while fetching: " . $e->getMessage();
            return '';
        }
    }
}
if (!function_exists("awsFileDownload")) {

    function awsFileDownload($fileKey, $raw = false)
    {
        try {
            if ($fileKey != '') {
                $s3 = new S3Client([
                    'region' => apiKeys('AWS_DEFAULT_REGION'),
                    'version' => 'latest',
                    'credentials' => [
                        'key' => apiKeys('AWS_ACCESS_KEY_ID'),
                        'secret' => apiKeys('AWS_SECRET_ACCESS_KEY'),
                    ],
                ]);

                $bucket = apiKeys('AWS_BUCKET');

                // Get the object from S3
                $result = $s3->getObject([
                    'Bucket' => $bucket,
                    'Key' => $fileKey,
                ]);

                // Get file contents
                $fileContent = $result['Body']->getContents();
                
                if ($raw) {
                    return $fileContent;
                }
                
                // Get extension and clean filename
                $extension = strtolower(pathinfo($fileKey, PATHINFO_EXTENSION));
                $filename = basename($fileKey);
                
                // Clean filename to avoid issues
                $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
                
                // Determine content type
                $contentType = $result['ContentType'] ?? null;
                
                // Map common file extensions to MIME types
                $mimeTypes = [
                    'pdf' => 'application/pdf',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'doc' => 'application/msword',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'xls' => 'application/vnd.ms-excel',
                    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'ppt' => 'application/vnd.ms-powerpoint',
                    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'zip' => 'application/zip',
                    'txt' => 'text/plain',
                    'csv' => 'text/csv',
                    'mp4' => 'video/mp4',
                    'mp3' => 'audio/mpeg',
                ];
                
                // Use mapped MIME type if available
                if (!$contentType || $contentType === 'binary/octet-stream') {
                    $contentType = $mimeTypes[$extension] ?? 'application/octet-stream';
                }

                // Clear any output buffers
                if (ob_get_level()) {
                    ob_end_clean();
                }

                // Set up headers for download
                return response($fileContent)
                    ->header('Content-Type', $contentType)
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->header('Content-Length', strlen($fileContent))
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
                    
            } else {
                return redirect()->back()->with("error", "File key is missing");
            }
        } catch (Exception $e) {
            // Handle any AWS errors
            return redirect()->back()->with("error", 'Error while fetching file: ' . $e->getMessage());
        }
    }
}
if (!function_exists("awsFileEncoded")) {
    function awsFileEncoded($fileKey, $format = 'base64')
    {
        try {
            if (empty($fileKey)) {
                return response()->json([
                    'error' => 'File key is missing'
                ], 400);
            }

            $s3 = new S3Client([
                'region' => apiKeys('AWS_DEFAULT_REGION'),
                'version' => 'latest',
                'credentials' => [
                    'key' => apiKeys('AWS_ACCESS_KEY_ID'),
                    'secret' => apiKeys('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            $bucket = apiKeys('AWS_BUCKET');

            // Get the object from S3
            $result = $s3->getObject([
                'Bucket' => $bucket,
                'Key' => $fileKey,
            ]);

            // Get file contents
            $fileContent = $result['Body']->getContents();
            $contentType = $result['ContentType'] ?? 'application/octet-stream';
            
            switch ($format) {
                case 'base64':
                    return [
                        'data' => base64_encode($fileContent),
                        'mime_type' => $contentType
                    ];
                    
                case 'data_uri':
                    return response()->json([
                        'data_uri' => "data:$contentType;base64," . base64_encode($fileContent)
                    ]);
                    
                case 'raw':
                    return response($fileContent)
                        ->header('Content-Type', $contentType);
                        
                default:
                    return response()->json([
                        'error' => 'Invalid format specified'
                    ], 400);
            }
                
        } catch (Exception $e) {
            \Log::error('AWS S3 Error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Error while fetching file',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
if (!function_exists("awsFilePreviewUrl")) {
    function awsFilePreviewUrl($fileKey, $extension, $expiration = '+1 hour')
    {
        try {
            if ($fileKey == '') {
                return null;
            }

            $s3 = new S3Client([
                'region' => apiKeys('AWS_DEFAULT_REGION'),
                'version' => 'latest',
                'credentials' => [
                    'key' => apiKeys('AWS_ACCESS_KEY_ID'),
                    'secret' => apiKeys('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            $bucket = apiKeys('AWS_BUCKET');
             if ($extension === 'pdf') {
                $cmd = $s3->getCommand('GetObject', [
                    'Bucket' => $bucket,
                    'Key' => $fileKey,
                    'ResponseContentType' => 'application/pdf',
                    'ResponseContentDisposition' => 'inline; filename="' . rawurlencode(basename($fileKey)) . '"',
                    'ResponseCacheControl' => 'max-age=3600',
                ]);
            } else {
                $cmd = $s3->getCommand('GetObject', [
                    'Bucket' => $bucket,
                    'Key' => $fileKey,
                    'ResponseContentDisposition' => 'inline; filename="' . basename($fileKey) . '"'
                ]);
            }

            $request = $s3->createPresignedRequest($cmd, $expiration);
            
            return (string) $request->getUri();
            
        } catch (Exception $e) {
            \Log::error('Error generating preview URL: ' . $e->getMessage());
            return null;
        }
    }
}
if (!function_exists("awsFileDownloadToFolder")) {

    function awsFileDownloadToFolder($fileKey, $folder_path, $fileName)
    {
        try {
            $s3 = new S3Client([
                'region' => apiKeys('AWS_DEFAULT_REGION'),
                'version' => 'latest',
                'credentials' => [
                    'key' => apiKeys('AWS_ACCESS_KEY_ID'),
                    'secret' => apiKeys('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            $bucket = apiKeys('AWS_BUCKET');
            if (!is_dir(storage_path('app/public/aws-files/encrypted/' . $folder_path))) {
                mkdir(storage_path('app/public/aws-files/encrypted/' . $folder_path), 0777);
            }
            // $localPath = storage_path('app/public/aws-files/encrypted/'.$folder_path.'/'.$filename);
            $localFolder = storage_path('app/public/aws-files/encrypted/' . $folder_path);
            $result = $s3->getObject([
                'Bucket' => $bucket,
                'Key' => $fileKey,
                'SaveAs' => $localFolder . '/' . $fileName,
            ]);

        } catch (Exception $e) {
            echo "Error deleting file: " . $e->getMessage();
            return '';
        }
    }
}

if (!function_exists("assistantApiCall")) {
    function assistantApiCall($url, $data = array(), $is_file = false, $return = false)
    {
        $api_url = apiKeys('ASSISTANT_API_URL');

        $host = explode('.', request()->getHost());
        $host = $host[0];
        $token = apiKeys('ASSISTANT_API_TOKEN');

        $ch = curl_init($api_url . "/" . $url);
        // \Log::info($api_url."/".$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        if (count($data) > 0) {
            // Prepare the file for upload
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        $response = curl_exec($ch);
       
        // echo $response;
        $info = curl_getinfo($ch);
        curl_close($ch);
        $curl_response = json_decode($response, true);
      
        if ($return == true) {
            echo $response;
        }
        return $curl_response;
    }
}

if (!function_exists("assistantUploadApiCall")) {
    function assistantUploadApiCall($url, $data = array(), $return = false)
    {

        $api_url = apiKeys('ASSISTANT_API_URL');

        $host = explode('.', request()->getHost());
        $host = $host[0];
        $token = apiKeys('ASSISTANT_API_TOKEN');

        $ch = curl_init($api_url . "/" . $url);
        // \Log::info($api_url."/".$url);
        // Prepare the file for upload
        $file = new \CURLFile($data['filePath'], mime_content_type($data['filePath']), $data['fileName']);

        // Set up the data for the request
        $datas = [
            'file' => $file,
            'upload_path' => $data['filePath'],
            'file_name' => $data['fileName'],
            'user_id' => $data['user_id'],
            'conversation_id' => $data['conversation_id']
        ];

        // Set up headers
        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: multipart/form-data',
        ];
        $fileResource = fopen($data['filePath'], 'r');
        // Configure cURL options
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_INFILE, $fileResource);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($data['filePath']));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get HTTP response code
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE); // Header size
        $body = substr($response, $headerSize); // Extract response body
        // Check for cURL errors
        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            curl_close($ch);

            return [
                'status' => 'failed_api_call',
                'message' => 'cURL error: ' . $errorMessage,
            ];
        }

        // Close cURL session
        curl_close($ch);

        // Process HTTP response
        if ($httpCode >= 200 && $httpCode < 300) {
            // Decode JSON response if possible
            $decodedResponse = json_decode($body, true);
            return $decodedResponse;
        } else {
            // Handle errors based on HTTP status code and response body
            return [
                'status' => false,
                'message' => 'API error: HTTP ' . $httpCode,
                'error' => $body,
            ];
        }
    }
}

if(!function_exists("securityApi")){    
    function securityApi($url,$data=array(),$return=false){
        $isLocalhost = in_array(request()->getHost(), ['127.0.0.1', 'locahost']);
        if ($isLocalhost) {
            if ($return) {
                return ['status' => true, 'message' => 'Localhost bypass successful'];
            }
            return ['status' => 'success', 'message' => 'Localhost bypass successful'];

        }
        
        $api_url = apiKeys('SECURITY_URL');
        $host = explode('.', request()->getHost());
        $host = $host[0];
        $token = apiKeys('SECURITY_TOKEN');
        $ch = curl_init($api_url."/".$url);
      
       
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
           'Content-Type: application/json',
           'Authorization: Bearer '. $token
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        if(count($data) > 0){
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        $response = curl_exec($ch);
        // pre ($response);
        // echo $response;
        $info = curl_getinfo($ch);
        curl_close($ch);
        $curl_response = json_decode($response,true);

        if($return == true){
            echo $response;
        }
        return $curl_response;
    }

  if (!function_exists("awsRenameFile")) {
    function awsRenameFile($oldKey, $newKey)
    {
        try {
            $s3 = new S3Client([
                'region' => apiKeys('AWS_DEFAULT_REGION'),
                'version' => 'latest',
                'credentials' => [
                    'key' => apiKeys('AWS_ACCESS_KEY_ID'),
                    'secret' => apiKeys('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            $bucket = apiKeys('AWS_BUCKET');

            // Return error if new name is same as old
            if (trim($oldKey) === trim($newKey)) {
                return [
                    'status' => false,
                    'message' => 'Old and new file names are the same.',
                ];
            }

            // ✅ Step 1: Check if file with new name exists
            try {
                $s3->headObject([
                    'Bucket' => $bucket,
                    'Key' => $newKey,
                ]);

                // If no exception, file exists
                return [
                    'status' => false,
                    'message' => 'A file with the new name already exists.',
                ];

            } catch (\Aws\S3\Exception\S3Exception $e) {
                $errorCode = $e->getAwsErrorCode();
                if ($errorCode !== 'NotFound' && $errorCode !== 'NoSuchKey') {
                    return [
                        'status' => false,
                        'message' => 'Unexpected error while checking file: ' . $e->getMessage(),
                    ];
                }
                // continue if file doesn't exist
            }

            // ✅ Step 2: Copy object
            $s3->copyObject([
                'Bucket'     => $bucket,
                'CopySource' => "{$bucket}/{$oldKey}",
                'Key'        => $newKey,
                'ACL'        => 'private',
            ]);

            // ✅ Step 3: Delete old file
            $s3->deleteObject([
                'Bucket' => $bucket,
                'Key' => $oldKey,
            ]);

            // ✅ Step 4: Update file reference (optional)
            AwsFile::where('file_key', $oldKey)->update(['file_key' => $newKey]);

            return [
                'status' => true,
                'message' => 'File renamed successfully.',
            ];

        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Error renaming file: ' . $e->getMessage(),
            ];
        }
    }
}


}