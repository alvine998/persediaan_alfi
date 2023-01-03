<div class="row justify-contents-between">
    <div class="col-lg-5">
        <form action="{{ route('sync.product.import') }}" enctype="multipart/form-data" method="POST" class="">
            @csrf
        
            <div class="mb-3">
                <label for="formFile" class="form-label fw-bold mb-3"><i class="fas fa-file-excel text-danger"></i> Pilih File Import Excel</label>
                <input class="form-control" type="file" name="my-file" id="formFile">
            </div>

            @error('my-file')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
            @enderror
        
            <div class="d-flex">
                <button onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();" class="w-50 me-3 mt-3 btn btn btn-danger"><i class="fas fa-boxes"></i> Input Produk Massal</button>
                @php
                // Validasi Data
                $getData = collect(DB::select("SELECT * FROM history INNER JOIN product_history ON history.history_id = product_history.history_id WHERE history.type=1 AND product_history.status != 'Gagal' ORDER BY product_history.history_id DESC LIMIT 1"))->first();
            @endphp
            @if(!empty($getData))
            <a href="{{ route('sync.product.export-barcode') }}" class="mt-3 btn btn btn-danger w-50"><i class="fas fa-qrcode"></i> Cetak Barcode</a>
            @endif
            </div>
            
            
            
        </form>
    </div>

    <div class="col-lg-7 border-start">
        <label for="formFile" class="form-label fw-bold mb-3"><i class="fas fa-triangle-exclamation text-danger"></i> Status Terakhir</label>

        @if(!empty($error))
        <div class="shadow p-3 rounded mb-4">
            <h5 class="text-{{ $error->status == 0 ? 'danger' : 'danger' }}">{{ $error->status == 0 ? 'Input Produk Massal: Gagal' : 'Input Produk Massal: Sukses' }}</h5>
            @if($error->status == 1 && $error->error_count > 0)
            <h6>Dengan {{ $error->error_count }} Error</h6>
            @endif
            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <small class="fw-bold"><i class="fas fa-calendar text-danger"></i> Pada: {{ $error->created_at }}</small>

                @if($error->status == 0)
                <small class="fw-bold d-block"><i class="fas fa-triangle-exclamation text-danger"></i> Total Error: {{ $error->error_count }}</small>
                @endif
            </div>
        </div>
        @else 
        <div class="shadow p-3 rounded mb-4">
            <h5 class="text-danger"><i class="fas fa-circle-exclamation"></i> Anda Belum Pernah Input Produk</h4>
            <hr>
        </div>
        @endif
        
        @if(Session::has('message'))
            <div class="shadow px-4 py-3 text-danger my-4 fw-bold" role="alert">
                <i class="fas fa-signal"></i>&nbsp; {{ Session::get('message') }}
            </div>
        @endif



        @if(session()->has('my-status-input'))
        <label for="formFile my-4" class="form-label fw-bold mb-3"><i class="fas fa-triangle-exclamation text-danger"></i> Pesan Kesalahan Penyesuaian</label>
        
        <div style="height: 200px; overflow: auto;">
            <table class="table">
                <tr class="text-light bg-danger">
                    <th>Baris</th>
                    <th>Jenis Error</th>
                </tr>
                
        
                    <tbody >
                        @foreach(session()->get('my-status-input')['my-status-input'] as $validations)
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

