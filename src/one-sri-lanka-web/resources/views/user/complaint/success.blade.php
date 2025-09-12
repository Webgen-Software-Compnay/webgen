@extends('user.layouts.app')

@section('title', 'Complaint Submitted Successfully')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h2 class="text-success mb-3">Complaint Submitted Successfully!</h2>
                    
                    <p class="text-muted mb-4">
                        Thank you for your submission. Your complaint has been received and will be reviewed by the appropriate authorities.
                    </p>
                    
                    @if(session('complaint_id'))
                        <div class="alert alert-info">
                            <strong>Reference ID:</strong> #{{ session('complaint_id') }}
                            <br><small>Please keep this reference number for future inquiries.</small>
                        </div>
                    @endif
                    
                    <div class="d-grid gap-2 d-md-block">
                        <a href="{{ route('action') }}" class="btn btn-primary me-2">
                            <i class="fas fa-list me-1"></i> Submit Another Complaint
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home me-1"></i> Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">What Happens Next?</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6>Complaint Received</h6>
                                <p class="text-muted small">Your complaint has been successfully submitted to our system.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6>Under Review</h6>
                                <p class="text-muted small">The relevant authority will review your complaint within 2-3 business days.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6>Investigation</h6>
                                <p class="text-muted small">If required, an investigation will be conducted to address your concern.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6>Resolution</h6>
                                <p class="text-muted small">You may be contacted for updates or when the issue is resolved.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -2.25rem;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content h6 {
    margin-bottom: 0.25rem;
}

.timeline-content p {
    margin-bottom: 0;
}
</style>
@endsection
