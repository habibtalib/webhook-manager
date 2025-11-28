@extends('layouts.app')

@section('title', 'Add Website - Git Webhook Manager')
@section('page-title', 'Add New ' . ucfirst($type) . ' Website')
@section('page-description', 'Configure a new ' . $type . ' virtual host')

@section('content')
    @if(in_array(config('app.env'), ['local', 'dev', 'development']))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong><i class="bi bi-exclamation-triangle me-1"></i>Development Mode:</strong>
            Configurations will be saved to <code>storage/server/</code> instead of system directories.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('websites.store') }}" method="POST" id="websiteForm">
                        @csrf

                        <input type="hidden" name="project_type" value="{{ $type }}">

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Website Name <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name" 
                                value="{{ old('name') }}" 
                                required
                                placeholder="My Awesome Project"
                            >
                            <small class="form-text text-muted">
                                A friendly name for your website.
                            </small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Domain -->
                        <div class="mb-3">
                            <label for="domain" class="form-label">
                                Domain Name <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control font-monospace @error('domain') is-invalid @enderror" 
                                id="domain" 
                                name="domain" 
                                value="{{ old('domain') }}" 
                                required
                                placeholder="example.com"
                            >
                            <small class="form-text text-muted">
                                The domain name for this website (e.g., example.com or subdomain.example.com).
                            </small>
                            @error('domain')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Root Path -->
                        <div class="mb-3">
                            <label for="root_path" class="form-label">
                                Website Root Path
                            </label>
                            <input 
                                type="text" 
                                class="form-control font-monospace @error('root_path') is-invalid @enderror" 
                                id="root_path" 
                                name="root_path" 
                                value="{{ old('root_path') }}" 
                                placeholder="/var/www/example_com"
                            >
                            <small class="form-text text-muted">
                                Leave empty to auto-generate from domain name (e.g., /var/www/example_com).
                            </small>
                            @error('root_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Working Directory / Run Opt -->
                        @if($type === 'php')
                            <div class="mb-3">
                                <label for="working_directory" class="form-label">
                                    Working Directory (Document Root)
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control font-monospace @error('working_directory') is-invalid @enderror" 
                                    id="working_directory" 
                                    name="working_directory" 
                                    value="{{ old('working_directory', '/') }}" 
                                    placeholder="/ or /public or /public_html"
                                >
                                <small class="form-text text-muted">
                                    <strong>Relative path</strong> from root path. Examples: <code>/</code> (root), <code>/public</code>, <code>/public_html</code>
                                    <br>Final path: <code>{root_path}{working_directory}</code>
                                </small>
                                @error('working_directory')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <div class="mb-3">
                                <label for="working_directory" class="form-label">
                                    Run opt
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control font-monospace @error('working_directory') is-invalid @enderror" 
                                    id="working_directory" 
                                    name="working_directory" 
                                    value="{{ old('working_directory') }}" 
                                    placeholder="start"
                                >
                                <small class="form-text text-muted">
                                    Startup mode in package.json
                                </small>
                                @error('working_directory')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <!-- Version Selection -->
                        @if($type === 'php')
                            <div class="mb-3">
                                <label for="php_version" class="form-label">
                                    PHP Version
                                </label>
                                <select 
                                    class="form-select @error('php_version') is-invalid @enderror" 
                                    id="php_version" 
                                    name="php_version"
                                >
                                    <option value="">System Default</option>
                                    @foreach($phpVersions as $version)
                                        <option value="{{ $version }}" {{ old('php_version') === $version ? 'selected' : '' }}>
                                            PHP {{ $version }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    Select the PHP version for this website.
                                </small>
                                @error('php_version')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <div class="mb-3">
                                <label for="node_version" class="form-label">
                                    Node.js Version
                                </label>
                                <select 
                                    class="form-select @error('node_version') is-invalid @enderror" 
                                    id="node_version" 
                                    name="node_version"
                                >
                                    <option value="">System Default</option>
                                    @foreach($nodeVersions as $version)
                                        <option value="{{ $version }}" {{ old('node_version') === $version ? 'selected' : '' }}>
                                            Node.js {{ $version }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    Select the Node.js version for this website.
                                </small>
                                @error('node_version')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Port -->
                            <div class="mb-3">
                                <label for="port" class="form-label">
                                    Port
                                </label>
                                <input 
                                    type="number" 
                                    class="form-control @error('port') is-invalid @enderror" 
                                    id="port" 
                                    name="port" 
                                    value="{{ old('port') }}" 
                                    placeholder="3000"
                                    min="1"
                                    max="65535"
                                >
                                <small class="form-text text-muted">
                                    Port of the project
                                </small>
                                @error('port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <!-- SSL Enabled -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="ssl_enabled" 
                                    name="ssl_enabled"
                                    value="1"
                                    {{ old('ssl_enabled') ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="ssl_enabled">
                                    Enable Let's Encrypt SSL
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Enable SSL certificate for this website. You can enable this later.
                            </small>
                        </div>

                        <!-- Active Status -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="is_active" 
                                    name="is_active"
                                    value="1"
                                    {{ old('is_active', true) ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Set the website as active.
                            </small>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('websites.index', ['type' => $type]) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i> Create Website
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
