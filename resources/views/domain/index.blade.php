@extends("layouts.app")

@section("content")

<div class="container">
<div class="card">
        <div class="card-body">
        {{$project->title}}
        </div>
</div>
<br>
<h5>Recon</h5>
<form method="post" action="{{url('/subdomainscan')}}" class="row g-3">
        @csrf
        <div class="col-auto">
            <select class="form-select" id="option" name="option">
                <!-- <option selected>All</option> -->
                <option value="1" selected>Subdomain</option>
                <!-- <option value="2">Directory</option>
                <option value="3">Nuclei</option> -->
            </select>
        </div>
        <div class="col-auto">
            <input type="text" class="form-control" id="newproject" value="{{$domain->domain_name}}" name="domain" readonly>
        </div>
        <div class="col-auto">
            <!-- <input type="hidden" name="option" value="1"> -->
            <input type="hidden" name="domain_id" value="{{$domain->id}}">
            <input type="hidden" name="project_id" value="{{$domain->project_id}}">
            <input type="submit" value="scan" class="btn btn-primary">
        </div>
    </form>
    <br>
    <h5>Add Domain/Subdomain</h5>
    <form method="post" action="{{url('/subdomain/add')}}" class="row g-3">
        @csrf
        <div class="col-auto">
            <input type="text" class="form-control" id="newdomain" name="domain_name">
        </div>
        <div class="col-auto">
            <!-- <input type="hidden" name="option" value="1"> -->
            <input type="hidden" name="domain_id" value="{{$domain->id}}">
            <input type="submit" value="add" class="btn btn-primary">
        </div>
    </form>
    <br>
    <h5>Results</h5>
    <div class="card">
        <div class="card-body">
        <h5 class="card-title"><a href="/project/{{$domain->project_id}}" class="btn btn-primary"><i class="bi bi-arrow-left-circle-fill"></i>  back</a>  {{$domain->domain_name}}'s subdomains</h5>
        <div class="table-responsive">
        <table class="table table-hover">
        <thead>
                    <tr>
                    <th scope="col">name</th>
                    <th scope="col">HTTPenabled</th>
             
                    </tr>
        </thead>
        <tbody>
        @foreach($subdomains as $subdomain)
                <!-- <caption>List of subdomains</caption> -->
                <tr>
                    <td scope="row"><a href="/subdomain/{{$subdomain->id}}" class="link-secondary">{{$subdomain->subdomain_name}}</a>
                    <!-- <a href="{{$subdomain->subdomain_name}}"><i class="bi bi-box-arrow-up-right"></i></a> -->
                    </td>
                    @if(isset($subdomain->http_enable_subdomains))
                        @if($subdomain->http_enable_subdomains->first())
                        <td>
                            yes
                        </td>
                        @else
                        <td>
                            no
                        </td>
                        @endif
                    @endif

                    <td><a href="{{url("/subdomain/delete/$subdomain->id")}}" class="text-opacity-25 link-danger"><i class="bi bi-trash"></i></a></td>
                </tr>
                <!-- <tr>
                    <td>{{$subdomain->subdomain_name}}</td>
                </tr> -->
                
        @endforeach
        </tbody>
        </table>
        </div>

        {{$subdomains->links()}}
        </div>
    </div>
    <br>
</div>

@endsection