<?php

namespace App\Console\Commands;

use App\Services\TokenProcessService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TokenProcesses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'token:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all token`s status and refund';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Started Updating ');
            $val = TokenProcessService::ExpireToken();
            $this->info('Added successfully.....');
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
            Log::channel('commands')->error('[' . $this->signature . '] ' . $this->description . ' FAILS. ' . $th->getMessage());
        }
        return Command::SUCCESS;
    }
}
