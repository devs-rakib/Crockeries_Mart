<?php
/**
 * 404 Not Found Page - CrokersesMart
 * Standalone page (does NOT include header/footer)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --cm-primary: #ff3838;
            --cm-primary-dark: #e02020;
            --cm-dark: #1a1a2e;
            --cm-gray-500: #6c757d;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; }

        .error-page {
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            position: relative; overflow: hidden;
        }
        .error-page::before {
            content: ''; position: absolute; top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle at 30% 50%, rgba(255,56,56,.08) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(255,56,56,.05) 0%, transparent 40%);
            animation: float 15s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-20px, -20px); }
        }

        .error-content { text-align: center; position: relative; z-index: 1; padding: 40px 20px; }

        .error-code {
            font-size: clamp(100px, 20vw, 200px); font-weight: 900;
            background: linear-gradient(135deg, var(--cm-primary), #ff6b6b);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; line-height: 1; margin-bottom: 16px;
            text-shadow: none; position: relative;
            animation: glitch 3s infinite;
        }
        @keyframes glitch {
            0%, 90%, 100% { transform: none; }
            91% { transform: translate(-2px, 1px); }
            93% { transform: translate(2px, -1px); }
            95% { transform: translate(-1px, 2px); }
            97% { transform: translate(1px, -2px); }
        }

        .error-title {
            font-size: clamp(24px, 4vw, 36px); font-weight: 700;
            color: #fff; margin-bottom: 12px;
        }
        .error-text {
            font-size: 16px; color: rgba(255,255,255,.6); max-width: 440px;
            margin: 0 auto 36px; line-height: 1.6;
        }

        .btn-home {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 36px; border: none; border-radius: 8px;
            background: var(--cm-primary); color: #fff;
            font-size: 15px; font-weight: 700; text-decoration: none;
            transition: all .25s ease; position: relative; z-index: 1;
        }
        .btn-home:hover { background: var(--cm-primary-dark); color: #fff; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,56,56,.35); }

        .error-icon { font-size: 48px; color: rgba(255,255,255,.15); margin-bottom: 20px; }

        .decoration-circle {
            position: absolute; border-radius: 50%; border: 2px solid rgba(255,56,56,.1);
        }
        .circle-1 { width: 300px; height: 300px; top: -80px; right: -80px; }
        .circle-2 { width: 200px; height: 200px; bottom: -60px; left: -60px; }
        .circle-3 { width: 150px; height: 150px; top: 40%; left: 10%; border-color: rgba(255,255,255,.05); }
    </style>
</head>
<body>

<div class="error-page">
    <div class="decoration-circle circle-1"></div>
    <div class="decoration-circle circle-2"></div>
    <div class="decoration-circle circle-3"></div>

    <div class="error-content">
        <div class="error-icon">
            <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div class="error-code">404</div>
        <h1 class="error-title">Page Not Found</h1>
        <p class="error-text">The page you're looking for doesn't exist or has been moved.</p>
        <a href="<?= APP_URL ?>" class="btn-home">
            <i class="bi bi-house-fill"></i>
            Go Home
        </a>
    </div>
</div>

</body>
</html>
