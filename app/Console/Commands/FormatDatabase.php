<?php

namespace App\Console\Commands;

use App\Models\TechnicalAnswer;
use Illuminate\Console\Command;

class FormatDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:format';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (TechnicalAnswer::all()->chunk(100) as $technicalAnswers) {
            foreach ($technicalAnswers as $technicalAnswer) {
                $technicalAnswer->csr_id = preg_replace('/[^0-9.]+/', '', $technicalAnswer->csr_id);
                $technicalAnswer->save();
            }
            echo "\ndone 100\n\n";
        }
        echo "DONE ALL";
    }
}
