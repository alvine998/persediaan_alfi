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
        <h1>Daftar Produk dan QR-Code</h1>

        <table style="border-collapse: collapse; margin: auto; text-align:center;">
            <tr style="border: 1px solid black;">
                <th style="border: 1px solid black; padding: 20px 20px 20px 20px; box-sizing: border-box;">NO.</th>
                <th style="border: 1px solid black; padding: 20px 20px 20px 20px; box-sizing: border-box;">KODE SKU</th>
                <th style="border: 1px solid black; padding: 20px 20px 20px 20px; box-sizing: border-box;">NAMA BARANG</th>
                <th style="border: 1px solid black; padding: 20px 20px 20px 20px; box-sizing: border-box;">QR-CODE</th>
            </tr>

            @foreach($product as $p)
            <tr style="border: 1px solid black; padding: 0 20px 0 20px; box-sizing: border-box;" >
                <td style="border: 1px solid black; padding: 0 20px 0 20px; box-sizing: border-box;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black; padding: 0 20px 0 20px; box-sizing: border-box;">{{ $p->sku_id }}</td>
                <td style="border: 1px solid black; padding: 0 20px 0 20px; box-sizing: border-box;">{{ $p->nm_barang }}</td>
                <td style="border: 1px solid black; padding: 0 20px 0 20px; box-sizing: border-box;">
                    <img style="margin: 35px 0 35px 0;" width="130" height="130" src="data:image/png;base64, {!! base64_encode(QrCode::size(200)->generate($p->sku_id)) !!} ">
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</body>
</html>