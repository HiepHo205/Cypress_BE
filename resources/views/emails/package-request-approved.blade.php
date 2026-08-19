<!DOCTYPE html>
<html>
<body>

<h2>
    Hello {{ $requestData['full_name'] }}
</h2>

<p>
    Your package request for <strong>{{ $requestData['plan_name'] ?? $requestData['requested_plan_name'] ?? 'your selected package' }}</strong> has been approved.
</p>

<p>
    Your current package is: <strong>{{ $requestData['current_package_name'] ?? $requestData['plan_name'] ?? 'your selected package' }}</strong>
</p>

<p>
    Valid from: <strong>{{ $requestData['current_package_started_at'] ?? 'now' }}</strong>
    to <strong>{{ $requestData['current_package_expired_at'] ?? 'the end of your selected period' }}</strong>
</p>

<p>
    Our team will contact you shortly for the next steps.
</p>

<p>
    Thank you for choosing Cypress.
</p>

</body>
</html>