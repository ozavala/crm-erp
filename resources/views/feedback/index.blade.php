@extends('layouts.app')

@section('title', __('feedback.User Feedback'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('feedback.User Feedback') }}</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="
                    <thead>
                    <tr>
                        <th>{{ __('feedback.ID') }}</th>
                        <th>{{ __('feedback.User') }}</th>
                        <th>{{ __('feedback.Feedback') }}</th>
                        <th>{{ __('feedback.Created At') }}</th>
                        <th>{{ __('feedback.Actions') }}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection