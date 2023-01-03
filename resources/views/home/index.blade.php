@extends('page.main.index')

@section('content')


<h3 class="text-danger mb-3"><i class="fas fa-home"></i>
    Data 
</h3>
<hr class="mb-4">

@if(Session::has('messages'))
    <div class="shadow px-4 py-3 text-danger my-4 fw-bold" role="alert">
        <i class="fas fa-triangle-exclamation"></i>&nbsp; {{ Session::get('messages') }}
    </div>
@endif


@foreach($product as $p)
<div class="rounded shadow p-3 my-4">
    <div class="row">
        <div class="col-lg-2">
            <img width=120 height=120 class="rounded" src="{{ $p->link_photo }}" alt="">
        </div>

        <div class="col-lg-2 align-self-center">
            <small class="p-2 text-danger fw-bold border rounded text-center d-block m-auto">{{ $p->brand_id }}</small>
        </div>

        <div class="col-lg-5">
            <h6>{{ $p->sku_id }}</h6>
            <hr> 
            <div class="d-flex justify-content-between fw-bold">
                <small class="text-danger">Lokasi {{ $p->lokasi }}</small>
                <small class="text-danger">Harga Modal: Rp. {{ number_format($p->harga_modal,0,',','.') }}</small>
            </div>

            <div class="d-flex justify-content-between mt-3 fw-bold">
                @php
                    $getStock = DB::select("SELECT * FROM stock WHERE stock.sku_id='{$p->sku_id}'");
                    $countStock = 0;
                    foreach($getStock as $s){
                        $countStock += $s->qty;
                    }
                @endphp
                <small class="text-danger">Stok: {{ $countStock }}</small>
                <small class="text-danger">Harga Jual: Rp. {{ number_format($p->harga_jual,0,',','.') }}</small>
            </div>
        </div>

        <div class="col-lg-3 align-self-center">
            <div class="d-flex">
                <a href="{{ route('home.tambah-stok',['id' => $p->sku_id]) }}" class="fs-6 btn btn-sm btn-danger d-inline-block w-25 py-3 me-2 rounded-circle m-auto"><i class="fas fa-plus"></i> <i class="fas fa-box"></i></a>

                <a href="{{ route('home.kurang-stok',['id' => $p->sku_id]) }}" class="fs-6 btn btn-sm btn-danger d-inline-block w-25 py-3 me-2 rounded-circle m-auto"><i class="fas fa-minus"></i> <i class="fas fa-box"></i></a>

                <a href="{{ route('home.cetak-qrcode',['id' => $p->sku_id]) }}" target="_blank" class="fs-6 btn btn-sm btn-danger d-inline-block w-25 py-3 me-2 rounded-circle m-auto"><i class="fas fa-qrcode"></i></a>
            </div>
        </div>
    </div>
</div>
@endforeach

@if($totalproduct != count($product))
<a href="{{ route('home') }}" class="btn btn-danger w-50 rounded-pill d-block m-auto my-5" href=""><i class="fas fa-spinner"></i> Klik Tombol Ini Untuk Load Ulang Seluruh Produk Anda</a>
@endif



@endSection