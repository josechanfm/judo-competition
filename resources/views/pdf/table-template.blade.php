<!-- resources/views/pdf/table-template.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th { background-color: #4CAF50; color: white; padding: 12px; }
        .table td { padding: 10px; border: 1px solid #ddd; }
        .table tr:nth-child(even) { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1 style="text-align: center;">{{ $title }}</h1>
    
    <table class="table">
        <thead>
            <tr>
                @foreach($headers as $header)
                <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                @foreach($row as $cell)
                <td>{{ $cell }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>