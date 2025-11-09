<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body { font-family: sans-serif; padding: 40px; }
        h1 { color: #2b2b2b; }
        .total { margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>
<h1>Invoice for {{ $user }}</h1>
<h3>مرحبا</h3>
<p>Thank you for your payment.</p>
<p class="total">Total: ${{ $amount }}</p>
</body>
</html>
