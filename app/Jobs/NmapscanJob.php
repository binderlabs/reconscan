<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Models\Subdomain;
use App\Models\Nuclei;
use App\Models\Nmap;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NmapscanJob implements ShouldQueue
{
    use Batchable,Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subdomain_name, $domain_id, $subdomain_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subdomain_name, $domain_id, $subdomain_id)
    {
        //
        $this->subdomain_name = $subdomain_name;
        $this->domain_id = $domain_id;
        $this->subdomain_id = $subdomain_id;
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

        $subdomain_path = Subdomain::find($this->subdomain_id);
        $path = $subdomain_path->path;
        echo $path;
        $domain_name = $subdomain::find($subdomain_id)->domain_id;
        $domain_name = Domain::find($domain_name)->domain_name;

        $output_file = $path."nmapscan";
        $command = "nmap -sC -sV -sT ".$subdomain_name." -oA ".$output_file;
        
        error_log($command);
        error_log("Nmap Scanning");
        exec($command);
        error_log("Nmap scan complete");
        // $results = file("$output_file", FILE_IGNORE_NEW_LINES);
        // if(filesize($output_file) > 1){
        //     foreach($results as $jsonoutput){
        //         $result = json_decode($jsonoutput, true);
        //         $template_id = $result["template-id"];
        //         $name = $result["info"]["name"];
        //         $severity = $result["info"]["severity"];
        //         $type = $result["type"];
        //         $host = $result["host"];
        //         $matched_at = $result["matched-at"];
        //         $version_info = "";
        //         $description = "";
        //         $matcher_name = "";
        //         $curl_command = "";

        //         if(array_key_exists('curl-command', $result)){
        //             $curl_command = $result["curl-command"];
        //         }
        //         else{
        //             $curl_command = "-";
        //         }
        //         if(array_key_exists('matcher-name', $result)){
        //             $matcher_name = $result["matcher-name"];
        //         }
        //         else{
        //             $matcher_name = "-";
        //         }

        //         if(array_key_exists('description', $result["info"])){
        //             $description = $result["info"]["description"];
        //         }
        //         else{
        //             $description = "-";
        //         }

        //         if(array_key_exists('extracted-results', $result)){
        //             $extracted_results = $result["extracted-results"];
        //             foreach($extracted_results as $extracted_result)
        //             {
        //                 $version_info = $version_info.','.$extracted_result;
                        
        //             }
        //             $version_info = substr($version_info, 1);
        //         }
        //         else{
        //             $version_info = "-";
        //         }
        //         $nuclei = new Nuclei;
        //         $nuclei->domain_id = $domain_id;
        //         $nuclei->subdomain_id = $subdomain_id;
        //         $nuclei->template_id = $template_id;
        //         $nuclei->name = $name;
        //         $nuclei->severity = $severity;
        //         $nuclei->type = $type;
        //         $nuclei->host = $host;
        //         $nuclei->matched_at = $matched_at;
        //         $nuclei->version_info = $version_info;
        //         $nuclei->description = $description;
        //         $nuclei->matcher_name = $matcher_name;
        //         $nuclei->curl_command = $curl_command;
        //         $nuclei->save();

        // }
    // }
}
}