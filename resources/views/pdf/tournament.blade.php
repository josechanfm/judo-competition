<!-- resources/views/pdf/with-header.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        .content { margin-top: 80px; }
        .product-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .product-table th { background-color: #f8f9fa; padding: 12px; text-align: left; }
        .product-table td { padding: 10px; border-bottom: 1px solid #dee2e6; }
        .price { color: #28a745; font-weight: bold; }
    </style>
</head>
<body>
    <div class="content">
        <h1>{{ $title }}</h1>
        
        <table class="product-table">
            <thead>
                <tr>
                    <th>产品名称</th>
                    <th>价格</th>
                    <th>描述</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>{{ $product['name'] }}</td>
                    <td class="price">{{ $product['price'] }}</td>
                    <td>{{ $product['description'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 30px; text-align: center;">
            <p>生成时间: {{ now()->format('Y年m月d日 H:i:s') }}</p>
        </div>
    </div>
</body>
</html>