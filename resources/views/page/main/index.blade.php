<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('assets/css/index.css') }}">
  </head>
  <body>

    {{-- Menu Bar --}}
    <nav style="position: sticky: top: 0; "  class="navbar navbar-expand-lg bg-danger text-light sticky-top">
      <div class="container text-light">
        <a class="navbar-brand text-light fw-bold" href="#"><i class="fas fa-box"></i>&nbsp;SIMULASI INVENTORY</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
         
          </ul>
          @php
              $getDateExpired = DB::select("SELECT * FROM expired WHERE expired.is_seen != 1");
          @endphp
          <form action="{{ route('home') }}" method="GET" class="d-flex" role="search">
            @csrf
            <input class="form-control me-2" value="" name="search" type="search" placeholder="Cari Produk" aria-label="Search">
            <a data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-light rounded-circle text-danger notification" data-route="{{ route('disable-notification') }}" id="bell-trigger" type="submit">
              @if(count($getDateExpired) != 0)
              <small class="bell-notification">{{count($getDateExpired)}}</small> 
              @endif
              <i class="fas fa-bell"></i></a>
            
          </form>
          
        </div>
      </div>
    </nav>


<!-- Modal Menampilkan tanggal product yang expired -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Product Yang 5 Bulan Lagi Expired</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @php
            $expired = DB::select("SELECT * FROM expired ORDER BY expired.expired_id DESC");
        @endphp

      @if(count($expired) != 0)
      <table class="table table-hover">
        <thead>
            <tr class="bg-danger text-light">
                <th scope="col">No.</th>
                <th scope="col">Tanggal Rekam</th>
                <th scopr="col">Total Expired</th>
                <th scope="col">Export</th>
            </tr>
        </thead>
        @foreach($expired as $exp)
          {{-- looping detailnya --}}
            @php
                $getProductExpired = DB::select("SELECT * FROM expired_detail INNER JOIN stock ON expired_detail.stock_id = stock.stock_id WHERE expired_detail.expired_id='{$exp->expired_id}'");
            @endphp

        <tr>
          <td class="align-middle">{{ $loop->iteration }}</td>
          <td class="align-middle">{{ $exp->tanggal_rekam }}</td>
          <td class="align-middle">{{ count($getProductExpired) }}</td>
          <td class="align-middle">
              <a target="_blank" href="{{ route('sync.product.template-expired-export',['id' => $exp->expired_id]) }}" class="my-1 btn btn-sm btn-danger w-100 "><i class="fas fa-download"></i> Export Data</a>
          </td>
        </tr>
        @endforeach
      </table>
      @else 
      <div class="text-danger text-center my-4">
        <i class="fas fa-triangle-exclamation d-block fs-1 mb-4"></i>
        <h2>Belum Ada Data Expired</h2>

      </div>
      @endif


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

    {{-- Sidebar and content ( left and right side ) --}}
<div style=""  class="contents d-flex">
    {{-- Sidebar --}}
    <div style="position: sticky; top: 50px; height: 100px;"  class="col-lg-2 pt-4 px-3 border-end-gradients">
        <a class="{{ $title == 'Home' ? 'link-active' : '' }} text-decoration-none p-3 rounded {{ $title == 'Home' ? 'text-light' : 'text-secondary' }} w-100 d-block m-auto" href="{{ route('home') }}"><i class="fas fa-home"></i>&nbsp; Data</a>

        <a class="{{ $title == 'Sync' ? 'link-active' : '' }} text-decoration-none p-3 rounded {{ $title == 'Sync' ? 'text-light' : 'text-secondary' }} w-100 d-block m-auto" href="{{ route('sync') }}"><i class="fas fa-spinner"></i>&nbsp; Sync</a>

        <a class="{{ $title == 'Master' ? 'link-active' : '' }} text-decoration-none p-3 rounded {{ $title == 'Master' ? 'text-light' : 'text-secondary' }} w-100 d-block m-auto" href="{{ route('home.master') }}"><i class="fas fa-box"></i>&nbsp; Master Data</a> 

        <a class="text-decoration-none p-3 rounded text-secondary w-100 d-block m-auto" href="{{ route('auth.logout') }}"><i class="fas fa-arrow-right-from-bracket"></i>&nbsp; Keluar</a>
    </div>

    {{-- Content --}}
        <div class="col-lg-10 p-5">
    @yield('content')
        </div>
    </div>  
    {{-- End sidebar and content --}}

    
     <!-- Bottom Source -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
     <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
     <script src="https://kit.fontawesome.com/fbc67db110.js" crossorigin="anonymous"></script>
     <script src="{{ asset('assets/js/index.js') }}"></script>
  </body>
</html>