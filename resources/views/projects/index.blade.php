@extends('layouts.app')

@section('content')

    <div class=" container-fluid">
        <div class="pagetitle">
            <h1>Data Tables</h1>
            <nav>
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item">Tables</li>
                <li class="breadcrumb-item active">Data</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <form method="GET" action="{{ route('projects.list') }}">
            <div class="row mb-3">

                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <input type="date" name="date" class="form-control">
                </div>
                
                @if(auth()->user()->role->name == 'admin')

                <div class="col-md-3">
                    <input type="text" name="user" class="form-control" placeholder="Submitter name">
                </div>
                @endif

                <div class="col-md-3">
                    <button class="btn btn-primary">Filter</button>
                    <a href="{{ route('projects.list') }}" class="btn btn-secondary">Reset</a>
                </div>

            </div>

        </form>

        @if(auth()->user()->role->name == 'admin')
            <input type="text" id="rejectReason" placeholder="Reject reason" class=" mb-2">

            <button onclick="bulkAction('approve')" class="btn btn-success">Bulk Approve</button>

            <button onclick="bulkAction('reject')" class="btn btn-danger">Bulk Reject</button>
        @endif

        <section class="section">
            <div class="row">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                <div class="col-lg-12">


                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Project  List</h5>
                            @if(auth()->user()->role->name == 'user')
                                <a href="{{route('projects.create')}}" class="btn btn-primary btn-sm">Add Category</a>
                            @endif
                        </div>
                        <div class="card-body">
                            <table class="table" id="projectTable">
                                <thead>
                                    <tr>
                                        @if(auth()->user()->role->name == 'admin' )
                                            <th>
                                                <input type="checkbox" id="selectAll">
                                            </th>
                                        @endif
                                        <th scope="col">#</th>
                                        <th scope="col">File</th>
                                        @if(auth()->user()->role->name == 'admin')
                                            <th>User Name</th>
                                        @endif
                                        <th scope="col">Project Name</th>
                                        <th>Submission Date</th>
                                        <th scope="col">Status</th>
                                        @if(auth()->user()->role->name == 'admin' )
                                            <th scope="col">Action</th>
                                        @endif

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($projects as $key => $project)
                                        <tr>
                                            @if(auth()->user()->role->name == 'admin')
                                            <td>
                                                <input type="checkbox" class="projectCheckbox"   value="{{ $project->id }}"
                                                    @if($project->status != 'pending') disabled @endif>
                                            </td>
                                            @endif
                                            <td>{{$key+1}}</td>
                                            <td>
                                                @if($project->file)

                                                    <a href="{{ asset($project->file) }}" target="_blank" class="btn btn-sm btn-primary">
                                                        View File
                                                    </a>

                                                    <!-- <a href="{{ asset($project->file) }}" download class="btn btn-sm btn-success">
                                                        Download
                                                    </a> -->

                                                @else
                                                    No File
                                                @endif
                                            </td>
                                            @if(auth()->user()->role->name == 'admin')
                                                <td>{{ $project->user->name }}</td>
                                            @endif
                                            <td>{{$project->title}}</td>
                                            <td>{{ $project->created_at->format('d-m-Y') }}</td>

                                            <td>
                                                @if($project->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                                @endif

                                                @if($project->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                                @endif

                                                @if($project->status == 'rejected')
                                                <span class="badge bg-danger">Rejected</span><br>
                                                @endif
                                            </td>

                                            <td>
                                                @if(auth()->user()->role->name == 'admin')

                                                    @if($project->status == 'pending')

                                                        <form action="{{ route('projects.approve',$project->id) }}" method="POST" style="display:inline">
                                                            @csrf
                                                            <button class="btn btn-success btn-sm">Approve</button>
                                                        </form>

                                                        <form action="{{ route('projects.reject',$project->id) }}" method="POST" style="display:inline">
                                                            @csrf
                                                            <input type="text" name="reason" placeholder="Reject reason" required>
                                                            <button class="btn btn-danger btn-sm">Reject</button>
                                                        </form>

                                                    @else
                                                        
                                                       <a href="{{ route('projects.history', $project->id) }}" class="btn btn-info">History</a>
                                                    @endif

                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>

        document.addEventListener("DOMContentLoaded", function () {

            const table = document.querySelector("#projectTable");

            // DataTable Initialize
            let dataTable;
            if (table) {
                dataTable = new simpleDatatables.DataTable(table, {
                    searchable: true,
                    fixedHeight: true,
                    perPage: 5,
                    perPageSelect: [5, 10, 25, 50],
                });
                console.log("DataTable initialized");  // 3. Confirm DataTable init success
            }

            // Bulk Action Function
            window.bulkAction = function(action){
                console.log("bulkAction called with action:", action);  // 4. Check which action is triggered

                let checkboxes = document.querySelectorAll('.projectCheckbox:checked');
                console.log("Checked checkboxes:", checkboxes.length);  // 5. Check how many checkboxes are checked

                if(checkboxes.length === 0){
                    alert("Please select at least one project");
                    console.log("No checkbox selected, returning early");  // 6. Early return info
                    return;
                }

                let ids = [];

                checkboxes.forEach(function(box){
                    ids.push(box.value);
                });
                console.log("IDs collected for bulk action:", ids);  // 7. Show collected project IDs

                let reason = '';

                if(action === 'reject'){
                    reason = document.getElementById('rejectReason').value;
                    console.log("Reject reason:", reason);  // 8. Log reason for rejection

                    if(reason.trim() === ''){
                        alert("Reject reason required");
                        console.log("Reject reason empty, returning early");  // 9. Early return due to no reason
                        return;
                    }
                }

                fetch("{{ route('projects.bulk.action') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        projects: ids,
                        action: action,
                        reason: reason
                    })
                })
                .then(res => {
                    console.log("Fetch response status:", res.status);  // 10. Log HTTP response status
                    return res.json();
                })
                .then(data => {
                    console.log("Fetch response JSON:", data);  // 11. Log returned JSON data

                    alert(data.message);
                    location.reload();
                })
                .catch(error => {
                    console.error("Fetch error:", error);  // 12. Log fetch errors
                });
            }
        });

    </script>
@endsection('content')




