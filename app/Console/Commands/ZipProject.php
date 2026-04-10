<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class ZipProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backupcode:zip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zip the Laravel project directory';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            ini_set('memory_limit', '-1');
            // $rootPath = base_path();
            // $date = date("Y-m-d-H-i-s");
            $base_path = storage_path('app/public/code-backup');

            if (!is_dir($base_path)) {
                mkdir($base_path, 0777, true);
            }
            $date = date("Y-m-d");
            $base_path = $base_path . "/" . $date;
            if (!is_dir($base_path)) {
                mkdir($base_path, 0777, true);
            }

            // $zipfile = 'code-' . $date . ".zip";
            // $output = $base_path . '/' . $zipfile;

            // $zip = new ZipArchive();
            // if ($zip->open($output, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            //     $this->error("Cannot open <$output>");
            //     return 1;
            // }

            // $files = new RecursiveIteratorIterator(
            //     new RecursiveDirectoryIterator($rootPath),
            //     RecursiveIteratorIterator::LEAVES_ONLY
            // );

            // foreach ($files as $name => $file) {
            //     if (!$file->isDir()) {
            //         $filePath = $file->getRealPath();
            //         $relativePath = substr($filePath, strlen($rootPath) + 1);

            //         if (strpos($relativePath, 'vendor' . DIRECTORY_SEPARATOR) === 0 ||
            //             strpos($relativePath, 'storage/app/public/code-backup' . DIRECTORY_SEPARATOR) === 0 ||
            //             strpos($relativePath, 'old' . DIRECTORY_SEPARATOR) === 0 ||
            //             strpos($relativePath, '.zip') === 0) {
            //             continue;
            //         }
            //         $zip->addFile($filePath, $relativePath);
            //     }
            // }

            // // Close the zip file to finalize it
            // $zip->close();
            // $this->info("Project zipped successfully as $output");

            
            // Backup the database
            // $database = Config::get('database.connections.mysql.database');
            // $username = Config::get('database.connections.mysql.username');
            // $password = Config::get('database.connections.mysql.password');
            // $host = Config::get('database.connections.mysql.host');
            $data = configData();
            $database = decrypt($data['x']);
            $username =  decrypt($data['y']);
            $password =  decrypt($data['z']);
            $host =  decrypt($data['v']);
            $dumpPath = $base_path . "/" . $database . ".sql";

            // Use full path for mysqldump command
            $command = "mysqldump -u $username -p$password -h $host $database > $dumpPath";
            exec($command);


            
            // Upload the database backup to AWS
            $database_file = $database . "-" . date("Y-m-d-H-i-s"). ".sql";
            $res = awsBackupCode(config('awsfilepath.database_backup') . "/" . $database_file, $dumpPath);
            echo "Database\n\n";
            unlink($dumpPath);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

}
