@extends('layouts.auth')

<style>
    body {
        background: #212529; /* Warna gelap solid */
        color: #f8f9fa; /* Warna teks lebih terang */
    }

    .register-box {
        background: #343a40; /* Warna gelap lebih solid */
        color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
    }

    .card-header a {
        color: #ffffff;
        font-weight: bold;
    }

    .login-box-msg {
        color: #000000;
    }

    .form-control {
        background-color: #495057;
        border: 1px solid #6c757d;
        color: #ffffff;
    }

    .form-control::placeholder {
        color: #e0e0e0;
    }

    .form-control:focus {
        border-color: #f8f9fa;
        box-shadow: 0 0 8px rgba(248, 249, 250, 0.8);
    }

    .btn-primary {
        background-color: #007bff; /* Biru solid */
        border-color: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
        transform: scale(1.05);
        transition: 0.3s ease-in-out;
    }

    .text-link {
        color: #f8f9fa; /* Link lebih jelas */
    }

    .text-link:hover {
        color: #ffffff;
        text-decoration: underline;
    }

    .input-group-text {
        background-color: #495057;
        color: #ffffff;
    }
</style>

@section('content')
<div class="register-box">
    <div class="card card-dark">
        <div class="card-header text-center">
            <a href="{{ url('/') }}" class="h1">PWL POS</a>
        </div>

        <div class="card-body">
            <p class="login-box-msg">Daftar akun baru</p>
            <form action="{{ route('register.post') }}" method="POST" id="form-register">
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
                    <input type="text" id="nama" name="nama" class="form-control" placeholder="Nama Lengkap">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-id-card"></span>
                        </div>
                    </div>
                    <small id="error-nama" class="error-text text-danger"></small>
                </div>

                <div class="input-group mb-3">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <a href="#" class="toggle-password" data-target="#password">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    <small id="error-password" class="error-text text-danger"></small>
                </div>

                <div class="input-group mb-3">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Konfirmasi Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <a href="#" class="toggle-password" data-target="#password_confirmation">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    <small id="error-password_confirmation" class="error-text text-danger"></small>
                </div>

                <div class="row">
                    <div class="col-5">
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold">
                            <i class="fas fa-user-plus"></i>&nbsp;&nbsp;Daftar
                        </button>
                    </div>
                    <div class="col-7 d-flex align-items-center justify-content-end">
                        <p class="mb-0">
                            Sudah punya akun? <a href="{{ url('login') }}" class="text-link">Masuk di sini</a>
                        </p>
                    </div>
                </div>
            </form>
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

        // Validasi form registrasi dengan jQuery Validate
        $("#form-register").validate({
            rules: {
                username: {
                    required: true,
                    minlength: 4,
                    maxlength: 20
                },
                nama: {
                    required: true
                },
                password: {
                    required: true,
                    minlength: 5,
                    maxlength: 20
                },
                password_confirmation: {
                    required: true,
                    equalTo: "#password"
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
                                title: 'Registrasi Berhasil',
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
                                title: 'Gagal Registrasi',
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
