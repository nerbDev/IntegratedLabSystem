
@extends ('layouts.masterlayout')

@section('title', 'My Laboratory Results')

@section('content')
<style>
    .results-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    .results-card:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(0, 212, 255, 0.4);
        transform: translateX(5px);
    }
    .date-box {
        background: rgba(0, 212, 255, 0.1);
        color: #0dcaf0;
        border-radius: 12px;
        padding: 10px;
        text-align: center;
        min-width: 70px;
    }
    .service-title {
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 2px;
    }
    .info-sub {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.5);
    }
    .btn-see-result {
        background: #0dcaf0;
        color: #000;
        font-weight: 600;
        border-radius: 50px;
        padding: 8px 25px;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    .btn-see-result:hover {
        background: #00d4ff;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.4);
        color: #000;
    }
</style>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="text-white">Medical Examination History</h3>
            <p class="text-muted small">View and download your official laboratory findings</p>
        </div>
    </div>

    @if($releasedAppointments->isEmpty())
        <div class="text-center py-5 rounded-4" style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1);">
            <i class="bi bi-file-earmark-medical text-muted" style="font-size: 3rem;"></i>
            <h5 class="text-white mt-3">No results available yet</h5>
            <p class="text-muted">Your results will appear here once they are officially released by the lab.</p>
        </div>
    @else
        <div class="row">
            @foreach($releasedAppointments as $app)
                <div class="col-12">
                    <div class="results-card d-flex align-items-center">
                        <!-- Date Column -->
                        <div class="date-box me-4">
                            <div class="small text-uppercase">{{ \Carbon\Carbon::parse($app->appointment_date)->format('M') }}</div>
                            <div class="h4 mb-0">{{ \Carbon\Carbon::parse($app->appointment_date)->format('d') }}</div>
                        </div>

                        <!-- Appointment Details Column -->
                        <div class="flex-grow-1">
                            <div class="service-title">{{ $app->service }}</div>
                            <div class="d-flex gap-3">
                                <span class="info-sub"><i class="bi bi-person me-1"></i>{{ $app->first_name }} {{ $app->last_name }}</span>
                                <span class="info-sub"><i class="bi bi-geo-alt me-1"></i>{{ $app->appointment_type }}</span>
                                <span class="info-sub"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($app->appointment_time)->format('h:i A') }}</span>
                            </div>
                        </div>

                        <!-- Status Column -->
                        <div class="me-4 d-none d-md-block">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                                <i class="bi bi-check-circle-fill me-1"></i> Released
                            </span>
                        </div>

                        <!-- Action Column -->
                        <div>
                            @if($app->result && $app->result->file_path)
                                <a href="{{ route('patient.result.download', $app->id) }}" class="btn-see-result">
                                    <i class="bi bi-download me-1"></i> View Result
                                </a>
                            @else
                                <span class="text-muted small italic">Processing file...</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
