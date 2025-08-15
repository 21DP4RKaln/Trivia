@extends('admin.layout')

@section('title', 'Questions Management')

@section('content')
<div class="page-header">
    <div class="header-content">
        <div class="header-text">
            <h1 class="page-title">
                <i class="fas fa-question-circle"></i>
                Questions Management
            </h1>
            <p class="page-subtitle">Review and manage trivia questions</p>
        </div>
        <div class="header-stats">
            <div class="stat-item">
                <div class="stat-number">{{ count($fallbackQuestions) }}</div>
                <div class="stat-label">Fallback Questions</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ count($apiQuestions) }}</div>
                <div class="stat-label">API Questions</div>
            </div>
        </div>
    </div>
</div>

<!-- Question Sources Overview -->
<div class="sources-overview">
    <div class="source-card fallback-source">
        <div class="source-icon">
            <i class="fas fa-database"></i>
        </div>
        <div class="source-info">
            <h3>Fallback Questions</h3>
            <p>Local backup questions used when API is unavailable</p>
            <div class="source-stats">
                <span class="stat">{{ count($fallbackQuestions) }} available</span>
            </div>
        </div>
    </div>

    <div class="source-card api-source">
        <div class="source-icon">
            <i class="fas fa-cloud"></i>
        </div>
        <div class="source-info">
            <h3>API Questions</h3>
            <p>Live questions from numbersapi.com</p>
            <div class="source-stats">
                <span class="stat">{{ count($apiQuestions) }} samples</span>
            </div>
        </div>
    </div>
</div>

<!-- Fallback Questions Section -->
<div class="questions-section">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-database"></i>
            Fallback Questions
        </h2>
        <div class="section-actions">
            <button class="btn btn-outline btn-sm" onclick="toggleAllQuestions('fallback')">
                <i class="fas fa-eye"></i>
                Toggle All
            </button>
        </div>
    </div>

    <div class="questions-grid" id="fallback-questions">
        @foreach($fallbackQuestions as $index => $question)
            <div class="question-card" data-category="fallback">
                <div class="question-header">
                    <div class="question-number">
                        <span class="number">{{ $index + 1 }}</span>
                        <span class="badge fallback-badge">Fallback</span>
                    </div>
                    <button class="btn-toggle" onclick="toggleQuestion(this)">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                
                <div class="question-content">
                    <div class="question-text">
                        {{ $question['question'] }}
                    </div>
                    <div class="question-details collapsed">
                        <div class="options-list">
                            @foreach($question['options'] as $optionIndex => $option)
                                <div class="option-item {{ $option === $question['correct_answer'] ? 'correct' : '' }}">
                                    <span class="option-letter">{{ ['A', 'B', 'C', 'D'][$optionIndex] }}</span>
                                    <span class="option-text">{{ $option }}</span>
                                    @if($option === $question['correct_answer'])
                                        <i class="fas fa-check correct-icon"></i>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        @if(isset($question['full_fact']) && $question['full_fact'])
                            <div class="question-fact">
                                <div class="fact-header">
                                    <i class="fas fa-lightbulb"></i>
                                    Interesting Fact
                                </div>
                                <div class="fact-content">
                                    {{ $question['full_fact'] }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- API Questions Section -->
<div class="questions-section">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-cloud"></i>
            API Questions (Samples)
        </h2>
        <div class="section-actions">
            <button class="btn btn-outline btn-sm" onclick="toggleAllQuestions('api')">
                <i class="fas fa-eye"></i>
                Toggle All
            </button>
            <button class="btn btn-primary btn-sm" onclick="refreshApiQuestions()">
                <i class="fas fa-refresh"></i>
                Refresh
            </button>
        </div>
    </div>

    <div class="questions-grid" id="api-questions">
        @foreach($apiQuestions as $index => $question)
            <div class="question-card" data-category="api">
                <div class="question-header">
                    <div class="question-number">
                        <span class="number">{{ $index + 1 }}</span>
                        <span class="badge api-badge">API</span>
                    </div>
                    <button class="btn-toggle" onclick="toggleQuestion(this)">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                
                <div class="question-content">
                    <div class="question-text">
                        {{ $question['question'] }}
                    </div>
                    <div class="question-details collapsed">
                        <div class="options-list">
                            @foreach($question['options'] as $optionIndex => $option)
                                <div class="option-item {{ $option === $question['correct_answer'] ? 'correct' : '' }}">
                                    <span class="option-letter">{{ ['A', 'B', 'C', 'D'][$optionIndex] }}</span>
                                    <span class="option-text">{{ $option }}</span>
                                    @if($option === $question['correct_answer'])
                                        <i class="fas fa-check correct-icon"></i>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        @if(isset($question['full_fact']) && $question['full_fact'])
                            <div class="question-fact">
                                <div class="fact-header">
                                    <i class="fas fa-lightbulb"></i>
                                    Interesting Fact
                                </div>
                                <div class="fact-content">
                                    {{ $question['full_fact'] }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Question Stats -->
<div class="stats-section">
    <div class="stats-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-bar"></i>
                Question Statistics
            </h3>
        </div>
        <div class="card-content">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value">{{ count($fallbackQuestions) }}</div>
                        <div class="stat-label">Fallback Questions</div>
                        <div class="stat-description">Local backup questions</div>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-cloud"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value">∞</div>
                        <div class="stat-label">API Questions</div>
                        <div class="stat-description">Unlimited from numbersapi.com</div>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value">100%</div>
                        <div class="stat-label">Availability</div>
                        <div class="stat-description">Fallback ensures continuity</div>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-random"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value">4</div>
                        <div class="stat-label">Options per Question</div>
                        <div class="stat-description">Multiple choice format</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('resources/js/admin/admin-questions.js') }}"></script>
@endpush
@endsection