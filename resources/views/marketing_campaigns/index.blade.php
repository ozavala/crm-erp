@extends('layouts.app')

@section('title', 'Marketing Campaigns')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Marketing Campaigns</h3>
                    <a href="{{ route('marketing-campaigns.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Campaign
                    </a>
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

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Recipients</th>
                                    <th>Sent</th>
                                    <th>Open Rate</th>
                                    <th>Click Rate</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaigns as $campaign)
                                    <tr>
                                        <td>
                                            <a href="{{ route('marketing-campaigns.show', $campaign) }}" class="text-decoration-none">
                                                {{ $campaign->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($campaign->type) }}</span>
                                        </td>
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
                                        <td>{{ $campaign->total_recipients }}</td>
                                        <td>{{ $campaign->sent_count }}</td>
                                        <td>{{ $campaign->open_rate }}%</td>
                                        <td>{{ $campaign->click_rate }}%</td>
                                        <td>{{ $campaign->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('marketing-campaigns.show', $campaign) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                @if($campaign->canBeSent())
                                                    <a href="{{ route('marketing-campaigns.edit', $campaign) }}" 
                                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                @endif
                                                @if($campaign->status === 'draft')
                                                    <form action="{{ route('marketing-campaigns.send', $campaign) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success" 
                                                                onclick="return confirm('Are you sure you want to send this campaign?')"
                                                                title="Send">
                                                            <i class="fas fa-paper-plane"></i> Send
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($campaign->status === 'sending')
                                                    <form action="{{ route('marketing-campaigns.pause', $campaign) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Pause">
                                                            <i class="fas fa-pause"></i> Pause
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($campaign->status === 'paused')
                                                    <form action="{{ route('marketing-campaigns.resume', $campaign) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Resume">
                                                            <i class="fas fa-play"></i> Resume
                                                        </button>
                                                    </form>
                                                @endif
                                                @if(in_array($campaign->status, ['draft', 'scheduled', 'paused']))
                                                    <form action="{{ route('marketing-campaigns.cancel', $campaign) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                onclick="return confirm('Are you sure you want to cancel this campaign?')"
                                                                title="Cancel">
                                                            <i class="fas fa-times"></i> Cancel
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No campaigns found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $campaigns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 