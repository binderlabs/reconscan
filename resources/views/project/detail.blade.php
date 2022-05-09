@extends("layouts.app")

@section("content")

<div class="container">
    <div class="card">
        <div class="card-body">
            @if(isset($project))
            {{$project->title}}
            @endif
        </div>
    </div>
    <br>
    <!-- <h5> SCAN</h5> -->
    <!-- <form method="post" action="{{url('/scan')}}">
        @csrf
        <div class="input-group mb-3">        
            <label class="input-group-text" for="option">Options</label>
            <select class="form-select" id="option" name="option">
                <option selected>All</option>
                <option value="1">Subdomain</option>
                <option value="2">Directory</option>
                <option value="3">Nuclei</option>
            </select>
        </div>
        <div class="input-group">
            <input type="hidden" name="project_id" value="{{$project->id}}">
            <input type="text" class="form-control" id="newproject" placeholder="Enter domain..." name="domain">
            <input type="submit" value="scan" class="btn btn-primary">
        </div>
    </form> -->
    <br>
    <h5> create domain </h5>
    <form method="post" action="{{url('/newdomain')}}">
        @csrf
        <div class="input-group">
            <input type="hidden" name="project_id" value="{{$project->id}}">
            <input type="text" class="form-control" placeholder="Enter domain..." name="domain">
            <input type="submit" value="create" class="btn btn-primary">
        </div>
    </form>
    <br><br>

    <div class="table-responsive">
        <h5>DOMAINS</h5>
        <div class="row">
        @foreach($domains as $a)
        <div class="col-md-4">
            <div class="card" style="">
            <span class="border border-white">
                <div class="card-body">
                    <h5 class="card-title">{{$a->domain_name}}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">{{$a->created_at->diffForHumans()}}</h6>
                    <a href="/domain/{{$a->id}}" class="card-link">view</a>
                    <a href="/domain/delete/{{$a->id}}" class="card-link link-danger">delete</a>
                </div>
            </span>
            </div>
        <br>
        </div>
        @endforeach
        </div>
        <!-- <div class="card">
            <div class="card-body">
                <div class="list-group">
                @foreach($domains as $a)
                <div class="row">
                <button type="button" class="list-group-item list-group-item-action"><a href="/domain/{{$a->id}}">{{$a->domain_name}}</a></button> 
                {{$a->created_at->diffForHumans()}}
                </div>
                @endforeach
                </div>
            </div>
        </div> -->
        
        <br>
        <br>
    </div>

</div>
@endsection