<?php

namespace App\Http\Controllers;
use App\Models\Project;
use App\Models\Subdomain;
use App\Models\Domain;
use App\Models\Nuclei;
use App\Models\Nmap;
use App\Models\Screenshot;
use Illuminate\Http\Request;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

class ProjectController extends Controller
{
    //
    public function index(){
        $data = Project::all();
        
        return view("project.index", ['projects'=> $data]);
    }
    
    public function create(){
        $project = new Project;
        $project->title = request()->title;
        $path = env('PROJECT_DIR').request()->title.'_'.mt_rand()."/";
        mkdir($path, 0777);
        $project->path = $path;
        $project->save();
        return back();
    }

    public function browse($id){
        $project = Project::find($id);
        $domains = Domain::where('project_id', $project->id)->get();
        error_log($domains);
        return view("project.detail", ['project'=> $project, 'domains'=>$domains]);
    }

    public function delete($id){
        $project = Project::find($id);
        // $domains = Domain::where('project_id', $project->id)->get();
        // $domain = Domain::where('project_id', $project->id);
        // foreach($domains as $domain){
        //     $subdomains = Subdomain::where('domain_id', $domain->id);
        //     $subdomains->delete();
        // }
        $path = $project->path;
        // rmdir($path);
        if (PHP_OS === 'Windows')
        {
            exec(sprintf("rd /s /q %s", escapeshellarg($path)));
        }
        else
        {
            exec(sprintf("rm -rf %s", escapeshellarg($path)));
        }
        // if(isset($domain)){
        //     $domain->delete();
        // }
        $project->delete();
        return redirect('/')->with('info', 'Project deleted');
    }
    
}
