@extends('layouts.app')

@section('title', $website->name . ' - Git Webhook Manager')
@section('page-title', $website->name)
@section('page-description', ucfirst($website->project_type) . ' Website Details')

@section('page-actions')
    <div class="btn-group" role="group">
        <a href="{{ route('websites.edit', $website) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <button type="button" 
                class="btn btn-outline-info"
                onclick="confirmAction('Redeploy Configuration', 'Regenerate and redeploy Nginx and PHP-FPM configurations for {{ $website->domain }}?', 'Yes, redeploy!', 'question').then(confirmed => { if(confirmed) document.getElementById('redeploy-form').submit(); })">
            <i class="bi bi-arrow-clockwise me-1"></i> Redeploy
        </button>
        <button type="button" 
                class="btn btn-outline-danger"
                data-bs-toggle="modal" 
                data-bs-target="#deleteModal">
            <i class="bi bi-trash me-1"></i> Delete
        </button>
    </div>
    
    <!-- Hidden redeploy form -->
    <form id="redeploy-form" action="{{ route('websites.redeploy', $website) }}" method="POST" class="d-none">
        @csrf
    </form>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <!-- Basic Information -->
                    <h5 class="card-title mb-4">Basic Information</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Name:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $website->name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Domain:</strong>
                        </div>
                        <div class="col-md-8">
                            <code>{{ $website->domain }}</code>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Project Type:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-{{ $website->project_type_badge }}">
                                {{ $website->project_type === 'php' ? 'PHP' : 'Node.js' }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Version:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $website->version_display }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-{{ $website->status_badge }}">
                                {{ $website->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Nginx Status:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-{{ $website->nginx_status_badge }}">
                                {{ ucfirst($website->nginx_status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>SSL Enabled:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($website->ssl_enabled)
                                <span class="badge bg-success">
                                    <i class="bi bi-shield-check me-1"></i> Yes
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="bi bi-shield me-1"></i> No
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>SSL Status:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-{{ $website->ssl_status_badge }}">
                                {{ ucfirst($website->ssl_status) }}
                            </span>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Path Configuration -->
                    <h5 class="card-title mb-4">Path Configuration</h5>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Root Path:</strong>
                        </div>
                        <div class="col-md-8">
                            <code>{{ $website->root_path }}</code>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>{{ $website->project_type === 'php' ? 'Working Directory:' : 'Run opt:' }}</strong>
                        </div>
                        <div class="col-md-8">
                            <code>{{ $website->working_directory ?? $website->root_path }}</code>
                        </div>
                    </div>

                    @if($website->project_type === 'node' && $website->port)
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Port:</strong>
                            </div>
                            <div class="col-md-8">
                                {{ $website->port }}
                            </div>
                        </div>
                    @endif

                    <hr class="my-4">

                    <!-- Timestamps -->
                    <h5 class="card-title mb-4">Timestamps</h5>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Created At:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $website->created_at->format('d M Y, h:i A') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Last Updated:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $website->updated_at->format('d M Y, h:i A') }}
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('websites.index', ['type' => $website->project_type]) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back to List
                        </a>
                        <div class="btn-group">
                            <form action="{{ route('websites.toggle-ssl', $website) }}" method="POST" class="d-inline">
                                @csrf
                                @method('POST')
                                <button type="submit" class="btn btn-{{ $website->ssl_enabled ? 'success' : 'outline-secondary' }}">
                                    <i class="bi bi-shield-check me-1"></i> 
                                    {{ $website->ssl_enabled ? 'SSL Enabled' : 'Enable SSL' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>{{ $website->name }}</strong>?</p>
                    <p class="text-muted small">This will only remove the configuration from the database. The actual files and directories will not be deleted.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('websites.destroy', $website) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
