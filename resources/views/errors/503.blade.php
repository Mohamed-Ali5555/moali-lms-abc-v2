<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --color-1: #2f57ef;
            --main-color: #c664ff;
            --color-2: #192335;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0d1526;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated background blobs */
        .bg-blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.25;
            animation: float 8s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }
        .bg-blob-1 {
            width: 500px; height: 500px;
            background: var(--color-1);
            top: -150px; left: -150px;
            animation-delay: 0s;
        }
        .bg-blob-2 {
            width: 400px; height: 400px;
            background: var(--main-color);
            bottom: -100px; right: -100px;
            animation-delay: 3s;
        }
        .bg-blob-3 {
            width: 300px; height: 300px;
            background: var(--color-1);
            bottom: 100px; left: 10%;
            animation-delay: 5s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }

        /* Floating particles */
        .particles {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .particle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-1), var(--main-color));
            opacity: 0.4;
            animation: rise linear infinite;
        }
        .particle:nth-child(1)  { width:8px;  height:8px;  left:10%; animation-duration:12s; animation-delay:0s;  }
        .particle:nth-child(2)  { width:5px;  height:5px;  left:25%; animation-duration:9s;  animation-delay:2s;  }
        .particle:nth-child(3)  { width:10px; height:10px; left:40%; animation-duration:14s; animation-delay:1s;  }
        .particle:nth-child(4)  { width:6px;  height:6px;  left:60%; animation-duration:11s; animation-delay:3s;  }
        .particle:nth-child(5)  { width:4px;  height:4px;  left:75%; animation-duration:8s;  animation-delay:0.5s;}
        .particle:nth-child(6)  { width:9px;  height:9px;  left:88%; animation-duration:13s; animation-delay:4s;  }
        .particle:nth-child(7)  { width:5px;  height:5px;  left:50%; animation-duration:10s; animation-delay:6s;  }

        @keyframes rise {
            0%   { transform: translateY(110vh) rotate(0deg);   opacity: 0;   }
            10%  { opacity: 0.4; }
            90%  { opacity: 0.4; }
            100% { transform: translateY(-10vh)  rotate(720deg); opacity: 0; }
        }

        /* Main card */
        .maintenance-card {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 60px 50px;
            max-width: 680px;
            width: 90%;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4);
        }

        /* Gear icon animation */
        .gear-wrapper {
            margin-bottom: 30px;
            position: relative;
            display: inline-block;
        }
        .gear-icon {
            font-size: 80px;
            display: inline-block;
            animation: spin 6s linear infinite;
            background: linear-gradient(135deg, var(--color-1), var(--main-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }
        .gear-icon-small {
            font-size: 45px;
            display: inline-block;
            animation: spin-reverse 4s linear infinite;
            background: linear-gradient(135deg, var(--main-color), var(--color-1));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: absolute;
            bottom: -10px;
            right: -20px;
            line-height: 1;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        @keyframes spin-reverse {
            from { transform: rotate(0deg); }
            to   { transform: rotate(-360deg); }
        }

        /* Gradient heading */
        .maintenance-title {
            font-size: 38px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 16px;
            background: linear-gradient(to right, var(--color-1) 0%, var(--main-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .maintenance-subtitle {
            font-size: 16px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        .maintenance-desc {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.5);
            line-height: 1.7;
            margin-bottom: 40px;
        }

        /* Divider */
        .gradient-divider {
            width: 80px;
            height: 3px;
            background: linear-gradient(to right, var(--color-1), var(--main-color));
            border-radius: 2px;
            margin: 20px auto 30px;
        }

        /* Progress bar */
        .progress-wrapper {
            margin-bottom: 36px;
        }
        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: rgba(255,255,255,0.5);
            margin-bottom: 8px;
        }
        .progress-bar-bg {
            background: rgba(255,255,255,0.08);
            border-radius: 50px;
            height: 8px;
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            border-radius: 50px;
            background: linear-gradient(to right, var(--color-1), var(--main-color));
            width: 0%;
            animation: progress-grow 3s ease-out forwards;
        }
        @keyframes progress-grow {
            0%   { width: 0%; }
            100% { width: 72%; }
        }

        /* Info badges */
        .badges {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 36px;
        }
        .badge-item {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 50px;
            padding: 8px 18px;
            font-size: 13px;
            color: rgba(255,255,255,0.75);
        }
        .badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-1), var(--main-color));
            flex-shrink: 0;
            animation: pulse-dot 2s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50%       { transform: scale(1.4); opacity: 0.6; }
        }

        /* Back soon pulse ring */
        .pulse-ring-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 13px;
            color: rgba(255,255,255,0.4);
        }
        .pulse-dot-lg {
            position: relative;
            width: 12px;
            height: 12px;
        }
        .pulse-dot-lg::before,
        .pulse-dot-lg::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: var(--main-color);
        }
        .pulse-dot-lg::after {
            animation: ripple 2s ease-out infinite;
            background: transparent;
            border: 2px solid var(--main-color);
        }
        @keyframes ripple {
            0%   { transform: scale(1);   opacity: 1; }
            100% { transform: scale(3);   opacity: 0; }
        }

        @media (max-width: 576px) {
            .maintenance-card {
                padding: 40px 24px;
            }
            .maintenance-title {
                font-size: 28px;
            }
            .gear-icon {
                font-size: 60px;
            }
        }
    </style>
</head>
<body>

    <!-- Background blobs -->
    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>
    <div class="bg-blob bg-blob-3"></div>

    <!-- Floating particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Main content -->
    <div class="maintenance-card">

        <div class="gear-wrapper">
            <span class="gear-icon">⚙</span>
            <span class="gear-icon-small">⚙</span>
        </div>

        <h1 class="maintenance-title">Under Maintenance</h1>

        <p class="maintenance-subtitle">We're working hard to improve your experience</p>

        <div class="gradient-divider"></div>

        <p class="maintenance-desc">
            Our platform is currently undergoing scheduled maintenance.<br>
            We'll be back online shortly with new improvements and updates.
        </p>

        <div class="progress-wrapper">
            <div class="progress-label">
                <span>Progress</span>
                <span>72%</span>
            </div>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill"></div>
            </div>
        </div>

        <div class="badges">
            <div class="badge-item">
                <div class="badge-dot"></div>
                Upgrading System
            </div>
            <div class="badge-item">
                <div class="badge-dot"></div>
                Improving Performance
            </div>
            <div class="badge-item">
                <div class="badge-dot"></div>
                Adding New Features
            </div>
        </div>

        <div class="pulse-ring-wrapper">
            <div class="pulse-dot-lg"></div>
            <span>We'll be back soon — thank you for your patience!</span>
        </div>

    </div>

</body>
</html>
