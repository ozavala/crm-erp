@extends('layouts.app')

@section('title', 'Email Template Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ $template->name }}</h3>
                    <div class="btn-group">
                        <a href="{{ route('email-templates.edit', $template) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('email-templates.preview', $template) }}" class="btn btn-info" target="_blank">
                            <i class="fas fa-eye"></i> Preview
                        </a>
                        <a href="{{ route('email-templates.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Template Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $template->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td><span class="badge bg-info">{{ ucfirst($template->type) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($template->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Subject:</strong></td>
                                    <td>{{ $template->subject }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $template->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created By:</strong></td>
                                    <td>{{ $template->creator->name ?? 'Unknown' }}</td>
                                </tr>
                            </table>

                            <h5 class="mt-4">Email Content</h5>
                            <div class="border p-3 bg-light">
                                {!! $template->content !!}
                            </div>

                            @if($template->html_content)
                                <h5 class="mt-4">HTML Content</h5>
                                <div class="border p-3 bg-light">
                                    {!! $template->html_content !!}
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <h5>Available Variables</h5>
                            <div class="list-group">
                                @foreach($template->getAvailableVariables() as $variable)
                                    <div class="list-group-item">
                                        <code>{{ $variable }}</code>
                                    </div>
                                @endforeach
                            </div>

                            <h5 class="mt-4">Usage Statistics</h5>
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $template->campaigns()->count() }}</h4>
                                    <small>Campaigns using this template</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 