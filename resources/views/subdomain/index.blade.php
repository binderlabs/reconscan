@extends("layouts.app")

@section("content")
<style>
    #select2{
   display: none;
}
</style>
<div class="container">
    <h5 class="card-title"><a href="/domain/{{$subdomain->domain_id}}" class="btn btn-primary"><i class="bi bi-arrow-left-circle-fill"></i>  back</a>  {{$subdomain->subdomain_name}} </h5>
    <br>
    <form method="post" action="/subdomain/scan" class="row g-2">
        @csrf
        <div class="col-auto">
            <label class="input-group-text" for="option">Options</label>
        </div>
        <div class="col-auto">
            <select class="form-select" id="select1" name="option">
                <option selected value="1">All</option>
                <option value="2">Nmap</option>
                <option value="3">Directory</option>
                <option value="4">Nuclei</option>
                <option value="5">HTTP Enabled</option>
                <option value="6">Screenshot</option>
            </select>
        
            <select class="form-select" id="select2" name="filename">
                @foreach($filelists as $filelist)
                <option selected value="{{$filelist}}">{{$filelist}}</option>
                @endforeach
            </select>
        
        </div>
        
        
        <div class="col-auto">
            <input type="hidden" name="subdomain_name" value="{{$subdomain->subdomain_name}}">
            <input type="hidden" name="domain_id" value="{{$subdomain->domain_id}}">
            <input type="hidden" name="subdomain_id" value="{{$subdomain->id}}">
            <input type="submit" value="scan" class="btn btn-primary">
        </div>
    </form>
    <br>
    @if(isset($subdomain->http_enable_subdomains))
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Web Server Information</h5>
            <div class="overflow-auto">
            <div class="table-responsive" style="max-height:400px;">
                <table class="table">
                <thead>
                    <tr>
                    <th scope="col">Url</th>
                    <th scope="col">IP</th>
                    <th scope="col">Location</th>
                    <th scope="col">Title</th>
                    <th scope="col">Server</th>
                    <th scope="col">Status Code</th>
                    <th scope="col">vhost</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach($subdomain->http_enable_subdomains as $http_enable_subdomain)
                    <tr>
                    <td> <a href="{{$http_enable_subdomain->url}}" target="_blank">{{$http_enable_subdomain->url}}</a> </td>
                    <td> {{$http_enable_subdomain->host}}</td>
                    <td> {{$http_enable_subdomain->location}}</td>
                    <td> {{$http_enable_subdomain->title}}</td>
                    <td> {{$http_enable_subdomain->server}}</td>
                    <td> {{$http_enable_subdomain->statuscode}}</td>
                    @if($http_enable_subdomain->vhost == 1)
                    <td> true </td>
                    @else
                    <td> false </td>
                    @endif
                    </tr>
                    @endforeach
                   
                </tbody>
                </table>
            </div>
            </div>   
        </div>
    </div>
    @endif

    <br>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Nmap Result</h5>
            @if(isset($nmapoutput))
            <h6 class="card-subtitle mb-2 text-muted" id="nmap_scantime"></h6>
            <div class="overflow-auto">
            <div class="table-responsive" style="max-height:400px;">
                <table class="table">
                <thead>
                    <tr>
                    <th scope="col">result</th>
                    </tr>
                </thead>
                <p id="nmapscan"></p>
                <tbody>
                    <tr>
                    <td>
                    <code>
                        @foreach($nmapoutput as $output) 
                            {{$output}}<br>
                        @endforeach
                    </code>

                         
                    </td>
                    </tr>
                </tbody>
                </table>
            </div>
            </div>   
            @else
            <h6>Scan to show in here</h6>
            @endif
        </div>
    </div>

    <br>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Nuclei Result</h5>
            <h6 class="card-subtitle mb-2 text-muted" id="nuclei_scantime"></h6>
           
            @if(isset($nucleis))
            {{$nucleis->links()}}
            <div class="table-responsive-sm">
                <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                    <th scope="col">template_id</th>
                    <th scope="col">severtiy</th>
                    <th scope="col">rule name</th>
                    <th scope="col">matches</th>
                    <th scope="col">type</th>
                    <th scope="col">url</th>
                    <th scope="col">version</th>
                    
                    <th scope="col">curl command</th>
                    </tr>
                </thead>
                <p id="nucleiscan"></p>
                <tbody>
                @foreach($nucleis as $nuclei)
                    <tr>
                    <td>{{$nuclei->template_id}}</td>
                    @if ($nuclei->severity == "info")
                    <td class="text-primary">{{$nuclei->severity }}</td>
                    @elseif ($nuclei->severity == "medium")
                    <td class="text-warning">{{$nuclei->severity }}</td>
                    @elseif ($nuclei->severity == "high")
                    <td class="text-danger">{{$nuclei->severity }}</td>
                    @else
                    <td class="">{{$nuclei->severity }}</td>
                    @endif
                    <td>{{$nuclei->name}}</td>
                    <td>{{$nuclei->matcher_name}}</td>
                    <td>{{$nuclei->type}}</td>
                    <td>{{$nuclei->matched_at}}</td>
                    <td>{{$nuclei->version_info}}</td>
                    <td>{{$nuclei->curl_command}}</td>
                    </tr>
                @endforeach
                </tbody>
                </table>
            </div>
            @else
            <h6>Scan to show in here</h6>
            @endif
        </div>
    </div>

    <br>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Directory Scanning Result</h5>
            <h6 class="card-subtitle mb-2 text-muted" id="dir_scantime"></h6>
            <div class="overflow-auto">
            <div class="table-responsive" style="max-height:400px;">
                <table class="table">
                <thead>
                    <tr>
                    <th scope="col">output</th>
                    </tr>
                </thead>
                <p id="dirscan"></p>
                <tbody>
                    <tr>
