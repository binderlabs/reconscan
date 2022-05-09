<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Domain;
use App\Models\Subdomain;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\NmapscanJob;
use App\Jobs\NucleiscanJob;
use App\Jobs\SubdomainscanJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class ScannerController extends Controller
{
    public function scan(){
        $data = "";
        $result ="";
        if(request()->option == 1) //subdomain recon
        {
            $data = request()->domain;
            $results = $this->domainscan(request()->domain);
            $domain = new Domain;
            $domain->project_id = request()->project_id;
            $domain->domain_name = request()->domain;
            $domain->save();
            foreach($results as $result){
                $subdomain = new Subdomain;
                $subdomain->domain_id = $domain->id;
                $subdomain->subdomain_name = $result;
                $subdomain->save();
            }
        }
        return back();
    }
    public function subdomainscan(){
        $domain_id = request()->domain_id;
        $project_id = request()->project_id;

        $domain = Domain::find($domain_id);
        if(request()->option == 1) //subdomain recon
        {
            error_log($domain->path);
            $batch = Bus::batch([
                new SubdomainscanJob(request()->domain, $domain->path, $domain_id, $project_id),
            ])->then(function (Batch $batch) {
                // All jobs completed successfully...
            })->catch(function (Batch $batch, Throwable $e) {
                $failed = 1;
                // First batch job failure detected...
            })->finally(function (Batch $batch) {

                // The batch has finished executing...
            })->name(request()->domain.'_'.request()->domain_id.'_subdomainscanjob')->dispatch();
        }

        error_log($batch->id);
        
        return back();
    }
}