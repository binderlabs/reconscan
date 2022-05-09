@extends("layouts.app")

@section("content")

<div class="container">
    @if(session('info'))
        <div class="alert alert-info">
            {{ session('info') }} 
        </div>
    @endif
    <form method="post" action="{{url('/project/create')}}">
        @csrf
        <h5>New Project</h5>
            <div class="input-group">
                <input type="text" class="form-control" id="newproject" placeholder="project name" name="title">
                <input type="submit" value="create" class="btn btn-primary">
            </div>
    </form>

</div>
<br>
<div class="container">
    <h5>Existing Project</h5>
    <div class="row">
    @foreach($projects as $project)
    <div class="col-md-4">
        <div class="card" style="">
            <div class="card-body">
                <p class="card-text">{{$project->title}}</p>
                Number of domains: <span class="badge rounded-pill bg-primary">{{$project->domains->count()}}</span>
                <br><br>
                <a href="{{ url("project/$project->id")}}" class="card-link">view</a>
                <a href="{{ url("project/delete/$project->id")}}" class="card-link">delete</a>
            </div>
        </div>
    <br>
    </div>
    @endforeach
    </div>
</div>
@endsection