<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Domain;
use App\Models\Subdomain;
use App\Models\Nmap;
use App\Models\Nuclei;
use App\Models\Screenshot;
use App\Models\Directory;
use App\Models\HttpEnableSubdomain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use App\Jobs\NmapscanJob;
use App\Jobs\NucleiscanJob;
use App\Jobs\DirscanJob;
use App\Jobs\HttpscanJob;
use App\Jobs\CapturescreenshotJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class SubdomainController extends Controller
{
    //
    public function nmapscan($subdomain_name, $domain_id, $subdomain_id){
        $batch = Bus::batch([
            new NmapscanJob($subdomain_name, $domain_id, $subdomain_id),
        ])->then(function (Batch $batch) {
            // All jobs completed successfully...
        })->catch(function (Batch $batch, Throwable $e) {
            $failed = 1;
            // First batch job failure detected...
        })->finally(function (Batch $batch) {

            // The batch has finished executing...
        })->name($subdomain_name.'_'.$subdomain_id.'_nmapscanjob')->dispatch();
        echo  "Nmap scan job is ".$batch->id;
        $jobstatus = Subdomain::find($subdomain_id);
        $jobstatus->nmap_jobid = $batch->id;
        $jobstatus->update();
    }

    public function nucleiscan($subdomain_name, $domain_id, $subdomain_id){
        $batch = Bus::batch([
            new NucleiscanJob($subdomain_name, $domain_id, $subdomain_id),
        ])->then(function (Batch $batch) {
            // All jobs completed successfully...
        })->catch(function (Batch $batch, Throwable $e) {
            $failed = 1;
            // First batch job failure detected...
        })->finally(function (Batch $batch) {

            // The batch has finished executing...
        })->name($subdomain_name.'_'.$subdomain_id.'_nucleiscanjob')->dispatch();
    
    echo  "Nuclei scan job is ".$batch->id;
    $jobstatus = Subdomain::find($subdomain_id);
    $jobstatus->nuclei_jobid = $batch->id;
    $jobstatus->update();
    }

    public function checkhttp($subdomain_name, $domain_id, $subdomain_id, $project_id){
        $batch = Bus::batch([
            new HttpscanJob($subdomain_name, $domain_id, $subdomain_id, $project_id),
        ])->then(function (Batch $batch) {
            // All jobs completed successfully...
        })->catch(function (Batch $batch, Throwable $e) {
            $failed = 1;
            // First batch job failure detected...
        })->finally(function (Batch $batch) {

            // The batch has finished executing...
        })->name($subdomain_name.'_'.$subdomain_id.'_httpscanjob')->dispatch();
    
    echo  "HTTP scan job is ".$batch->id;
    // $jobstatus = Subdomain::find($subdomain_id);
    // $jobstatus->nuclei_jobid = $batch->id;
    // $jobstatus->update();
    }

    public function dirscan($subdomain_name, $domain_id, $subdomain_id, $filename){
        $batch = Bus::batch([
            new DirscanJob($subdomain_name, $domain_id, $subdomain_id, $filename),
        ])->then(function (Batch $batch) {
            // All jobs completed successfully...
        })->catch(function (Batch $batch, Throwable $e) {
            $failed = 1;
            // First batch job failure detected...
        })->finally(function (Batch $batch) {

            // The batch has finished executing...
        })->name($subdomain_name.'_'.$subdomain_id.'_dirscanjob')->dispatch();
        echo  "Dir scan job is ".$batch->id;
        $jobstatus = Subdomain::find($subdomain_id);
        $jobstatus->dir_jobid = $batch->id;
        $jobstatus->update();
    }

    public function screenshotscan($subdomain_name, $domain_id, $subdomain_id, $project_id){
        $batch = Bus::batch([
            new CapturescreenshotJob($subdomain_name, $domain_id, $subdomain_id, $project_id),
        ])->then(function (Batch $batch) {
            // All jobs completed successfully...
        })->catch(function (Batch $batch, Throwable $e) {
            $failed = 1;
            // First batch job failure detected...
        })->finally(function (Batch $batch) {

            // The batch has finished executing...
        })->name($subdomain_name.'_'.$subdomain_id.'_capturescreenshotjob')->dispatch();
    }

    public function scanall($subdomain_name, $domain_id, $subdomain_id, $filename){
        self::nmapscan($subdomain_name, $domain_id, $subdomain_id);
        self::nucleiscan($subdomain_name, $domain_id, $subdomain_id);
        self::dirscan($subdomain_name, $domain_id, $subdomain_id, $filename);
        self::checkhttp($subdomain_name, $domain_id, $subdomain_id);
        // screenshotscan($domainname);
    }

    public function index($id){
        $subdomain = Subdomain::find($id);
        $subdomain_path = $subdomain->path."nmapscan.nmap";
        if (file_exists($subdomain_path)) {
        $results = file("$subdomain_path");
        }
        else{
            $results = NULL;
        }
        $myfiles = array_diff(scandir(resource_path()."/wordlists"), array('.', '..')); 
        $nuclei_out = Nuclei::where('subdomain_id', $subdomain->id)->latest()->paginate(5);
        $nuclei_jobid = $subdomain->nuclei_jobid;
        $nmap_jobid = $subdomain->nmap_jobid;
        $dir_jobid = $subdomain->dir_jobid;
        // $test = Subdomain::find($id)->http_enable_subdomains;
        if(file_exists($subdomain->path."gobuster.txt")){
            $dirout = file($subdomain->path."gobuster.txt");
        }
        else{
            $dirout = [];
        }
        $screenshots = Screenshot::where('subdomain_id', $subdomain->id)->get();

        return view("subdomain.index", ['subdomain'=> $subdomain, 'nucleis'=>$nuclei_out, 'nmapoutput'=>$results, 'nuclei_jobid'=> $nuclei_jobid, 'nmap_jobid'=>$nmap_jobid,'dir_jobid'=>$dir_jobid,'filelists'=>$myfiles, 'diroutput'=>$dirout, 'screenshots'=>$screenshots]);
    }

    public function scan(){
        $domain = Domain::find(request()->domain_id);
        $project_id = $domain->project_id;
        $subdomain = new Subdomain;
        $screenshot = new Screenshot;
        $subdomain_name = request()->subdomain_name;
        $domain_id = request()->domain_id;
        $subdomain_id = request()->subdomain_id;
        $subdomain_path = $domain->path.$subdomain_name.'/';
        $domain_name = $subdomain::find($subdomain_id)->domain_id;
        $domain_name = Domain::find($domain_name)->domain_name;
        $filename = request()->filename ?? "";

        if (!file_exists($subdomain_path)){
            mkdir($subdomain_path, 0777);
        }

        if(request()->option == 1) // scan all
        {
            self::scanall($subdomain_name, $domain_id, $subdomain_id, $filename);
        }

        else if(request()->option == 2) // nmap scan
        {
            self::nmapscan($subdomain_name, $domain_id, $subdomain_id);
        }
        else if(request()->option == 3) // dirscan
        {
            self::dirscan($subdomain_name, $domain_id, $subdomain_id, $filename);
        }
        else if(request()->option == 4) //nuclei scan
        {
            self::nucleiscan($subdomain_name, $domain_id, $subdomain_id);
        }
        else if(request()->option == 5) //check web server 
        {
            self::checkhttp($subdomain_name, $domain_id, $subdomain_id, $project_id);
        }
        else if(request()->option == 6) //screen capture
        {
            self::screenshotscan($subdomain_name, $domain_id, $subdomain_id, $project_id);
        }
        return back();
    }

    public function delete($id){
        $subdomain = Subdomain::find($id);
        $path = $subdomain->path;
        if (PHP_OS === 'Windows')
        {
            exec(sprintf("rd /s /q %s", escapeshellarg($path)));
        }
        else
        {
            exec(sprintf("rm -rf %s", escapeshellarg($path)));
        }
        $nuclei = Nuclei::where('subdomain_id','=',$id);
        $nuclei->delete();
        $subdomain->delete();  
        return back();
    }
}
