<!-- resources/views/pdf/download-template.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div>{!! $content !!}</div>
    <hr>
    <p>生成时间: {{ now()->format('Y-m-d H:i:s') }}</p>
</body>
</html>