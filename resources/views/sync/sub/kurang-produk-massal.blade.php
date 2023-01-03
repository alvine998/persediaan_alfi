<div class="row justify-contents-between">
    <div class="col-lg-5">
        <form action="{{ route('sync.product.kurang-stok-massal') }}" enctype="multipart/form-data" method="POST" class="">
            @csrf
        
            <div class="mb-3">
                <label for="formFile" class="form-label fw-bold mb-3"><i class="fas fa-file-excel text-danger"></i> Pilih File Import Excel Untuk Stok Export</label>
                <input class="form-control" type="file" name="stock_export" id="formFile">
            </div>
            @error('stock_export')
                <small class="text-danger my-2 d-block">{{ $message }}</small>
            @enderror

            <div class="mb-3">
                <label for="formFile" class="form-label fw-bold mb-3"><i class="fas fa-file-excel text-danger"></i> Pilih File Import Excel Untuk Stok Gudang</label>
                <input class="form-control" type="file" name="stock_gudang" id="formFile">
            </div>

            @error('stock_gudang')
                <small class="text-danger my-2 d-block">{{ $message }}</small>
            @enderror
        
            <div class="d-flex">
                <button onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();" class="w-100 mt-3 btn btn btn-danger"><i class="fas fa-minus"></i> Kurangi Stok Massal</button>
            </div>
        </form>
    </div>

    <div class="col-lg-7 border-start">
        <label for="formFile" class="form-label fw-bold mb-3"><i class="fas fa-triangle-exclamation text-danger"></i> Status Terakhir</label>

        @if(!empty($error3))
        <div class="shadow p-3 rounded mb-4">
            <h5 class="text-{{ $error3->status == 0 ? 'danger' : 'danger' }}">{{ $error3->status == 0 ? 'Pengurangan Stok Produk Massal: Gagal' : 'Pengurangan Stok Produk Massal: Sukses' }}</h4>
            @if($error3->status == 1 && $error3->error_count > 0)
            <h6>Dengan {{ $error3->error_count }} Error</h6>
            @endif
            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <small class="fw-bold"><i class="fas fa-calendar text-danger"></i> Pada: {{ $error3->created_at }}</small>

                @if($error3->status == 0)
                <small class="fw-bold d-block"><i class="fas fa-triangle-exclamation text-danger"></i> Total Error: {{ $error3->error_count }}</small>
                @endif
            </div>
        </div>
        @else 
        <div class="shadow p-3 rounded mb-4">
            <h5 class="text-danger"><i class="fas fa-circle-exclamation"></i> Anda Belum Pernah Mengurangi Stok Produk</h4>
            <hr>
        </div>
        @endif
        
        @if(Session::has('message'))
            <div class="shadow px-4 py-3 text-primary my-4 fw-bold" role="alert">
                <i class="fas fa-signal"></i>&nbsp; {{ Session::get('message') }}
            </div>
        @endif

        @if(session()->has('my-status-kurang'))
        <label for="formFile my-4" class="form-label fw-bold mb-3"><i class="fas fa-triangle-exclamation text-danger"></i> Pesan Kesalahan Penyesuaian</label>
        
        <div style="height: 200px; overflow: auto;">
            <table class="table">
                <tr class="text-light bg-danger">
                    <th>Baris</th>
                    <th>Jenis Error</th>
                </tr>
                
        
                    <tbody >
                        @foreach(session()->get('my-status-kurang')['my-status-kurang'] as $validations)
                        <tr>
                            <td>{{ $validations['row'] }}</td>
                            <td>
                                {!! $validations['error'] !!}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
               
            </table>
        </div>
        @endif

        @if(session()->has('my-error'))
        <label for="formFile my-4" class="form-label fw-bold mb-3"><i class="fas fa-triangle-exclamation text-danger"></i> Pesan Kesalahan Penyesuaian</label>

        <table class="table">
            <tr class="text-light bg-danger">
              <th>Baris Ke-</th>
              <th>Jenis Error</th>
            </tr>

                @foreach(session()->get('my-error')['my-error'] as $validations)
                <tr>
                  <td>{{ $validations['row'] }}</td>
                  <td>
                    {{ $validations['error'] }}
                  </td>
                </tr>
                @endforeach
        </table>
        @endif

        @if(session()->has('failures'))
        <label for="formFile my-4" class="form-label fw-bold mb-3"><i class="fas fa-triangle-exclamation text-danger"></i> Pesan Kesalahan</label>

        <table class="table">
            <tr class="text-light bg-danger">
              <th>Baris Ke-</th>
              <th>Jenis Error</th>
            </tr>

                @foreach(session()->get('failures') as $validations)
                <tr>
                  <td>{{ $validations->row() - 1 }}</td>
                  <td>
                    @foreach($validations->errors() as $errors)
                    {{ $errors }}
                    @endforeach
                  </td>
                </tr>
                @endforeach
        </table>
        @endif
    </div>
</div>



