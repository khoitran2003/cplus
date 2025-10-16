@extends('admin.layout')

@section('title', 'Dashboard - C+ Scoring System')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Tổng Clients</div>
                        <div class="h5 mb-0 font-weight-bold">{{ $stats['total_clients'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Tổng Projects</div>
                        <div class="h5 mb-0 font-weight-bold">{{ $stats['total_projects'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-project-diagram fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Tổng Locations</div>
                        <div class="h5 mb-0 font-weight-bold">{{ $stats['total_locations'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-map-marker-alt fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Projects Active</div>
                        <div class="h5 mb-0 font-weight-bold">{{ $stats['active_projects'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-play-circle fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Projects -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-project-diagram me-2"></i>Recent Projects
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Project Name</th>
                                <th>Client</th>
                                <th>Industry</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($recentProjects ?? []) as $project)
                            <tr>
                                <td>{{ $project['name'] ?? 'N/A' }}</td>
                                <td>{{ $project['client_name'] ?? 'N/A' }}</td>
                                <td>{{ $project['industry'] ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ ($project['status'] ?? 'inactive') == 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($project['status'] ?? 'inactive') }}
                                    </span>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary" disabled>
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-success" disabled>
                                        <i class="fas fa-star"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No projects found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('clients.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create New Client
                    </a>
                    <a href="{{ route('projects.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Create New Project
                    </a>
                    <a href="{{ route('locations.index') }}" class="btn btn-info">
                        <i class="fas fa-map-marker-alt me-2"></i>Manage Locations
                    </a>
                    <a href="{{ route('criteria.index') }}" class="btn btn-warning">
                        <i class="fas fa-list-check me-2"></i>Manage Criteria
                    </a>
                </div>
            </div>
        </div>

        <!-- Project Scoring Status -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie me-2"></i>Scoring Status
                </h6>
            </div>
            <div class="card-body">
                <canvas id="scoringStatusChart" width="100" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Projects with Scoring Charts -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar me-2"></i>Project Scoring Comparison
                </h6>
            </div>
            <div class="card-body">
                <canvas id="projectComparisonChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Scoring Status Pie Chart
const scoringStatusCtx = document.getElementById('scoringStatusChart').getContext('2d');
const scoringStatusChart = new Chart(scoringStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Completed', 'In Progress', 'Not Started'],
        datasets: [{
            data: [{{ ($scoringStatus['completed'] ?? 0) }}, 
                   {{ ($scoringStatus['in_progress'] ?? 0) }}, 
                   {{ ($scoringStatus['not_started'] ?? 0) }}],
            backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Project Comparison Chart
const comparisonCtx = document.getElementById('projectComparisonChart').getContext('2d');
const comparisonChart = new Chart(comparisonCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(($comparisonLabels ?? [])) !!},
        datasets: [{
            label: 'Average Score',
            data: {!! json_encode(($comparisonData ?? [])) !!},
            backgroundColor: 'rgba(102, 126, 234, 0.8)',
            borderColor: 'rgba(102, 126, 234, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
@endsection
