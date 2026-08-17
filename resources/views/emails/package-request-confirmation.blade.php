<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .button {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px 5px 10px 0;
            text-decoration: none;
            border-radius: 5px;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }
        .button-confirm {
            background-color: #4CAF50;
        }
        .button-confirm:hover {
            background-color: #45a049;
        }
        .button-keep {
            background-color: #2196F3;
        }
        .button-keep:hover {
            background-color: #0b7dda;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Hello {{ $requestData['full_name'] }}</h2>

    <p>
        You already have an active package: <strong>{{ $requestData['current_package'] ?? 'N/A' }}</strong>.
    </p>

    <p>
        You requested a change to package: <strong>{{ $requestData['requested_plan_name'] ?? 'your selected plan' }}</strong>.
    </p>

    <p>
        Your current package expires on: <strong>{{ $requestData['expired_at'] ?? 'soon' }}</strong>.
    </p>

    <p style="margin-top: 30px;">
        <strong>What would you like to do?</strong>
    </p>

    <p>
        <a href="{{ $requestData['confirm_url'] }}" class="button button-confirm">
            ✓ Confirm Change
        </a>
        <a href="{{ $requestData['keep_current_url'] }}" class="button button-keep">
            ✓ Keep Current Package
        </a>
    </p>

    <p style="margin-top: 20px; font-size: 12px; color: #666;">
        If you don't take any action, your current package will remain active.
    </p>
</div>

</body>
</html>
