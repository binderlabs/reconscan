<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Models\Subdomain;
use App\Models\Nuclei;
use App\Models\Nmap;
use App\Models\Directory;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DirscanJob implements ShouldQueue
{
    use Batchable,Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subdomain_name, $domain_id, $subdomain_id, $filename;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subdomain_name, $domain_id, $subdomain_id, $filename)
    {
        //
        $this->subdomain_name = $subdomain_name;
        $this->domain_id = $domain_id;
        $this->subdomain_id = $subdomain_id;
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...
            return;
        }
        $domain = Domain::find($this->domain_id);
        $subdomain = new Subdomain;
        $subdomain_name = $this->subdomain_name;
        $domain_id = $this->domain_id;
        $subdomain_id = $this->subdomain_id;

        $subdomainpath = Subdomain::find($this->subdomain_id);
        $subdomain_path = $subdomainpath->path;
        $domain_name = $subdomain::find($subdomain_id)->domain_id;
        $domain_name = Domain::find($domain_name)->domain_name;
        $output_file = $subdomain_path."gobuster.txt";
        echo "Subdomain path is ".$subdomain_path;
        $filename = $this->filename;
        echo $filename;
        $command = "gobuster dir --wildcard -t 50 -e -u ".$subdomain_name." -w ".resource_path()."/wordlists/".$filename." -o ".$output_file;
        error_log($command);
        error_log("Scanning gobuster");
        exec($command);
        error_log("Scanning complete");
    }
}
