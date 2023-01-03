@extends('page.main.index')

@section('content')


<div class="shadow p-5 rounded">
    <form action="{{ route('home.proses-tambah-stok',['id' => $product->sku_id]) }}" method="POST">
        @csrf
        <h3 class="text-danger mb-3"><i class="fas fa-plus"></i>
            Tambah Stok 
        </h3>
        <hr class="mb-4">
        
        <div class="text-danger px-4" style="border-left: 5px solid red;">
            <h4>{{ $product->nm_barang }}</h4>
        <h6><i class="fas fa-key"></i> Kode SKU: {{ $product->sku_id }}</h6>
        </div>
        <hr class="mb-4 mt-4">
        
        <div class="row">
            <div class="mb-3 col-lg-5">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold">Penginput</label>
                <input type="text" name="penginput" value="{{ old('penginput') }}" class="form-control" id="exampleFormControlInput1" placeholder="Penginput">
                @error('penginput')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>
        
            <div class="mb-3 col-lg-5">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold">Tanggal</label>
                <input type="date" name="tanggal_transaksi" value="{{ old('tanggal_transaksi') }}" class="form-control" id="exampleFormControlInput1" placeholder="Tanggal">
                @error('tanggal_transaksi')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="mb-3 col-lg-3">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold">No. PO</label>
                <input type="text" name="no_transaksi" value="{{ old('no_transaksi') }}" class="form-control" id="exampleFormControlInput1" placeholder="No. PO">
                @error('no_transaksi')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>
        
            <div class="mb-3 col-lg-3">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold">Tanggal Expired</label>
                <input type="date" name="tanggal_exp" value="{{ old('tanggal_exp') }}" class="form-control" id="exampleFormControlInput1" placeholder="Tanggal Expired">
                @error('tanggal_exp')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>
        
            <div class="mb-3 col-lg-3">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold">Qty</label>
                <input type="number" name="qty" value="{{ old('qty') }}" class="form-control" id="exampleFormControlInput1" placeholder="Qty">
                @error('qty')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>
        </div>
        
        <div class="mt-4">
            <button onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();" class="btn btn-danger w-25 me-3">Tambahkan Stok</button>
            <a href="{{ route('home') }}" class="btn btn-danger d-inline-block w-25">Kembali</a>
        </div>
    </form>
</div>


@endSection