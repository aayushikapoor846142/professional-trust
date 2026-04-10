@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <svg width="80" height="80" fill="currentColor" class="text-danger" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    
                    <h2 class="h4 text-danger mb-3">Access Denied</h2>
                    <p class="text-muted mb-4">
                        {{ $exception->getMessage() ?: 'You don\'t have permission to access this resource.' }}
                    </p>
                    
                    <p class="text-muted mb-4">
                        This could be because:
                    </p>
                    
                    <ul class="text-left text-muted mb-4">
                        <li>The resource doesn't belong to you</li>
                        <li>You don't have the required permissions</li>
                        <li>The resource has been moved or deleted</li>
                    </ul>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Go Back
                        </a>
                        <a href="{{ route('panel.list') }}" class="CdsTYButton-btn-primary">
                            <i class="fas fa-home me-2"></i>Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 15px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.card-body {
    padding: 3rem 2rem;
}

.text-danger {
    color: #dc3545 !important;
}

.btn {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

ul {
    max-width: 400px;
    margin: 0 auto;
}

li {
    margin-bottom: 0.5rem;
    padding-left: 1rem;
}
</style>
@endsection 