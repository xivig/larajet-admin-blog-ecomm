<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Xivig Enterprise Admin Portal - Password Recovery">
    <title>Forgot Password - Xivig Control Center</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('xivig-blog/plugins/bootstrap-icons/bootstrap-icons.min.css') }}">

    <style>
        :root {
            --font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-heading: 'Outfit', system-ui, -apple-system, sans-serif;
            --color-primary: #6366f1;
            --color-secondary: #ec4899;
            --color-dark: #070a12;
            --color-surface: rgba(15, 23, 42, 0.82);
            --color-border: rgba(255, 255, 255, 0.08);
            --gradient-accent: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-sans);
            background-color: var(--color-dark);
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.18) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(236, 72, 153, 0.14) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(168, 85, 247, 0.08) 0px, transparent 60%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            color: #e2e8f0;
        }

        .auth-container {
            width: 100%;
            max-width: 460px;
            position: relative;
        }

        .auth-card {
            background: var(--color-surface);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid var(--color-border);
            border-radius: 24px;
            padding: 40px 36px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7);
            position: relative;
            overflow: hidden;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--gradient-accent);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #818cf8;
            font-size: 1.5rem;
            margin-bottom: 16px;
        }

        .brand-title {
            font-family: var(--font-heading);
            font-size: 1.6rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .brand-subtitle {
            font-size: 0.9rem;
            color: #94a3b8;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 1.1rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px 12px 42px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #ffffff;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 12px;
            background: var(--gradient-accent);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
        }

        .btn-submit:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 24px;
            color: #94a3b8;
            font-size: 0.88rem;
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #ffffff;
        }

        .alert-box {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.88rem;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="brand-header">
                <div class="brand-icon">
                    <i class="bi bi-key-fill"></i>
                </div>
                <h1 class="brand-title">Password Recovery</h1>
                <p class="brand-subtitle">Enter your administrative email to generate a secure password recovery token.</p>
            </div>

            @if(session('status'))
                <div class="alert-box alert-success">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('status') }}
                    @if(session('reset_url'))
                        <div style="margin-top: 8px;">
                            <a href="{{ session('reset_url') }}" style="color: #ffffff; text-decoration: underline; font-weight: 600;">Click here to Proceed to Password Reset</a>
                        </div>
                    @endif
                </div>
            @endif

            @if($errors->any())
                <div class="alert-box alert-danger">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.password.email') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Administrator Email</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="admin@xivig.com" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="bi bi-send me-1"></i> Send Password Reset Link
                </button>
            </form>

            <a href="{{ route('admin.login') }}" class="back-link">
                <i class="bi bi-arrow-left me-1"></i> Back to Admin Sign In
            </a>
        </div>
    </div>
</body>
</html>
