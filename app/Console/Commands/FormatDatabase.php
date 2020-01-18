<?php

namespace App\Console\Commands;

use App\Models\ContactPerson;
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
                $contactPerson = new ContactPerson([
                    'name' => ltrim(rtrim($technicalAnswer->contact_person_name, ' '), ' '),
                    'username' => ltrim(rtrim($technicalAnswer->contact_person_username, ' '), ' '),
                    'email' => ltrim(rtrim($technicalAnswer->contact_person_email, ' '), ' '),
                ]);

                $technicalAnswer->contact_person()->save($contactPerson);
                $technicalAnswer->unset('contact_person_name');
                $technicalAnswer->unset('contact_person_email');
                $technicalAnswer->unset('contact_person_username');
                $technicalAnswer->save();
                echo "CSR ID: " . preg_replace('/[^0-9.]+/', '', $technicalAnswer->csr_id) . PHP_EOL;
            }
            echo "\ndone 100\n\n";
        }

    }
}
