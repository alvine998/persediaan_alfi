@extends('page.main.index')

@section('content')


<h3 class="text-danger mb-3"><i class="fas fa-clock-rotate-left"></i>
    History 
</h3>
<hr class="mb-4">

<table class="table table-hover">
    <thead>
        <tr class="bg-danger text-light">
            <th scope="col">No.</th>
            <th scope="col">Aktivitas</th>
            <th scopr="col">Pada</th>
            <th scope="col">Status</th>
            <th scope="col">Jumlah Error</th>
        </tr>
    </thead>

    <tbody>
        @foreach($history as $h)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td class="fw-bold">
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
            </td>
            <td class="text-danger fw-bold">
                {{$h->created_at}}
            </td>
            <td>
                @if($h->status == 1)
                    Berhasil
                @else 
                    Gagal
                @endif
            </td>

            <td>
                @if($h->error_count == 0)
                    N/A
                @else 
                    {{ $h->error_count }}
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>



@endSection