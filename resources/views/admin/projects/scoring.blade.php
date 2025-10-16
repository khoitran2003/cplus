@extends('admin.layout')

@section('title', 'Scoring - ' . $project->name)
@section('page-title', 'Chấm điểm đa tiêu chí')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-star me-2"></i>Project: {{ $project->name }}
                </h6>
                <div>
                    <a href="{{ route('projects.show', $project->id) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back to Project
                    </a>
                    <button type="button" class="btn btn-success btn-sm" onclick="exportResults()">
                        <i class="fas fa-download me-1"></i>Export Results
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Client:</strong> {{ $project->client->name }}<br>
                        <strong>Industry:</strong> {{ $project->industry ?? 'N/A' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong> 
                        <span class="badge bg-{{ $project->status == 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($project->status) }}
                        </span><br>
                        <strong>Locations:</strong> {{ $project->projectLocations->count() }}
                    </div>
                </div>

                <!-- Scoring Form -->
                <form id="scoringForm" method="POST" action="{{ route('projects.save-scoring', $project->id) }}">
                    @csrf
                    
                    <!-- Criteria Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-list-check me-2"></i>Tiêu chí đánh giá
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">STT</th>
                                            <th width="25%">Tên tiêu chí</th>
                                            <th width="35%">Mô tả</th>
                                            <th width="10%">Trọng số</th>
                                            <th width="10%">Điểm tối đa</th>
                                            <th width="15%">Loại</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($criteria as $index => $criterion)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $criterion->name }}</strong></td>
                                            <td>{{ $criterion->description }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-primary">{{ $criterion->weight }}%</span>
                                            </td>
                                            <td class="text-center">{{ $criterion->max_score }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ ucfirst($criterion->type) }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Scoring Matrix -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-table me-2"></i>Ma trận chấm điểm
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-primary">
                                        <tr>
                                            <th rowspan="2" class="align-middle text-center">Địa điểm</th>
                                            <th colspan="{{ $criteria->count() }}" class="text-center">Điểm theo tiêu chí</th>
                                            <th rowspan="2" class="align-middle text-center">Tổng điểm</th>
                                            <th rowspan="2" class="align-middle text-center">Xếp hạng</th>
                                        </tr>
                                        <tr>
                                            @foreach($criteria as $criterion)
                                            <th class="text-center">
                                                {{ $criterion->name }}<br>
                                                <small>(Max: {{ $criterion->max_score }})</small>
                                            </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($project->projectLocations as $index => $projectLocation)
                                        <tr>
                                            <td>
                                                <strong>{{ $projectLocation->location->name }}</strong><br>
                                                <small class="text-muted">
                                                    {{ $projectLocation->location->city }}, 
                                                    {{ $projectLocation->location->province }}
                                                </small>
                                            </td>
                                            @foreach($criteria as $criterion)
                                            <td class="text-center">
                                                @php
                                                    $existingScore = $projectLocation->scores
                                                        ->where('criteria_id', $criterion->id)
                                                        ->first();
                                                @endphp
                                                <input type="number" 
                                                       class="form-control form-control-sm score-input" 
                                                       name="scores[{{ $index }}][score]"
                                                       data-project-location="{{ $projectLocation->id }}"
                                                       data-criteria="{{ $criterion->id }}"
                                                       value="{{ $existingScore->score ?? '' }}"
                                                       min="0" 
                                                       max="{{ $criterion->max_score }}"
                                                       step="0.01"
                                                       onchange="calculateTotalScore({{ $projectLocation->id }})">
                                                
                                                <input type="hidden" 
                                                       name="scores[{{ $index }}][project_location_id]" 
                                                       value="{{ $projectLocation->id }}">
                                                <input type="hidden" 
                                                       name="scores[{{ $index }}][criteria_id]" 
                                                       value="{{ $criterion->id }}">
                                                <input type="hidden" 
                                                       name="scores[{{ $index }}][notes]" 
                                                       value="">
                                            </td>
                                            @endforeach
                                            <td class="text-center">
                                                <span class="badge bg-success total-score" id="total-{{ $projectLocation->id }}">
                                                    {{ $projectLocation->total_score ? number_format($projectLocation->total_score, 2) : '0.00' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning ranking" id="rank-{{ $projectLocation->id }}">
                                                    {{ $index + 1 }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Lưu điểm số
                            </button>
                            <button type="button" class="btn btn-info btn-lg ms-2" onclick="calculateAllScores()">
                                <i class="fas fa-calculator me-2"></i>Tính lại điểm
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Results Summary -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar me-2"></i>Kết quả tổng hợp
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <canvas id="locationComparisonChart" height="100"></canvas>
                    </div>
                    <div class="col-md-4">
                        <h6>Bảng xếp hạng</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Hạng</th>
                                        <th>Địa điểm</th>
                                        <th>Điểm</th>
                                    </tr>
                                </thead>
                                <tbody id="rankingTable">
                                    @foreach($project->projectLocations as $index => $projectLocation)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $projectLocation->location->name }}</td>
                                        <td>{{ $projectLocation->total_score ? number_format($projectLocation->total_score, 2) : '0.00' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Criteria data for calculations
const criteria = @json($criteria->keyBy('id'));
const projectLocations = @json($project->projectLocations->keyBy('id'));

function calculateTotalScore(projectLocationId) {
    let totalScore = 0;
    let totalWeight = 0;
    
    // Get all score inputs for this project location
    const scoreInputs = document.querySelectorAll(`input[data-project-location="${projectLocationId}"]`);
    
    scoreInputs.forEach(input => {
        const criteriaId = parseInt(input.dataset.criteria);
        const score = parseFloat(input.value) || 0;
        const criterion = criteria[criteriaId];
        
        if (criterion) {
            const weightedScore = (score / criterion.max_score) * criterion.weight;
            totalScore += weightedScore;
            totalWeight += criterion.weight;
        }
    });
    
    const finalScore = totalWeight > 0 ? (totalScore / totalWeight) * 100 : 0;
    
    // Update total score display
    const totalScoreElement = document.getElementById(`total-${projectLocationId}`);
    if (totalScoreElement) {
        totalScoreElement.textContent = finalScore.toFixed(2);
    }
    
    // Update ranking
    updateRanking();
}

function calculateAllScores() {
    projectLocations.forEach((projectLocation, id) => {
        calculateTotalScore(id);
    });
}

function updateRanking() {
    // Get all project locations with their scores
    const locations = [];
    projectLocations.forEach((projectLocation, id) => {
        const totalScoreElement = document.getElementById(`total-${id}`);
        const score = parseFloat(totalScoreElement.textContent) || 0;
        locations.push({ id, score, name: projectLocation.location.name });
    });
    
    // Sort by score descending
    locations.sort((a, b) => b.score - a.score);
    
    // Update ranking badges
    locations.forEach((location, index) => {
        const rankElement = document.getElementById(`rank-${location.id}`);
        if (rankElement) {
            rankElement.textContent = index + 1;
        }
    });
    
    // Update ranking table
    updateRankingTable(locations);
    
    // Update chart
    updateChart(locations);
}

function updateRankingTable(locations) {
    const tbody = document.getElementById('rankingTable');
    tbody.innerHTML = '';
    
    locations.forEach((location, index) => {
        const row = tbody.insertRow();
        row.insertCell(0).textContent = index + 1;
        row.insertCell(1).textContent = location.name;
        row.insertCell(2).textContent = location.score.toFixed(2);
    });
}

function updateChart(locations) {
    const ctx = document.getElementById('locationComparisonChart').getContext('2d');
    
    // Destroy existing chart if it exists
    if (window.locationChart) {
        window.locationChart.destroy();
    }
    
    window.locationChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: locations.map(l => l.name),
            datasets: [{
                label: 'Tổng điểm',
                data: locations.map(l => l.score),
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
}

function exportResults() {
    const format = prompt('Chọn định dạng xuất:\n1 - PDF\n2 - Excel', '1');
    
    if (format === '1') {
        window.open(`{{ route('projects.export', [$project->id, 'pdf']) }}`, '_blank');
    } else if (format === '2') {
        window.open(`{{ route('projects.export', [$project->id, 'excel']) }}`, '_blank');
    }
}

// Initialize chart on page load
document.addEventListener('DOMContentLoaded', function() {
    const locations = @json($project->projectLocations->sortByDesc('total_score')->values());
    updateChart(locations.map((pl, index) => ({
        id: pl.id,
        score: parseFloat(pl.total_score) || 0,
        name: pl.location.name
    })));
});
</script>
@endsection
