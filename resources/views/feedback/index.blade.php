@extends('layouts.app')

@section('title', 'User Feedback')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>User Feedback</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Feedback</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection