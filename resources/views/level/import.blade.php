<!-- File: resources/views/level/import.blade.php -->

<form action="{{ url('/level/import_ajax') }}" method="POST" id="form-import" enctype="multipart/form-data">
    @csrf
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title mb-0">
                    <i class="fa fa-upload mr-2"></i> Import Data Level
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <!-- Card Info & Template Download -->
                <div class="card mb-3 border-info">
                    <div class="card-body p-2">
                        <p class="mb-2">
                            <i class="fa fa-info-circle text-info"></i>
                            Pastikan file Excel sesuai dengan template berikut:
                        </p>
                        <ul class="mb-2">
                            <li>Kolom A: <strong>level_kode</strong></li>
                            <li>Kolom B: <strong>level_nama</strong></li>
                        </ul>
                        <a href="{{ asset('assets/templates/template_level.xlsx') }}"
                            class="btn btn-sm btn-outline-info" download>
                            <i class="fa fa-file-excel"></i> Download Template
                        </a>
                    </div>
                </div>

                <!-- File Input -->
                <div class="form-group">
                    <label for="file_level" class="font-weight-bold">File Excel (.xlsx)</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="file_level" name="file_level" required>
                        <label class="custom-file-label" for="file_level">Pilih file...</label>
                    </div>
                    <small id="error-file_level" class="error-text form-text text-danger"></small>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-sm btn-secondary">
                    <i class="fa fa-times"></i> Batal
                </button>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fa fa-upload"></i> Upload
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    // Pastikan script ini dijalankan setelah modal selesai di-load
    $('#myModal').on('shown.bs.modal', function() {
        // Update label custom-file saat file dipilih
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // Validasi form menggunakan jQuery Validate
        $("#form-import").validate({
            rules: {
                file_level: {
                    required: true,
                    extension: "xlsx"
                }
            },
            submitHandler: function(form) {
                var formData = new FormData(form); 
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            dataLevel.ajax.reload();
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
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