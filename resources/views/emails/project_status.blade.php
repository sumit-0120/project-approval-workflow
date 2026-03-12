
<!DOCTYPE html>
<html>
<head>
    <title>Project Status</title>
</head>
<body>

    <h2>Project Status Update</h2>

    <p><strong>Project:</strong> {{ $project->title }}</p>

    <p><strong>Status:</strong> {{ ucfirst($status) }}</p>

    @if($reason)
    <p><strong>Reason:</strong> {{ $reason }}</p>
    @endif

    <p><strong>Date:</strong> {{ now() }}</p>

</body>
</html>