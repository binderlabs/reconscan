<?php

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

class SubdomainscanJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $domainname;
    protected $path;
    protected $domain_id;
    protected $project_id;
    // protected $subdomain;
    // protected $subd;
    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($domainname, $path, $domain_id, $project_id)
    {
        //
        $this->domainname = $domainname;
        $this->path = $path;
        $this->domain_id = $domain_id;
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
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...
            return;
        }
        $command = "subfinder -d ".$this->domainname." -o ".$this->path."subfinderout.txt";
        $command2 = "httpx -l ".$this->path."subfinderout.txt -title -content-length -status-code -silent -vhost -ports 21-23,25-26,37,53,79-82,88,100,106,110-111,113,119,135,139,143-144,179,199,254-255,280,311,389,427,443-445,464-465,497,513-515,543-544,548,554,587,593,625,631,636,646,787,808,873,902,990,993,995,1000,1022,1024-1033,1035-1041,1044,1048-1050,1053-1054,1056,1058-1059,1064-1066,1069,1071,1074,1080,1110,1234,1433,1494,1521,1720,1723,1755,1761,1801,1900,1935,1998,2000-2003,2005,2049,2103,2105,2107,2121,2161,2301,2383,2401,2601,2717,2869,2967,3000-3001,3128,3268,3306,3389,3689-3690,3703,3986,4000-4001,4045,4899,5000-5001,5003,5009,5050-5051,5060,5101,5120,5190,5357,5432,5555,5631,5666,5800,5900-5901,6000-6002,6004,6112,6646,6666,7000,7070,7937-7938,8000,8002,8008-8010,8031,8080-8081,8443,8888,9000-9001,9090,9100,9102,9999-10001,10010,32768,32771,49152-49157,50000 -json -o ".$this->path."httpDomain.txt -threads 100";
        $filename = $this->path."subfinderout.txt";
        $subdomain = new Subdomain();
        $subdomain->domain_id = $this->domain_id;
        $subdomain->project_id = $this->project_id;
        $subdomain->subdomain_name = $this->domainname;
        $subdomain->path = $this->path.$this->domainname."/";
        if (!file_exists($this->path.$this->domainname."/")){
        $subdomain->save();
            mkdir($this->path.$this->domainname."/", 0777);
        }

        exec($command);
        $results = file("$filename", FILE_IGNORE_NEW_LINES);
        foreach($results as $result){
            $domain = Domain::find($this->domain_id);
            $subdomain_path = $domain->path.$result.'/';
            // if (!file_exists($subdomain_path)){
            //     mkdir($subdomain_path, 0777);
            // }

            $subdomain = new Subdomain();
            $subdomain->domain_id = $this->domain_id;
            $subdomain->subdomain_name = $result;
            $subdomain->path = $subdomain_path;
            if (!file_exists($subdomain_path)){
            mkdir($subdomain_path, 0777);
            $subdomain->save();
            }
            else {
                echo "Already scanned";
            }
        }
        echo "\n";
        echo "Now scanning for http enabled domain";
        exec($command2);

        $httpx_path = $this->path."httpDomain.txt";
        $http_enabledresults = file("$httpx_path", FILE_IGNORE_NEW_LINES);
        // print_r($http_enabledresults);
            foreach($http_enabledresults as $jsonoutput){
                $result = json_decode($jsonoutput, true);

                if(array_key_exists('host', $result)){
                    $host = $result["host"];
                }
                else{
                    $host = "-";
                }
                if(array_key_exists('url', $result)){
                    $url = $result["url"];
                }
                else{
                    $url = "-";
                }
                if(array_key_exists('status-code', $result)){
                    $statuscode = $result["status-code"];
                }
                else{
                    $statuscode = "-";
                }
                if(array_key_exists('title', $result)){
                    $title = $result["title"];
                }
                else{
                    $title = "-";
                }
                if(array_key_exists('content-length', $result)){
                    $content_length = $result["content-length"];
                }
                else{
                    $content_length = "-";
                }
                $httpenabled = 1;
                if(array_key_exists('location', $result)){
                    $location = $result["location"];
                }
                else{
                    $location = "-";
                }
                if(array_key_exists('webserver', $result)){
                    $server = $result["webserver"];
                }
                else{
                    $server = "-";
                }
                if(array_key_exists('vhost', $result)){
                    $vhost = $result["vhost"];
                }
                else{
                    $vhost = 0;
                }
                $input = $result["input"];
                $subdomain_id = new Subdomain();
                $id = $subdomain_id::where("subdomain_name", $input)->first();
                $idforhttpenabled = $id->id;
                $project_id = $this->project_id;
                echo $idforhttpenabled;
                $domainid = $this->domain_id;
                // $subdomain_id = DB::table('subdomains')->where('subdomain_name','=',$input)->get();
                // echo $subdomain_id->id;
                // $subdomain = DB::table('subdomains')->where('subdomain_name','=',$input)->update([
                //     'httpenabled'   => $httpenabled,
                //     'title'         => $title,
                //     'url'           => $url,
                //     'location'      => $location,
                //     'host'          => $host,
                //     'server'        => $server,
                //     'statuscode'    => $statuscode,
                //     'vhost'         => $vhost
                // ]);

                // echo $subdomain;
                $httpenableddomain = DB::table('http_enable_subdomains')->insert([
                        'domain_id'     => $domainid,
                        'subdomain_id'  => $idforhttpenabled,
                        'project_id'    => $project_id,
                        'httpenabled'   => $httpenabled,
                        'title'         => $title,
                        'url'           => $url,
                        'location'      => $location,
                        'host'          => $host,
                        'server'        => $server,
                        'statuscode'    => $statuscode,
                        'vhost'         => $vhost
                    ]);
            }

    }
}
//{"timestamp":"2022-04-15T16:25:31.443364+08:00","scheme":"http","port":"80","path":"/","body-sha256":"39dd202d40bfb8f31078d7a4208fc1fd9fe59330adf6f6f1c0ddbc0766f858dc","header-sha256":"6e68fd9dd3c243b5290719cd9ff215f4144fee1b6acb9b1b4acc4c2d9bd253dc","url":"http://api-mytune.telenor.com.mm:80","input":"api-mytune.telenor.com.mm","content-type":"text/html","method":"GET","host":"103.255.172.222","content-length":484,"status-code":200,"response-time":"111.273783ms","failed":false}