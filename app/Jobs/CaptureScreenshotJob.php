<?php
namespace App\Jobs;

use App\Models\ScreenshotLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Models\ScreenshotHistory;
use App\Models\CompanySiteScreenshot;

class CaptureScreenshotJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    protected $directoryPath;
    protected $file_name;
    protected $capture_id;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @param string $url
     * @param string $directoryPath
     * @param string $file_name
     * @param int $capture_id
     * @param string $type ('company' or 'professional')
     */
    public function __construct($url, $directoryPath, $file_name, $capture_id, $type)
    {
        $this->url = $url;
        $this->directoryPath = $directoryPath;
        $this->file_name = $file_name;
        $this->capture_id = $capture_id;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            // Create the directory if it does not exist
            if (!File::exists($this->directoryPath)) {
                File::makeDirectory($this->directoryPath, 0755, true);
            }

            // Full path for the screenshot
            $screenshotPath = $this->directoryPath . '/' . $this->file_name;
            
            // Run Puppeteer script to capture screenshot
            $process = new Process([
                'node', 'library/screenshot-capture/capture.js', $this->url, $screenshotPath
            ]);

            $process->run();

            // Check if the process was successful
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Save screenshot information
            if ($this->type === 'screen_capture') {
                ScreenshotHistory::create([
                    'file_name' => $this->file_name,
                    'capture_id' => $this->capture_id,
                    'added_by' => 0
                ]);
            } elseif ($this->type === 'professional_sites') {
                CompanySiteScreenshot::create([
                    'file_name' => $this->file_name,
                    'professional_site_id' => $this->capture_id,
                    'added_by' => 0
                ]);
            }

        } catch (ProcessFailedException $e) {
            ScreenshotLog::create(['error_log'=>"Screenshot capture failed for URL: {$this->url} - Error: " . $e->getMessage(),'type'=>$this->type]);
        }
    }
}
