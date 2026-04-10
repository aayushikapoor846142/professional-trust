<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteLocalAwsFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-local-aws-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Local Aws File that user downloads';

    /**
     * Execute the console command.
     */
    /**
     * Deletes all files in the 'public/aws-files' folder.
     *
     * This command checks if the 'public/aws-files' folder exists, and if it does, it deletes all the files in that folder.
     * If the folder is already empty, it outputs a message indicating that. If the folder does not exist, it outputs an error message.
     */
    public function handle()
    {
         
         $folderPath = 'public/aws-files';

        if (Storage::exists($folderPath)) {
            
            $files = Storage::allFiles(directory: $folderPath);

            if (!empty($files)) {
               
                foreach ($files as $file) {
                    Storage::delete($file);
                }
                $this->info('All files in the aws-files folder have been deleted.');
            } else {
                $this->info('The aws-files folder is already empty.');
            }
        } else {
            $this->error('The aws-files folder does not exist.');
        }
    }
}
