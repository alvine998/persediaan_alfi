@extends('page.main.index')

@section('content')

<h3 class="text-danger mb-4"><i class="fas fa-spinner"></i> &nbsp;  
    @if($sub == '' || $sub == 'input-produk')
    Sync ( <a href="{{ route('sync-sub',['sub' => 'input-produk']) }}" class="ms-2 text-danger text-decoration-none"><i class="fas fa-boxes"></i> Bagian Input Produk Massal</a> )
    @elseif($sub == 'tambah-produk-massal')
    Sync ( <a href="{{ route('sync-sub',['sub' => 'tambah-produk-massal']) }}" class="ms-2 text-danger text-decoration-none"><i class="fas fa-plus"></i> Bagian Tambah Stok Massal</a> )
    @elseif($sub == 'kurang-produk-massal')
    Sync ( <a href="{{ route('sync-sub',['sub' => 'pemotongan-produk-massal']) }}" class="ms-2 text-danger text-decoration-none"><i class="fas fa-minus"></i> Pengurangan Stok Massal</a> )
    @endif</h3>
</h3>

<hr class="my-4">

@if(Session::has('messages'))
    <div class="shadow px-4 py-3 text-danger my-4 fw-bold" role="alert">
        <i class="fas fa-triangle-exclamation"></i>&nbsp; {{ Session::get('messages') }}
    </div>
@endif

<select class="form-select my-4 w-50 selects" aria-label="Default select example">
    @if($sub == 'input-produk' || $sub == '')
    <option value="1" selected data-routes="{{ route('sync-sub',['sub' => 'input-produk']) }}">Input Produk Massal</option>
    <option value="2" data-routes="{{ route('sync-sub',['sub' => 'tambah-produk-massal']) }}">Tambah Stok Massal</option>
    <option value="3" data-routes="{{ route('sync-sub',['sub' => 'kurang-produk-massal']) }}">Pengurangan Stok Massal</option>
    @elseif($sub == 'tambah-produk-massal')
    <option value="1" data-routes="{{ route('sync-sub',['sub' => 'input-produk']) }}">Input Produk Massal</option>
    <option value="2" selected data-routes="{{ route('sync-sub',['sub' => 'tambah-produk-massal']) }}">Tambah Stok Massal</option>
    <option value="3" data-routes="{{ route('sync-sub',['sub' => 'kurang-produk-massal']) }}">Pengurangan Stok Massal</option>
    @elseif($sub == 'kurang-produk-massal')
    <option value="1" data-routes="{{ route('sync-sub',['sub' => 'input-produk']) }}">Input Produk Massal</option>
    <option value="2"  data-routes="{{ route('sync-sub',['sub' => 'tambah-produk-massal']) }}">Tambah Stok Massal</option>
    <option value="3" selected  data-routes="{{ route('sync-sub',['sub' => 'kurang-produk-massal']) }}">Kurang Stok Massal</option>
    @endif
    
</select>

<hr class="my-4">

{{-- List Of Submenu --}}
{{-- <ul class="py-4 rounded border">

    <li class="list-unstyled d-inline-block">
        <a href="{{ route('sync-sub',['sub' => 'input-produk']) }}" class="{{ $sub == 'input-produk' || $sub == ''  ? 'link-active' : '' }} text-decoration-none {{ $sub == 'input-produk' || $sub == ''  ? 'text-light' : 'text-secondary' }} px-4 py-2 rounded "><i class="fas fa-boxes"></i> Input Produk</a>
        <a href="{{ route('sync-sub',['sub' => 'tambah-produk-massal']) }}" class="{{ $sub == 'tambah-produk-massal' ? 'link-active' : '' }} text-decoration-none {{ $sub == 'tambah-produk-massal' ? 'text-light' : 'text-secondary' }} px-4 py-2 rounded "><i class="fas fa-plus"></i> Tambah Produk Massal</a>
        <a href="{{ route('sync-sub',['sub' => 'pemotongan-produk-massal']) }}" class="{{ $sub == 'pemotongan-produk-massal' ? 'link-active' : '' }} text-decoration-none {{ $sub == 'pemotongan-produk-massal' ? 'text-light' : 'text-secondary' }} px-4 py-2 rounded "><i class="fas fa-minus"></i> Pemotongan Produk Massal</a>
    </li>
    
</ul> --}}
{{-- End Of List Submenu --}}

<div class="sub-content my-4">
    @if($sub == '' || $sub == 'input-produk')
        @include('sync.sub.input-produk')
    @elseif($sub == 'tambah-produk-massal')
        @include('sync.sub.tambah-produk-massal')
    @elseif($sub == 'kurang-produk-massal')
        @include('sync.sub.kurang-produk-massal')
    @endif
</div>

<hr class="mt-4">

<div class="mt-5" style="height: 300px; overflow: auto;">
<table class="table table-hover">
    <thead>
        <tr class="bg-danger text-light">
            <th scope="col">No.</th>
            <th scope="col">Aktivitas</th>
            <th scopr="col">Pada</th>
            <th scope="col">Status</th>
            <th scope="col">Template</th>
        </tr>
    </thead>

    <tbody>
        @foreach($history as $h)
        <tr>
            <td class="align-middle">{{ $loop->iteration }}</td>
            <td class="fw-bold align-middle">
                @if($h->type == 1)
                    Melakukan Input Produk Massal
                @elseif($h->type == 2)
                    Menambahkan Stok Secara Massal
                @elseif($h->type == 3)
                    Mengurangi Stok Secara Massal
                @elseif($h->type == 4)
                    Menambahkan Stok Secara Tunggal
                @elseif($h->type == 5)
                    Mengurangi Stok Secara Tunggal
                @endif
            </td class="align-middle">
            <td class="text-danger fw-bold align-middle">
                {{$h->created_at}}
            </td>
            <td class="align-middle">
                @if($h->status == 1)
                    Berhasil
                @else 
                    Gagal
                @endif

                @if($h->status == 1 && $h->error_count > 0)
                <h6 class="text-danger">Dengan {{ $h->error_count }} Error</h6>
                @endif
            </td>

            <td class="align-middle">
                <a href="{{ route('sync.product.template-export',['id' => $h->history_id]) }}" class="my-3 btn btn-sm btn-danger w-100 "><i class="fas fa-download"></i> Template</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

@endSection



