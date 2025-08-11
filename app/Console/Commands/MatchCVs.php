<?php

namespace App\Console\Commands;

use App\Models\JobApp;
use Illuminate\Console\Command;

class MatchCVs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'match-cvs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Match CVs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
//        $application = JobApp::where('close', false)->whereHas('dwqdwq',  function (){
//
//        })->get();
    }
}
