<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Domain;
use App\Models\Subdomain;
use App\Models\Project;

class DomainController extends Controller
{
    //
    public function index($id){
        $domain = Domain::find($id);
        $project = Project::find($domain->project_id);
        $subdomains = $domain->subdomains()->paginate(10);
        $httpxpath = $domain->path."httpDomain.txt";
        if (file_exists($httpxpath)) {
            $httpx = file("$httpxpath");
            }
            else{
                $httpx = NULL;
            }
        
        return view("domain.index", ['domain'=>$domain, 'subdomains'=> $subdomains, 'project'=>$project, 'httpxs'=>$httpx]);
    }

    public function newdomain(){
        $project = Project::find(request()->project_id);
        $domain = new Domain;
        $domain->project_id = request()->project_id;
        $domain->domain_name = request()->domain;
        error_log($project->path);
        $domain->path = $project->path.request()->domain.'_'.mt_rand().'/';
        mkdir($domain->path, 0777);
        $domain->save();
        
        $domainid = Domain::find($domain->id);
        $subdomains = $domainid->subdomains()->paginate(15);
        return back();
    }

    public function delete($id){
        $domain = Domain::find($id);
        $path = $domain->path;
        if (PHP_OS === 'Windows')
        {
            exec(sprintf("rd /s /q %s", escapeshellarg($path)));
        }
        else
        {
            exec(sprintf("rm -rf %s", escapeshellarg($path)));
        }
        $domain->delete();
        return back();
    }
    public function adddomain(){
        $domain = Domain::find(request()->domain_id);
        $project_id = $domain->project_id;
        $subdomain = new Subdomain();
        $subdomain->domain_id = request()->domain_id;
        $subdomain->project_id = $project_id;
        $subdomain->subdomain_name = request()->domain_name;
        $subdomain->path = $domain->path.request()->domain_name."/";
        $subdomain->save();
        if(!file_exists($domain->path.request()->domain_name."/")){
            mkdir($domain->path.request()->domain_name."/", 0777);
        }
        return back();
    }

}
