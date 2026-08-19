<!DOCTYPE html>
<html>
<body>

<h2>
    Hello {{ $requestData['full_name'] }}
</h2>

<p>
    Unfortunately, your package request for <strong>{{ $requestData['plan_name'] ?? $requestData['requested_plan_name'] ?? 'your selected package' }}</strong> has been rejected.
</p>

<p>
    If you need further assistance, please contact us.
</p>

</body>
</html>