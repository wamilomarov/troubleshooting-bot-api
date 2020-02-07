<?php

namespace App\Console\Commands;

use App\Models\TechnicalAnswer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PopulateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:populate';

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
        DB::connection('mysql')->table('CSR_Notes')->orderBy("PK_CSR_ID")
            ->chunk(100, function ($tas) {
                foreach ($tas as $ta) {
                    TechnicalAnswer::query()
                        ->updateOrCreate(['csr_id' => $ta->PK_CSR_ID],
                            [
                                'slogan' => $ta->CSR_SLOGAN,
                                'problem_description' => $ta->DescrText,
                                'solution_description' => $ta->FaText
                            ]);
                    echo $ta->PK_CSR_ID . " DONE.\n";
                }
            });
    }
}
