@extends('layouts.auth')
<style>
    body {
        background: linear-gradient(to right, #039fff, #ebebeb); /* Warna hijau */
        color: white;
    }
    .login-box {
        background: rgba(255, 255, 255, 0.9);
        padding: 20px;
        border-radius: 10px;
    }
    </style>
    <style>
        .btn-success:hover {
            background-color: #1000f1;
            transform: scale(1.05);
            transition: 0.3s ease-in-out;
        }
        .form-control:focus {
            border-color: #06459e;
            box-shadow: 0 0 8px #032257;
        }
    </style>
    
    
@section('content')
<div class="login-box">
    <div class="card card-primary">
        <div class="card-header text-center">
            <a href="{{ url('/') }}" class="h1">PWL POS</a>
        </div>
        
        <div class="card-body">
            <p class="login-box-msg">Silahkan masuk untuk melanjutkan</p>
            <form action="{{ url('login') }}" method="POST" id="form-login">
                @csrf
                <div class="input-group mb-3">
                    <input type="text" id="username" name="username" class="form-control" placeholder="Username">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                    <small id="error-username" class="error-text text-danger"></small>
                </div>
                <div class="input-group mb-3">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                    <div class="input-group-append">
                        <!-- Tambahkan tombol toggle password -->
                        <div class="input-group-text">
                            <a href="#" class="toggle-password" data-target="#password">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    <small id="error-password" class="error-text text-danger"></small>
                    
                </div><div class="row">
                    <div class="col-5">
                        <button type="submit" class="btn btn-primary btn-block" style="font-weight: bold;">
                            <i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;Masuk
                        </button>
                    </div>
                    <div class="col-7 d-flex align-items-center justify-content-end">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label for="remember">Ingat Saya</label>
                        </div>
                    </div>
                </div>
                
            </form>
            <!-- Link ke halaman registrasi -->
            <p class="mb-0 mt-3">
                Belum punya akun?<a href="{{ url('register') }}" class="text-center"> Daftar di sini</a>
            </p>
        </div>
        
    </div>
</div>


@push('js')
<script>
    $(document).ready(function() {
        // Toggle password show/hide
        $('.toggle-password').on('click', function(e) {
            e.preventDefault();
            var targetInput = $($(this).data('target'));
            var icon = $(this).find('i');
            if (targetInput.attr('type') === 'password') {
                targetInput.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                targetInput.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
        

        // Validasi form login dengan jQuery Validate
        $("#form-login").validate({
            rules: {
                username: {
                    required: true,
                    minlength: 4,
                    maxlength: 20
                },
                password: {
                    required: true,
                    minlength: 4,
                    maxlength: 20
                }
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1000
                            }).then(function() {
                                window.location = response.redirect;
                            });
                        } else {
                            $('.error-text').text('');
                            if (response.msgField) {
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal menghubungi server'
                        });
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.input-group').append(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });
    });
    
</script>
@endpush
@endsection
