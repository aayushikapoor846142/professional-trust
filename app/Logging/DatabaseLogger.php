<?php 

namespace App\Logging;

use Illuminate\Support\Facades\DB;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;  // For setting the log level
use Monolog\Logger;
use Monolog\LogRecord;

class DatabaseLogger extends AbstractProcessingHandler
{
    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
    }

    protected function write(array $record): void
    {
        // Ensure the error_logs table is structured to match these columns
        DB::table('error_logs')->insert([
            'level' => $record->level->getName(), // Updated method to get the level name
            'message' => $record->message,
            'context' => json_encode($record->context),
            'created_at' => now(),
        ]);
    }
}
