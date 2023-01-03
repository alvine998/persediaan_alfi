<div class="row justify-contents-between">
    <div class="col-lg-5">
        <form action="{{ route('sync.product.tambah-stok-massal') }}" enctype="multipart/form-data" method="POST" class="">
            @csrf
        
            <div class="mb-3">
                <label for="formFile" class="form-label fw-bold mb-3"><i class="fas fa-file-excel text-danger"></i> Pilih File Import Excel Untuk Tambah Stok</label>
                <input class="form-control" type="file" name="my-files" id="formFile">
            </div>

            @error('my-files')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
            @enderror
        
            <div class="d-flex">
                <button onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();" class="w-100 mt-3 btn btn btn-danger"><i class="fas fa-plus"></i> Tambah Stok Massal</button>
            </div>
        </form>
    </div>

    <div class="col-lg-7 border-start">
        <label for="formFile" class="form-label fw-bold mb-3"><i class="fas fa-triangle-exclamation text-danger"></i> Status Terakhir</label>

        @if(!empty($error2))
        <div class="shadow p-3 rounded mb-4">
            <h5 class="text-{{ $error2->status == 0 ? 'danger' : 'danger' }}">{{ $error2->status == 0 ? 'Tambah Stok Produk Massal: Gagal' : 'Tambah Stok Produk Massal: Sukses' }}</h4>
            @if($error2->status == 1 && $error2->error_count > 0)
            <h6>Dengan {{ $error2->error_count }} Error</h6>
            @endif
            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <small class="fw-bold"><i class="fas fa-calendar text-danger"></i> Pada: {{ $error2->created_at }}</small>

                @if($error2->status == 0)
                <small class="fw-bold d-block"><i class="fas fa-triangle-exclamation text-danger"></i> Total Error: {{ $error2->error_count }}</small>
                @endif
            </div>
        </div>
        @else 
        <div class="shadow p-3 rounded mb-4">
            <h5 class="text-danger"><i class="fas fa-circle-exclamation"></i> Anda Belum Pernah Menambahkan Stok Produk</h4>
            <hr>
        </div>
        @endif
        
        @if(Session::has('message'))
            <div class="shadow px-4 py-3 text-primary my-4 fw-bold" role="alert">
                <i class="fas fa-signal"></i>&nbsp; {{ Session::get('message') }}
            </div>
        @endif

        @if(session()->has('my-status-tambah'))
        <label for="formFile my-4" class="form-label fw-bold mb-3"><i class="fas fa-triangle-exclamation text-danger"></i> Pesan Kesalahan Penyesuaian</label>
        
        <div style="height: 200px; overflow: auto;">
            <table class="table">
                <tr class="text-light bg-danger">
                    <th>Baris</th>
                    <th>Jenis Error</th>
                </tr>
                
        
                    <tbody >
                        @foreach(session()->get('my-status-tambah')['my-status-tambah'] as $validations)
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

