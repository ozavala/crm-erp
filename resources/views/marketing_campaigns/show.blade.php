@extends('layouts.app')

@section('title', 'Campaign Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ $campaign->name }}</h3>
                    <div class="btn-group">
                        @if($campaign->canBeSent())
                            <form action="{{ route('marketing-campaigns.send', $campaign) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success" 
                                        onclick="return confirm('Are you sure you want to send this campaign?')">
                                    <i class="fas fa-paper-plane"></i> Send Campaign
                                </button>
                            </form>
                        @endif
                        @if($campaign->status === 'draft')
                            <a href="{{ route('marketing-campaigns.edit', $campaign) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif
                        <a href="{{ route('marketing-campaigns.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <h5>Campaign Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $campaign->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Description:</strong></td>
                                    <td>{{ $campaign->description ?? 'No description' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td><span class="badge bg-info">{{ ucfirst($campaign->type) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @switch($campaign->status)
                                            @case('draft')
                                                <span class="badge bg-secondary">Draft</span>
                                                @break
                                            @case('scheduled')
                                                <span class="badge bg-warning">Scheduled</span>
                                                @break
                                            @case('sending')
                                                <span class="badge bg-primary">Sending</span>
                                                @break
                                            @case('sent')
                                                <span class="badge bg-success">Sent</span>
                                                @break
                                            @case('paused')
                                                <span class="badge bg-warning">Paused</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Subject:</strong></td>
                                    <td>{{ $campaign->subject }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $campaign->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                @if($campaign->scheduled_at)
                                    <tr>
                                        <td><strong>Scheduled:</strong></td>
                                        <td>{{ $campaign->scheduled_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                @endif
                                @if($campaign->sent_at)
                                    <tr>
                                        <td><strong>Sent:</strong></td>
                                        <td>{{ $campaign->sent_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                @endif
                            </table>

                            <h5 class="mt-4">Email Content</h5>
                            <div class="border p-3 bg-light">
                                {!! $campaign->content !!}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <h5>Campaign Statistics</h5>
                            <div class="row">
                                <div class="col-6">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $stats['total_recipients'] }}</h4>
                                            <small>Total Recipients</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $stats['sent_count'] }}</h4>
                                            <small>Sent</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-6">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $stats['opened_count'] }}</h4>
                                            <small>Opened</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $stats['clicked_count'] }}</h4>
                                            <small>Clicked</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-6">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $stats['bounced_count'] }}</h4>
                                            <small>Bounced</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-secondary text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $stats['unsubscribed_count'] }}</h4>
                                            <small>Unsubscribed</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($campaign->isSent())
                                <div class="mt-3">
                                    <h6>Performance Metrics</h6>
                                    <div class="progress mb-2">
                                        <div class="progress-bar bg-success" style="width: {{ $campaign->open_rate }}%">
                                            {{ $campaign->open_rate }}%
                                        </div>
                                    </div>
                                    <small>Open Rate</small>

                                    <div class="progress mb-2">
                                        <div class="progress-bar bg-info" style="width: {{ $campaign->click_rate }}%">
                                            {{ $campaign->click_rate }}%
                                        </div>
                                    </div>
                                    <small>Click Rate</small>

                                    <div class="progress mb-2">
                                        <div class="progress-bar bg-danger" style="width: {{ $campaign->bounce_rate }}%">
                                            {{ $campaign->bounce_rate }}%
                                        </div>
                                    </div>
                                    <small>Bounce Rate</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Recipients</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Sent At</th>
                                            <th>Opened At</th>
                                            <th>Clicked At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($campaign->recipients as $recipient)
                                            <tr>
                                                <td>{{ $recipient->name ?? 'N/A' }}</td>
                                                <td>{{ $recipient->email }}</td>
                                                <td>
                                                    @if($recipient->customer_id)
                                                        <span class="badge bg-primary">Customer</span>
                                                    @elseif($recipient->lead_id)
                                                        <span class="badge bg-warning">Lead</span>
                                                    @else
                                                        <span class="badge bg-secondary">Manual</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @switch($recipient->status)
                                                        @case('pending')
                                                            <span class="badge bg-secondary">Pending</span>
                                                            @break
                                                        @case('sent')
                                                            <span class="badge bg-success">Sent</span>
                                                            @break
                                                        @case('delivered')
                                                            <span class="badge bg-info">Delivered</span>
                                                            @break
                                                        @case('opened')
                                                            <span class="badge bg-primary">Opened</span>
                                                            @break
                                                        @case('clicked')
                                                            <span class="badge bg-warning">Clicked</span>
                                                            @break
                                                        @case('bounced')
                                                            <span class="badge bg-danger">Bounced</span>
                                                            @break
                                                        @case('unsubscribed')
                                                            <span class="badge bg-dark">Unsubscribed</span>
                                                            @break
                                                        @case('failed')
                                                            <span class="badge bg-danger">Failed</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>{{ $recipient->sent_at ? $recipient->sent_at->format('M d, Y H:i') : '-' }}</td>
                                                <td>{{ $recipient->opened_at ? $recipient->opened_at->format('M d, Y H:i') : '-' }}</td>
                                                <td>{{ $recipient->clicked_at ? $recipient->clicked_at->format('M d, Y H:i') : '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No recipients found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 