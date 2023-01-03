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
        <h1>Product Yang Akan Expired 5 Bulan Lagi</h1>

        <table style="border-collapse: collapse; margin: auto; text-align:center;">
            <tr style="border: 1px solid black;">
                <th style="border: 1px solid black; padding: 20px 20px 20px 20px; box-sizing: border-box;">TANGGAL REKAM</th>
                <th style="border: 1px solid black; padding: 20px 20px 20px 20px; box-sizing: border-box;">KODE SKU</th>
                <th style="border: 1px solid black; padding: 20px 20px 20px 20px; box-sizing: border-box;">QTY</th>
                <th style="border: 1px solid black; padding: 20px 20px 20px 20px; box-sizing: border-box;">TANGGAL EXP</th>
            </tr>

            @foreach($data as $my)
            <tr style="border: 1px solid black; padding: 0 20px 0 20px; box-sizing: border-box;" >
                <td style="border: 1px solid black; padding: 0 20px 0 20px; box-sizing: border-box;">{{ $my->tanggal_rekam }}</td>
                <td style="border: 1px solid black; padding: 0 20px 0 20px; box-sizing: border-box;">{{ $my->sku_id }}</td>
                <td style="border: 1px solid black; padding: 0 20px 0 20px; box-sizing: border-box;">{{ $my->qty }}</td>
                <td style="border: 1px solid black; padding: 0 20px 0 20px; box-sizing: border-box;">{{ $my->tanggal_exp }}</td>
            </tr>
            @endforeach
        </table>
    </div>
</body>
</html>