<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
</head>
<body>
    <div style="text-align: center;">
        <h1>Produk</h1>
        <h2>{{ $product->nm_barang }}</h2>
        <h2 style="color: red;">{{ $product->sku_id }}</h2>

        <img style="margin: 35px 0 35px 0;" width="200" height="200" src="data:image/png;base64, {!! base64_encode(QrCode::size(200)->generate($product->sku_id)) !!} ">
    </div>
</body>
</html>