@extends('page.main.index')

@section('content')


<h3 class="text-danger mb-3"><i class="fas fa-box"></i> &nbsp; 
    Master Data
</h3>
<hr class="mb-4">

@if(Session::has('messages'))
    <div class="shadow px-4 py-3 text-danger my-4 fw-bold" role="alert">
        <i class="fas fa-triangle-exclamation"></i>&nbsp; {{ Session::get('messages') }}
    </div>
@endif

<div class="table-responsive" style="height:400px; overflow: auto;">
  <table class="table table-hover align-middle">
    <thead>
      <tr class="align-middle bg-danger text-light">
        <th scope="col" >No</th>
        <th scope="col">Brand</th>
        <th scope="col">Kode SKU</th>
        <th scope="col">Nama Barang</th>
        <th scope="col">Kategori</th>
        <th scope="col">Ukuran</th>
        <th scope="col">Berat</th>
        <th scope="col">Panjang</th>
        <th scope="col">Lebar</th>
        <th scope="col">Tinggi</th>
        <th scope="col">Modal</th>
        <th scope="col">Jual</th>
        <th scope="col">Photo</th>
        <th scole="col">Aksi</th>
      </tr>
    </thead>
    <tbody>
        @foreach($product as $p)
            <tr>
                <th class="col-md-10" scope="row">{{ $loop->iteration }}</th>
                <td class="fw-bold text-danger">{{ $p->brand_id }}</td>
                <td>{{ $p->sku_id }}</td>
                <td class="fw-bold text-danger">{{ $p->nm_barang }}</td>
                <td>{{ $p->kategori }}</td>
                <td>{{ $p->ukuran }}</td>
                <td>{{ $p->berat }}</td>
                <td>{{ $p->panjang }}</td>
                <td>{{ $p->lebar }}</td>
                <td>{{ $p->tinggi }}</td>
                <td>{{ $p->harga_modal }}</td>
                <td>{{ $p->harga_jual }}</td>
                <td>
                    <img width=75 height=75 class="rounded" src="{{ $p->link_photo }}" alt="">
                </td>
                <td>
                  <a href="{{ route('delete-product',['id' => $p->sku_id]) }}" class="btn btn-danger">Hapus</a>
                </td>
            </tr>
        @endforeach
    </tbody>
  </table>
</div>

@endSection