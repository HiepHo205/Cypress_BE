<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        .icon-success {
            background-color: #d4edda;
            color: #28a745;
            font-size: 48px;
        }
        .icon-error {
            background-color: #f8d7da;
            color: #dc3545;
            font-size: 48px;
        }
        h1 {
            font-size: 28px;
            margin-bottom: 15px;
            color: #333;
        }
        .title-success {
            color: #28a745;
        }
        .title-error {
            color: #dc3545;
        }
        .message {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        button, a {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-close {
            background-color: #6c757d;
            color: white;
        }
        .btn-close:hover {
            background-color: #5a6268;
        }
        .btn-home {
            background-color: #667eea;
            color: white;
        }
        .btn-home:hover {
            background-color: #5568d3;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        @if($success)
            <div class="icon icon-success">✓</div>
            <h1 class="title-success">{{ $title }}</h1>
        @else
            <div class="icon icon-error">✕</div>
            <h1 class="title-error">{{ $title }}</h1>
        @endif

        <p class="message">{{ $message }}</p>

        <div class="actions">
            <button onclick="window.close()" class="btn-close">Close This Window</button>
            <a href="/login" class="btn-home">Go to Login</a>
        </div>

        <div class="footer">
            You can close this window now or go back to login.
        </div>
    </div>

    <script>
        // Try to close the window after 3 seconds (may not work due to browser security)
        setTimeout(() => {
            // Only auto-close if user didn't interact yet
            if (document.hidden) {
                window.close();
            }
        }, 3000);
    </script>
</body>
</html>
