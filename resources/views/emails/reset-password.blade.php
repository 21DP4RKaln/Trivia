<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            line-height: 1.6;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .email-header {
            background: linear-gradient(135deg, #10b981, #8b5cf6);
            padding: 40px 30px;
            text-align: center;
        }
        
        .email-logo {
            background: rgba(255, 255, 255, 0.2);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            backdrop-filter: blur(10px);
        }
        
        .email-logo svg {
            width: 40px;
            height: 40px;
            fill: white;
        }
        
        .email-title {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
        }
        
        .email-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            margin: 10px 0 0;
        }
        
        .email-body {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        
        .message {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }
          .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #10b981, #8b5cf6);
            color: white !important;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }
        
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4);
        }
        
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .security-info {
            background: #f8fafc;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 30px 0;
            border-radius: 0 10px 10px 0;
        }
        
        .security-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .security-text {
            color: #666;
            font-size: 14px;
        }
        
        .footer {
            background: #f8fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer-text {
            color: #9ca3af;
            font-size: 14px;
            margin: 0;
        }
        
        .link-fallback {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            word-break: break-all;
        }
        
        .link-fallback-text {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .link-url {
            color: #10b981;
            font-size: 12px;
            font-family: monospace;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            
            .email-header, .email-body, .footer {
                padding: 20px;
            }
            
            .email-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1 class="email-title">Password Reset</h1>
            <p class="email-subtitle">Trivia</p>
        </div>
        
        <div class="email-body">
            <div class="greeting">
                Hello{{ $user ? ', ' . $user->name : '' }}!
            </div>
            
            <div class="message">
                We received a request to reset your password for your Number Trivia Game account. If you made this request, click the button below to reset your password.
            </div>
            
            <div class="button-container">
                <a href="{{ route('password.reset', ['token' => $token, 'email' => $email]) }}" class="reset-button">
                    Reset Your Password
                </a>
            </div>
            
            <div class="security-info">
                <div class="security-title">Security Information</div>
                <div class="security-text">
                    This password reset link will expire in 60 minutes for your security. If you didn't request this password reset, you can safely ignore this email.
                </div>
            </div>
            
            <div class="link-fallback">
                <div class="link-fallback-text">If the button above doesn't work, copy and paste this link into your browser:</div>
                <div class="link-url">{{ route('password.reset', ['token' => $token, 'email' => $email]) }}</div>
            </div>
        </div>
        
        <div class="footer">
            <p class="footer-text">
                © {{ date('Y') }} Number Trivia Game. All rights reserved.<br>
                This is an automated email, please do not reply.
            </p>
        </div>
    </div>
</body>
</html>
