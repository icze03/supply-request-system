<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Supply Request System</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f5f5f3;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e8e8e4;
            border-radius: 14px;
            padding: 2.5rem 2.25rem;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 2rem;
        }
        .brand-icon {
            width: 32px;
            height: 32px;
            background: #1a1a1a;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .brand-name {
            font-size: 0.88rem;
            font-weight: 600;
            color: #1a1a1a;
        }

        .divider { height: 1px; background: #f0f0ec; margin-bottom: 1.75rem; }

        .heading { font-size: 1.25rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.3rem; letter-spacing: -0.01em; }
        .subheading { font-size: 0.8rem; color: #9a9a94; font-weight: 400; margin-bottom: 1.75rem; }

        .field { margin-bottom: 1rem; }
        .field-label { display: block; font-size: 0.75rem; font-weight: 500; color: #6a6a64; margin-bottom: 0.4rem; }
        .field-input {
            width: 100%;
            padding: 0.65rem 0.85rem;
            border: 1px solid #e4e4e0;
            border-radius: 8px;
            font-size: 0.85rem;
            font-family: 'DM Sans', sans-serif;
            color: #1a1a1a;
            background: #fafaf8;
            outline: none;
            transition: border-color 0.18s, box-shadow 0.18s;
        }
        .field-input::placeholder { color: #c4c4be; }
        .field-input:focus { border-color: #1a1a1a; background: #fff; box-shadow: 0 0 0 3px rgba(26,26,26,0.06); }
        .field-error { font-size: 0.72rem; color: #c05252; margin-top: 0.3rem; }

        .row { display: flex; justify-content: space-between; align-items: center; margin: 0.75rem 0 1.5rem; }
        .remember { display: flex; align-items: center; gap: 0.4rem; font-size: 0.78rem; color: #8a8a84; cursor: pointer; }
        .remember input { accent-color: #1a1a1a; cursor: pointer; }
        .forgot { font-size: 0.78rem; color: #8a8a84; text-decoration: none; transition: color 0.18s; }
        .forgot:hover { color: #1a1a1a; }

        .btn {
            width: 100%;
            padding: 0.7rem;
            background: #1a1a1a;
            color: #ffffff;
            font-size: 0.84rem;
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.18s, transform 0.15s;
        }
        .btn:hover { background: #2e2e2e; transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }

        .status { background: #f0fdf6; border: 1px solid #c3ddd1; border-radius: 8px; padding: 0.55rem 0.8rem; font-size: 0.78rem; color: #2a6a4a; margin-bottom: 1.25rem; }

        .footer { margin-top: 1.75rem; text-align: center; font-size: 0.68rem; color: #c4c4be; }
    </style>
</head>
<body>

    <div class="card">

        <div class="brand">
            <div class="brand-icon">
                <svg width="15" height="15" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/>
                </svg>
            </div>
            <span class="brand-name">Supply Request System</span>
        </div>

        <div class="divider"></div>

        <h1 class="heading">Sign in</h1>
        <p class="subheading">Enter your credentials to continue</p>

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="field">
                <label class="field-label" for="email">Email</label>
                <input id="email" type="email" name="email"
                    value="{{ old('email') }}"
                    class="field-input" placeholder="you@company.com"
                    required autofocus autocomplete="username">
                @error('email') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label class="field-label" for="password">Password</label>
                <input id="password" type="password" name="password"
                    class="field-input" placeholder="••••••••"
                    required autocomplete="current-password">
                @error('password') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="row">
                <label class="remember">
                    <input type="checkbox" name="remember"> Keep me signed in
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="btn">Sign In</button>
        </form>

        <p class="footer">Supply Request Management · v1.0.0</p>

    </div>

</body>
</html>