<?php

namespace App\Console\Commands;

use App\Jobs\CaptureScreenshotJob;
use App\Models\CompanySiteScreenshot;
use App\Models\ProfessionalSite;
use App\Models\ScreenCaptures;
use App\Models\ScreenshotHistory;
use App\Models\ScreenshotLog;
use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\File;
use Spatie\Async\Pool;
class CaptureScreenshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'capture:screenshot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Capture screenshots from websites in parallel using Puppeteer';

    /**
     * Execute the console command.
     */
   /* public function handle()
    {
        try{
            $pool = Pool::create()->concurrency(5);
            $records = ScreenCaptures::get();
            $allRecords = ScreenCaptures::get()->merge(ProfessionalSite::get()); // Merged both queries

            foreach($records as $record){
                $pool->add(function () use ($record) {
                    $file_name = time()."-".mt_rand(100,999).".png";
                    $url = $record->site_url;
            
                    $directoryPath = storage_path('app/public/screenshots/'.$record->unique_id);
                    $screenshotPath = $directoryPath.'/'. $file_name;
                    // Global Site Screenshot Capture

                    // Check if the directory exists, if not, create it
                    if (!\File::exists($directoryPath)) {
                        \File::makeDirectory($directoryPath, 0755, true); // Create directory with permissions
                    }
                    // Run Puppeteer script
                    $process = new Process([
                        'node', 'library/screenshot-capture/capture.js', $url, $screenshotPath
                    ]);
                    $process->run();
                    
                    // Check if the process was successful
                    if (!$process->isSuccessful()) {
                        throw new ProcessFailedException($process);
                    }
                    ScreenshotHistory::create(['file_name'=>$file_name,'capture_id'=>$record->id,'added_by'=>0]);
                    
                })->catch(function (ProcessFailedException $e) use ($record) {
                    ScreenshotLog::create(['error_log'=>"Screenshot capture failed for URL: {$record->site_url} - Error: " . $e->getMessage(),'type'=>'screen_capture']);
                });
                
            }


            // Capture Companies Site Screenshots

            $records = ProfessionalSite::get();
            foreach($records  as $record){
                $pool->add(function () use ($record) {
                    $file_name = time()."-".mt_rand(100,999).".png";
                    $url = $record->site_url;
            
                    $directoryPath = storage_path('app/public/company_screenshot/'.$record->unique_id.'/');

                    $screenshotPath = $directoryPath.'/'. $file_name;
                    if (!\File::exists($directoryPath)) {
                        \File::makeDirectory($directoryPath, 0755, true); // Create directory with permissions
                    }
                    // Run Puppeteer script
                    $process = new Process([
                        'node', 'library/screenshot-capture/capture.js', $url, $screenshotPath
                    ]);
                    
                    $process->run();
                    
                    // Check if the process was successful
                    if (!$process->isSuccessful()) {
                        throw new ProcessFailedException($process);
                    }
                    CompanySiteScreenshot::create(['file_name'=>$file_name,'professional_site_id'=>$record->id,'added_by'=>0]);
                })->catch(function (ProcessFailedException $e) use ($record) {
                    ScreenshotLog::create(['error_log'=>"Screenshot capture failed for URL: {$record->site_url} - Error: " . $e->getMessage(),'type'=>'professional_sites']);
                });
            }
            $pool->wait();
        } catch (ProcessFailedException $e) {
            echo $e->getMessage();       
        } 
        catch(\Exception $e){
            echo $e->getMessage();
        }
    }*/

    public function handle()
    {
        // Capture Professional Sites Screenshots
        $records = ScreenCaptures::where('site_status','active')->get();
        foreach ($records as $record) {
            $file_name = time() . "-" . mt_rand(100, 999) . ".png";
            $url = $record->site_url;
            $directoryPath = storage_path('app/public/screenshots/' . $record->unique_id);
            if(checkWebsiteStatus($url) == 1){
                // Dispatch the job for professional site
                CaptureScreenshotJob::dispatch($url, $directoryPath, $file_name, $record->id, 'screen_capture');
            }else{
                ScreenCaptures::where("id",$record->id)->update(['site_status'=>'down']);
            }
            
        }

        // Capture Company Sites Screenshots
        $companies = ProfessionalSite::where('site_status','active')->get();
        foreach ($companies as $company) {
            $file_name = time() . "-" . mt_rand(100, 999) . ".png";
            $url = $company->site_url;
            $directoryPath = storage_path('app/public/company_screenshot/' . $company->unique_id);
            if(checkWebsiteStatus($url) == 1){
                // Dispatch the job for company site
                CaptureScreenshotJob::dispatch($url, $directoryPath, $file_name, $company->id, 'professional_sites');
            }else{
                ProfessionalSite::where("id",$company->id)->update(['site_status'=>'down']);
            }
        }
        $this->startQueueWorker();
    }

    private function startQueueWorker()
    {
        // Run the queue worker using Symfony Process
        $process = new Process(['php', 'artisan', 'queue:work', '--stop-when-empty']);
        $process->setTimeout(0); // Set timeout to 0 so it doesn't stop
        $process->start();

        // Log the output
        foreach ($process as $type => $data) {
            //echo $data."\n\n<Br>";
        }
    }
}