<td>

<code>
<pre>
@forelse($diroutput as $dirout)
{{$dirout}}
@empty
no result or not scanned
@endforelse
</pre>
<code>
</td>
                    </tr>
                </tbody>
                </table>
            </div>
</div>
        </div>
    </div>


<div class="card">
    <div class="card-body">
             <h5 class="card-title">Screenshot Results</h5>
             <br>
             @if(isset($screenshots))
                @foreach($screenshots as $screenshot)
                <h5>{{$screenshot->url}}</h5>
                <img src="{{$screenshot->img_path}}" class="img-fluid" alt="{{$screenshot->url}}">
                <br>
                @endforeach
             @endif
    </div>
</div>
    
</div>
<script>
function checknuclei(){
var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       jobstatus = JSON.parse(xhttp.responseText);
       finished_scan = jobstatus["processedJobs"];
       scanned_time = jobstatus["finishedAt"];
       pendingJobs = jobstatus["pendingJobs"];
       var d = new Date(scanned_time);
       failed_scan = jobstatus["failedJobs"];
       if(finished_scan == 1){
        document.getElementById("nuclei_scantime").innerHTML = 'Scanned at ' + d.toUTCString();
       }
       else if (failed_scan == 1){
        document.getElementById("nucleiscan").innerHTML = '<p class="text-danger">scan failed, please rescan</p>';
       }
       else{
        document.getElementById("nucleiscan").innerHTML = '<div class="spinner-border text-primary" role="status" id="nmap_spinner"><span class="visually-hidden">Loading...</span></div> Scanning';
       }
    }
};

@if(isset($nuclei_jobid))
var nucleijobid = "{{$nuclei_jobid}}";
@else
var nucleijobid = 0;
@endif
console.log(nucleijobid);
console.log("Nuclei job is "+nucleijobid);
xhttp.open("GET", "{{url("/status/")}}"+"/"+nucleijobid, true);
xhttp.send();

}

function checknmap(){
var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       jobstatus = JSON.parse(xhttp.responseText);
       finished_scannmap = jobstatus["processedJobs"];
       scanned_time = jobstatus["finishedAt"];
       var d = new Date(scanned_time);
       failed_scan = jobstatus["failedJobs"];
       pendingJobs = jobstatus["pendingJobs"];
       if(finished_scannmap == 1){
        document.getElementById("nmap_scantime").innerHTML = 'Scanned at ' + d.toUTCString();
       }
       else if (failed_scan == 1){
        document.getElementById("nmapscan").innerHTML = '<p class="text-danger">scan failed, please rescan</p>';
       }
       else{
        document.getElementById("nmapscan").innerHTML = '<div class="spinner-border text-primary" role="status" id="nmap_spinner"><span class="visually-hidden">Loading...</span></div> Scanning';
       }
    }
};
@if(isset($nmap_jobid))
var nmap_jobid = "{{$nmap_jobid}}";
@else
var nmap_jobid = 0;
@endif
console.log("Nmap job is "+nmap_jobid);
xhttp.open("GET", "{{url("/status/")}}"+"/"+nmap_jobid, true);
xhttp.send();
}

function checkdir(){
var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
       // Typical action to be performed when the document is ready:
       jobstatus = JSON.parse(xhttp.responseText);
       finished_scannmap = jobstatus["processedJobs"];
       scanned_time = jobstatus["finishedAt"];
       var d = new Date(scanned_time);
       failed_scan = jobstatus["failedJobs"];
       pendingJobs = jobstatus["pendingJobs"];
       if(finished_scannmap == 1){
        document.getElementById("dir_scantime").innerHTML = 'Scanned at ' + d.toUTCString();
       }
       else if (failed_scan == 1){
        document.getElementById("dirscan").innerHTML = '<p class="text-danger">scan failed, please rescan</p>';
       }
       else{
        document.getElementById("dirscan").innerHTML = '<div class="spinner-border text-primary" role="status" id="nmap_spinner"><span class="visually-hidden">Loading...</span></div> Scanning';
       }
    }
};
@if(isset($dir_jobid))
var dir_jobid = "{{$dir_jobid}}";
@else
var dir_jobid = 0;
@endif
console.log("Dir job is "+dir_jobid);
xhttp.open("GET", "{{url("/status/")}}"+"/"+dir_jobid, true);
xhttp.send();
}

// function hideSpinner() {
//     document.getElementById('nmap_spinner').style.display = 'none';
// }

checknuclei();
checknmap();
checkdir();
$("#select1").change(function(){
    if($(this).val() == 3){
      $("#select2").show();
    }else{
      $("#select2").hide();
    }

});

</script>
<!-- {{$subdomain}} -->
@endsection