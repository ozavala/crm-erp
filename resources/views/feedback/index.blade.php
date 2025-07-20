@extends('layouts.app')

@section('title', __('feedback.Feedback List'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('feedback.Feedback List') }}</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="
                    <thead>
                    <tr>
                        <th>{{ __('feedback.ID') }}</th>
                        <th>{{ __('feedback.User') }}</th>
                        <th>{{ __('feedback.Type') }}</th>
                        <th>{{ __('feedback.Message') }}</th>
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