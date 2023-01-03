@extends('page.auth.index')
    
@section('content')
    <div style="width: 40%;" class="m-auto my-5 p-3 shadow-lg rounded login">
        <img class="login-image" src="{{ asset('assets/img/stock.png') }}" alt="">
        <form action="{{ route('auth.login') }}" method="POST">
            @csrf
            <h2 class="text-center text-danger mt-3">SIMULASI INVENTORY</h2>
            
            <div class="w-75 m-auto">
                <hr>
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label fw-bold">Username</label>
                    <input type="text" autocomplete="off" name="username" value="{{ old('username') }}" class="form-control" id="exampleFormControlInput1" placeholder="Username" >
                    @error('username')
                    <small class="text-danger mt-2 d-block">Username Anda Belum Terdaftar</small>
                    @enderror
                </div>
        
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" id="exampleFormControlInput1" placeholder="Password">
                    @error('password')
                    <small class="text-danger mt-2 d-block">Apalagi Password</small>
                    @enderror
                </div>

                @if(Session::has('message'))
                <div class="shadow bg-light text-danger p-3 shadow rounded fw-bold text-center my-4" role="alert">
                    <i class="fas fa-triangle-exclamation"></i> &nbsp; {{ Session::get('message') }}
                </div>
                @endif
        
                <button class="w-50 d-block m-auto my-4 fw-bold btn bg-danger text-light rounded">Login</button>

                <hr class="w-75 m-auto">
                <small class="text-secondary text-center d-block my-3 fw-bold">Web App Created By Alfi &copy; {{ date('Y') }}</small>
            </div>
        </form>
    </div>
@endSection