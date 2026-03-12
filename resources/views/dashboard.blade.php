@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    
    <div class="row">

        <div class="col-md-3">
            <div class="card bg-primary text-white p-3">
                Total Projects
                <h3>{{ $total }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white p-3">
                Pending
                <h3>{{ $pending }} ({{ $pendingPercent }}%)</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white p-3">
                Approved
                <h3>{{ $approved }} ({{ $approvedPercent }}%)</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-danger text-white p-3">
                Rejected
                <h3>{{ $rejected }} ({{ $rejectedPercent }}%)</h3>
            </div>
        </div>
    </div>

@endsection
