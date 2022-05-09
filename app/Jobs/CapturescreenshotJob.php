<?php

namespace App\Jobs;

namespace App\Jobs;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Domain;
use App\Models\Subdomain;
use App\Models\HttpEnableSubdomain;
use App\Models\Screenshot;
use Illuminate\Support\Facades\Storage;

class CapturescreenshotJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $subdomain_name;
    protected $domain_id;
    protected $subdomain_id;
    protected $project_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subdomain_name, $domain_id, $subdomain_id, $project_id)
    {
        //
        $this->subdomain_name = $subdomain_name;
        $this->domain_id = $domain_id;
        $this->subdomain_id = $subdomain_id;
        $this->project_id = $project_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $path = Subdomain::find($this->subdomain_id);
        $subdomain_path = $path->path;
        $httpenabled = HttpEnableSubdomain::where("subdomain_id", $this->subdomain_id)->get();
        foreach($httpenabled as $httpsub){
            $url = $httpsub["url"];
            $host = $httpsub["host"];
            $screenshot_url = $subdomain_path.$this->subdomain_name."_".$host."_".mt_rand()."/";
            $command = "/home/EyeWitness/Python/./EyeWitness.py --web --no-prompt --single ".$url." -d ".$screenshot_url;
            echo "Command to run: ".$command;
            echo "Executing";
            exec($command);
            echo "Executed";
            $pics = array_diff(scandir($screenshot_url."screens"), array('.', '..'));
            foreach($pics as $pic){
                echo "File name:".$pic."\n";
                $urlto_encode = $screenshot_url."screens/".$pic;
                echo "URL to encdoe".$urlto_encode."\n";
                $imagedata = file_get_contents($urlto_encode);
                // echo "Img data is :".$imagedata."\n";
                $base64 = 'data:'.mime_content_type($urlto_encode).';base64,'.base64_encode($imagedata);
                // echo "Base64 data is: ".$base64."\n";
                $screenshot = new Screenshot();
                $screenshot->project_id = $this->project_id;
                $screenshot->domain_id = $this->domain_id;
                $screenshot->subdomain_id = $this->subdomain_id;
                $screenshot->url = $url;
                $screenshot->img_path = $base64;
                $screenshot->save();
            }
        }
    }
}
