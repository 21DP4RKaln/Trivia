<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - Number Trivia Game</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/scss/app.scss', 'resources/css/auth/auth.css', 'resources/css/auth/terms-of-service.css', 'resources/js/app.js', 'resources/js/auth/terms-of-service.js'])    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="global-page auth-page">
    <!-- Global Background -->
    <div class="global-background"></div>
    
    <!-- Global Particles -->
    <div class="global-particles">
        <div class="global-particle" style="left: 11%; animation-delay: 1.2s;"></div>
        <div class="global-particle" style="left: 22%; animation-delay: 3.2s;"></div>
        <div class="global-particle" style="left: 33%; animation-delay: 5.2s;"></div>
        <div class="global-particle" style="left: 44%; animation-delay: 7.2s;"></div>
        <div class="global-particle" style="left: 55%; animation-delay: 9.2s;"></div>
        <div class="global-particle" style="left: 66%; animation-delay: 11.2s;"></div>
        <div class="global-particle" style="left: 77%; animation-delay: 13.2s;"></div>
        <div class="global-particle" style="left: 88%; animation-delay: 15.2s;"></div>
    </div>
    
    <!-- Scroll Progress Indicator -->
    <div class="scroll-indicator">
        <div class="scroll-progress" id="scrollProgress"></div>
    </div>

    <!-- Back Button -->
    <a href="{{ url()->previous() }}" class="back-button">
        <i class="fas fa-arrow-left"></i>
    </a>

    <div class="terms-container">
        @if(isset($termsData) && $termsData['last_updated'] !== $termsData['effective_date'])
            <div class="update-banner">
                <div class="update-content">
                    <div class="update-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="update-text">
                        <strong>Terms Updated</strong>
                        <p>These terms were last updated on {{ $termsData['last_updated'] }} by {{ $termsData['updated_by'] }}</p>
                    </div>
                    <button class="update-dismiss" onclick="dismissUpdateBanner()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif        <div class="terms-header">
            <h1 class="terms-title">Terms of Service</h1>
            @if(isset($termsData))
                    @auth
                        @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.terms-of-service') }}" class="admin-edit-link">
                                <i class="fas fa-edit"></i>
                                Edit Terms
                            </a>
                        @endif
                    @endauth
            @endif
        </div>          @if(isset($termsData) && !empty($termsData['content']) && $termsData['content'] !== 'null')
            @php
                $content = $termsData['content'];
                $decodedContent = json_decode($content, true);
            @endphp
            
            @if($decodedContent && is_array($decodedContent) && isset($decodedContent['sections']))
                @foreach($decodedContent['sections'] as $index => $section)
                    <div class="section">
                        <h2 class="section-title">{{ $section['title'] ?? "Section " . ($index + 1) }}</h2>
                        <div class="section-content">
                            @if(isset($section['content']))
                                <p>{{ $section['content'] }}</p>
                            @endif
                            
                            @if(isset($section['list']) && is_array($section['list']))
                                <ul class="list">
                                    @foreach($section['list'] as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            
                            @if(isset($section['subsections']) && is_array($section['subsections']))
                                @foreach($section['subsections'] as $subsection)
                                    <div class="subsection">
                                        <h3 class="subsection-title">{{ $subsection['title'] ?? '' }}</h3>
                                        <p>{{ $subsection['content'] ?? '' }}</p>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                {{-- Display content as HTML or as formatted text --}}
                <div class="section">
                    <div class="section-content">
                        @if(strip_tags($content) != $content)
                            {{-- Content has HTML tags, display as HTML --}}
                            {!! $content !!}
                        @else
                            {{-- Content is plain text, convert line breaks --}}
                            {!! nl2br(e($content)) !!}
                        @endif
                    </div>
                </div>
            @endif
        @else
            <div class="section">
                <h2 class="section-title">1. Acceptance of Terms</h2>
                <div class="section-content">
                    <p>By accessing and using the Number Trivia Game platform ("Service"), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.</p>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">2. Description of Service</h2>
                <div class="section-content">
                    <p>Number Trivia Game is an online educational platform that provides:</p>
                    <ul class="list">
                        <li>Interactive number-based trivia questions</li>
                        <li>Educational content about mathematical facts</li>
                        <li>User account management and progress tracking</li>
                        <li>Leaderboards and performance statistics</li>
                        <li>Social features for comparing results with other users</li>
                    </ul>
                </div>
            </div>

        @endif

        @if(!isset($termsData) || empty($termsData['content']) || $termsData['content'] === 'null')
            <div class="section">
                <h2 class="section-title">3. User Accounts and Registration</h2>
                <div class="section-content">
                    <div class="subsection">
                        <h3 class="subsection-title">3.1 Account Creation</h3>
                        <p>To access certain features of our Service, you must register for an account. You agree to provide accurate, current, and complete information during the registration process.</p>
                    </div>
                    
                    <div class="subsection">
                        <h3 class="subsection-title">3.2 Account Security</h3>
                        <p>You are responsible for safeguarding the password and for maintaining the confidentiality of your account. You agree to notify us immediately of any unauthorized use of your account.</p>
                    </div>

                    <div class="subsection">
                        <h3 class="subsection-title">3.3 Age Requirements</h3>
                        <p>You must be at least 13 years old to use this Service. Users under 18 must have parental consent before using our platform.</p>
                    </div>
                </div>
            </div>

        @endif

        <div class="contact-info">
            <h3><i class="fas fa-envelope"></i> Contact Information</h3>
            <p>If you have any questions about these Terms of Service, please contact us:</p>
            <ul class="list" style="color: white;">
                <li><strong>Email:</strong> support@trivia.com</li>
                <li><strong>Address:</strong> Trivia Game </li>
                <!-- If you want to add a phone number, uncomment the line below
                     <li><strong>Phone:</strong> Available through our support email</li> 
                    -->
            </ul>
        </div>        <div class="effective-date">
            @if(isset($termsData))
                <p><strong>Effective Date:</strong> {{ $termsData['effective_date'] }}</p>
                <p><strong>Last Updated:</strong> {{ $termsData['last_updated'] }}</p>
            @else
                <p><strong>Effective Date:</strong> Error</p>
                <p><strong>Last Updated:</strong> Error</p>
            @endif
        </div></div>
</body>
</html>
</body>
</html>
