<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trivia Game - @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 90%;
            text-align: center;
        }
        
        h1 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 2.5rem;
        }
        
        h2 {
            color: #555;
            margin-bottom: 1rem;
        }
        
        .question {
            background: #f8f9ff;
            border-radius: 15px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            font-size: 1.2rem;
            color: #333;
            border-left: 5px solid #667eea;
        }
        
        .options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .option {
            background: #f0f0f0;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            font-weight: bold;
        }
        
        .option:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
            transform: translateY(-2px);
        }
        
        .option input[type="radio"] {
            display: none;
        }
        
        .option input[type="radio"]:checked + .option-text {
            background: #667eea;
            color: white;
        }
        
        .btn {
            background: #667eea;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 1rem 0.5rem;
            min-width: 150px;
        }
        
        .btn:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-danger {
            background: #dc3545;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-outline {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-outline:hover {
            background: #667eea;
            color: white;
        }
        
        .auth-status {
            background: #e8f4fd;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .auth-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .auth-actions .btn {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            min-width: auto;
        }
        
        @media (max-width: 768px) {
            .auth-status {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .auth-actions {
                flex-direction: column;
                width: 100%;
            }
        }
        
        .game-stats {
            background: #e8f4fd;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .stat {
            text-align: center;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }
        
        .result-box {
            border-radius: 15px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        
        .success {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
        }
        
        .error {
            background: #f8d7da;
            border: 2px solid #dc3545;
            color: #721c24;
        }
        
        .last-question {
            background: #fff3cd;
            border: 2px solid #ffc107;
            color: #856404;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
            text-align: left;
        }
        
        @media (max-width: 768px) {
            .options {
                grid-template-columns: 1fr;
            }
            
            .game-stats {
                flex-direction: column;
                gap: 1rem;
            }
            
            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @auth
            @if(auth()->user()->isAdmin())
                <div style="text-align: right; margin-bottom: 10px;">
                    <a href="{{ route('admin.dashboard') }}" style="color: #667eea; text-decoration: none; font-size: 12px; background: #f0f0f0; padding: 5px 10px; border-radius: 5px;">🔧 Admin Panel</a>
                </div>
            @endif
        @endauth
        @yield('content')
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const options = document.querySelectorAll('.option');
            const submitBtn = document.getElementById('submit-btn');
            
            options.forEach(option => {
                option.addEventListener('click', function() {
                    options.forEach(opt => opt.classList.remove('selected'));
                    
                    this.classList.add('selected');
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                        if (submitBtn) submitBtn.disabled = false;
                    }
                });
            });
        });
    </script>
    
    <style>
        .option.selected {
            background: #667eea !important;
            color: white !important;
            border-color: #667eea !important;
        }
        
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
    </style>
</body>
</html>
