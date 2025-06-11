@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
             <h1 class="mb-4">Dashboard</h1>
             {{-- Lead Statistics Cards --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">Total Leads</div>
                        <div class="card-body">
                            <h4 class="card-title">{{ $totalLeads }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Active Leads</div>
                        <div class="card-body">
                            <h4 class="card-title">{{ $activeLeads }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-header">New Leads</div>
                        <div class="card-body">
                            <h4 class="card-title">{{ $leadStatusCounts['New'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-header">Qualified Leads</div>
                        <div class="card-body">
                            <h4 class="card-title">{{ $leadStatusCounts['Qualified'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
                
            </div>
                
            
        </div>
    </div>
</div>
@endsection
