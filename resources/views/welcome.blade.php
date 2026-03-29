<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        min-height: 100vh;
        font-family: "Segoe UI", Arial, sans-serif;
        background: #f3f3f3;
        color: #1f2937;
    }

    .welcome-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 32px 20px;
    }

    .welcome-card {
        width: 100%;
        max-width: 1080px;
        min-height: 460px;
        background: #fff;
        border: 1px solid #d9d9d9;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.06);
        display: grid;
        grid-template-columns: 1.1fr 1fr;
    }

    .welcome-left {
        padding: 56px 52px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: #ffffff;
    }

    .welcome-right {
        position: relative;
        background:
            linear-gradient(135deg, rgb(177, 206, 236, 0.08), rgba(255, 91, 46, 0.02)),
            #f4f5fa;
        border-left: 1px solid #e8d7d1;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 32px;
    }

    .brand-tag {
        display: inline-block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #312682;
        background: #f1f2f7;
        border: 1px solid #312682;
        border-radius: 999px;
        padding: 7px 12px;
        margin-bottom: 18px;
    }

    .brand-row {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 14px;
    }

    .brand-logo {
        position: relative;
        width: 100%;
        max-width: 60px;
        aspect-ratio: 1 / 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .brand-title-wrap h1 {
        margin: 0;
        font-size: 28px;
        line-height: 1.1;
        color: #111827;
        font-weight: 800;
    }

    .brand-subtitle {
        margin-top: 4px;
        color: #6b7280;
        font-size: 14px;
        font-weight: 500;
    }

    .info-list {
        display: grid;
        gap: 12px;
        margin-bottom: 26px;
    }

    .info-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 15px;
        color: #374151;
    }

    .info-icon {
        width: 30px;
        height: 30px;
        min-width: 30px;
        border-radius: 50%;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .feature-box {
        margin-top: 2px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .feature-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fafafa;
        padding: 14px 15px;
    }

    .feature-card h3 {
        margin: 0 0 6px;
        font-size: 14px;
        font-weight: 700;
        color: #111827;
    }

    .feature-card p {
        margin: 0;
        font-size: 13px;
        line-height: 1.55;
        color: #6b7280;
    }

    .auth-actions {
        margin-top: 30px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn-main,
    .btn-outline {
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 140px;
        padding: 12px 18px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 700;
        transition: all .18s ease;
    }

    .btn-main {
        background: #161616;
        color: #fff;
        border: 1px solid #161616;
    }

    .btn-main:hover {
        background: #000;
    }

    .btn-outline {
        background: #fff;
        color: #111827;
        border: 1px solid #d1d5db;
    }

    .btn-outline:hover {
        background: #f9fafb;
    }

    .right-top-text {
        position: absolute;
        top: 18px;
        left: 22px;
        right: 22px;
        font-size: 66px;
        line-height: .9;
        font-weight: 800;
        color: rgb(38, 42, 142, 0.92);
        letter-spacing: -3px;
        user-select: none;
        pointer-events: none;
    }

    .right-graphic {
        position: relative;
        width: 100%;
        max-width: 380px;
        aspect-ratio: 1 / 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }


    .center-logo {
        position: absolute;
        max-width: 80%;
        object-fit: contain;
        z-index: 5;
    }

    .footer-note {
        margin-top: 18px;
        font-size: 13px;
        color: #9ca3af;
    }

    @media (max-width: 900px) {
        .welcome-card {
            grid-template-columns: 1fr;
        }

        .welcome-right {
            min-height: 320px;
            border-left: none;
            border-top: 1px solid #e8d7d1;
        }

        .welcome-left {
            padding: 34px 24px;
        }

        .right-top-text {
            font-size: 58px;
        }

        .feature-box {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 560px) {
        .brand-row {
            align-items: flex-start;
        }

        .brand-title-wrap h1 {
            font-size: 28px;
        }

        .right-top-text {
            font-size: 44px;
        }

        .auth-actions a {
            width: 100%;
        }
    }
    </style>
</head>

<body>
    <div class="welcome-page">
        <div class="welcome-card">

            {{-- LEFT SIDE --}}
            <div class="welcome-left">
                <span class="brand-tag">Welcome</span>

                <div class="brand-row">
                    <div class="brand-logo">
                        <img src="{{ asset('images/grow.png') }}" alt="Company Logo" class="center-logo">
                    </div>
                    <div class="brand-title-wrap">
                        <h1>Grow Bridges Pvt. Ltd.</h1>
                        <div class="brand-subtitle">Connecting to Grow</div>
                    </div>
                </div>

                <div class="info-list">
                    <div class="info-item">
                        <div class="info-icon">📍</div>
                        <div><strong>Japan Office</strong> Tokeyo, Japan / +81-80-1234-5678 /
                            info@growbridges.com</div>
                    </div>
                </div>

                <div class="feature-box">
                    <div class="feature-card">
                        <h3>About Grow Bridges pvt. ltd.</h3>
                        <p>GrowBridges Pvt. Ltd. is a Japan based support and placement company dedicated to
                            international students.</p>
                    </div>
                    <div class="feature-card">
                        <h3>About the Web App</h3>
                        <p>Handels and Manage Student Files.</p>
                    </div>
                </div>

                @if (Route::has('login'))
                <div class="auth-actions">
                    @auth
                    <a href="{{ url('/dashboard') }}" class="btn-main">Go to Dashboard</a>
                    @else
                    <a href="{{ route('login') }}" class="btn-main">Log in</a>

                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-outline">Register</a>
                    @endif
                    @endauth
                </div>
                @endif

                <div class="footer-note">
                    &copy; {{ date('Y') }} Grow Bridges Pvt. Ltd. All rights reserved.
                </div>
            </div>

            {{-- RIGHT SIDE --}}
            <div class="welcome-right">
                <div class="right-top-text">Grow Bridges</div>

                <div class="right-graphic">
                    <img src="{{ asset('images/grow.png') }}" alt="Company Logo" class="center-logo">
                </div>
            </div>

        </div>
    </div>
</body>

</html>