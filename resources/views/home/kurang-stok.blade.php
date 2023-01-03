@extends('page.main.index')

@section('content')


<div class="shadow p-5 rounded">
    <form action="{{ route('home.proses-kurang-stok',['id' => $product->sku_id]) }}" method="POST">
        @csrf
        <h3 class="text-danger mb-3"><i class="fas fa-minus"></i>
            Pengurangan Stok 
        </h3>
        <hr class="mb-4">

        <div class="text-danger px-4" style="border-left: 5px solid red;">
            <h4>{{ $product->nm_barang }}</h4>
        <h6><i class="fas fa-key"></i> Kode SKU: {{ $product->sku_id }}</h6>
        </div>
        <hr class="mb-4 mt-4">

        @if(Session::has('messages'))
            <div class="shadow px-4 py-3 text-danger my-4 fw-bold" role="alert">
                <i class="fas fa-triangle-exclamation"></i>&nbsp; {{ Session::get('messages') }}
            </div>
        @endif


        <div class="row">
            <div class="mb-3 col-lg-4">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold"> Stok</label>
                <input type="text" name="qty" value="{{ old('qty') }}" class="form-control" id="exampleFormControlInput1" placeholder="Stok">
                @error('qty')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3 col-lg-8">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold"> Keterangan</label>
                <input type="text" name="keterangan" value="{{ old('keterangan') }}" class="form-control" id="exampleFormControlInput1" placeholder="Keterangan">
                @error('keterangan')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>
        </div>
        
        <div class="mt-4">
            <button onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();" class="btn btn-danger w-25 me-3">Kurangi Stok</button>
        <a href="{{ route('home') }}" class="btn btn-danger d-inline-block w-25">Kembali</a>
        </div>
    </form>
</div>

@endSection