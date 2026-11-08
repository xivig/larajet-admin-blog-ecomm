<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Xivig Enterprise Admin Portal Registration">
    <title>Admin Registration - Xivig Control Center</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('xivig-blog/plugins/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--color-dark);
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.18) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(236, 72, 153, 0.14) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(168, 85, 247, 0.08) 0px, transparent 60%),
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 100% 100%, 40px 40px, 40px 40px;
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
            max-width: 480px;
            position: relative;
        }

        .auth-card {
            background: var(--color-surface);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid var(--color-border);
            border-radius: 24px;
            padding: 38px 36px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7), 0 0 0 1px rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient-accent);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 26px;
        }

        .brand-logo {
            font-family: var(--font-heading);
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 8px;
        }

        .admin-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #a5b4fc;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .admin-badge .pulse-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
        }

        .brand-title {
            font-family: var(--font-heading);
            font-size: 1.45rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .brand-subtitle {
            font-size: 0.88rem;
            color: #94a3b8;
            line-height: 1.5;
        }

        .alert-box {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 12px 14px;
            color: #fca5a5;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 8px;
            letter-spacing: 0.2px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i.input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 0.95rem;
            transition: color 0.2s;
        }

        .form-control {
            width: 100%;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 12px 14px 12px 42px;
            font-size: 0.92rem;
            color: #ffffff;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            background: rgba(15, 23, 42, 0.85);
        }

        .input-wrapper:focus-within i.input-icon {
            color: #818cf8;
        }

        .btn-submit {
            width: 100%;
            background: var(--gradient-accent);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 13px 20px;
            font-family: var(--font-heading);
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
            transition: all 0.25s ease;
            margin-top: 24px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(99, 102, 241, 0.55);
            filter: brightness(1.08);
        }

        .auth-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 0.82rem;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .auth-link {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .auth-link:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <!-- Brand Header -->
            <div class="brand-header">
                <div>
                    <span class="admin-badge">
                        <span class="pulse-dot"></span>
                        Admin Onboarding &bull; v13 Engine
                    </span>
                </div>
                <a href="{{ url('/') }}" class="brand-logo">Xivig</a>
                <h1 class="brand-title">Register Administrator</h1>
                <p class="brand-subtitle">Create a privileged operator profile with access to manage the Xivig system.</p>
            </div>

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="alert-box">
                    <i class="fas fa-exclamation-circle" style="margin-top: 2px;"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Registration Form -->
            <form method="POST" action="{{ route('admin.register') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="adminName">Full Name</label>
                    <div class="input-wrapper">
                        <i class="bi bi-person input-icon"></i>
                        <input 
                            type="text" 
                            name="name" 
                            id="adminName" 
                            class="form-control" 
                            value="{{ old('name') }}" 
                            required 
                            autofocus 
                            placeholder="John Doe"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="adminEmail">Administrator Email</label>
                    <div class="input-wrapper">
                        <i class="bi bi-envelope input-icon"></i>
                        <input 
                            type="email" 
                            name="email" 
                            id="adminEmail" 
                            class="form-control" 
                            value="{{ old('email') }}" 
                            required 
                            placeholder="admin@xivig.com"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="adminPassword">Password</label>
                    <div class="input-wrapper">
                        <i class="bi bi-shield-lock input-icon"></i>
                        <input 
                            type="password" 
                            name="password" 
                            id="adminPassword" 
                            class="form-control" 
                            required 
                            placeholder="Minimum 8 characters"
                            autocomplete="new-password"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="adminPasswordConfirm">Confirm Password</label>
                    <div class="input-wrapper">
                        <i class="bi bi-shield-check input-icon"></i>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="adminPasswordConfirm" 
                            class="form-control" 
                            required 
                            placeholder="Repeat password"
                            autocomplete="new-password"
                        >
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <span>Create Administrator Account</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <!-- Card Footer Links -->
            <div class="auth-footer">
                <a href="{{ url('/') }}" class="auth-link">
                    <i class="bi bi-arrow-left"></i> Return to Site
                </a>
                <a href="{{ route('admin.login') }}" class="auth-link">
                    Already an Admin? Sign In <i class="bi bi-box-arrow-in-right"></i>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
