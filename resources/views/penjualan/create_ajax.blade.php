<form action="{{ url('/penjualan/store_ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Input Penjualan Kode -->
                <div class="form-group">
                    <label>Penjualan Kode</label>
                    <input type="text" name="penjualan_kode" id="penjualan_kode" class="form-control" placeholder="Masukkan Kode Penjualan" required>
                    <small id="error-penjualan_kode" class="error-text form-text text-danger"></small>
                </div>
                <!-- Input Pembeli -->
                <div class="form-group">
                    <label>Pembeli</label>
                    <input type="text" name="pembeli" id="pembeli" class="form-control" placeholder="Masukkan Nama Pembeli" required>
                    <small id="error-pembeli" class="error-text form-text text-danger"></small>
                </div>
                <!-- Input Tanggal Penjualan -->
                <div class="form-group">
                    <label>Tanggal Penjualan</label>
                    <input type="date" name="penjualan_tanggal" id="penjualan_tanggal" class="form-control" required>
                    <small id="error-penjualan_tanggal" class="error-text form-text text-danger"></small>
                </div>
                <!-- Detail Barang -->
                <div class="form-group">
                    <label>Detail Barang</label>
                    <div id="detail-barang">
                        <div class="row mb-2 detail-barang-item">
                            <div class="col-md-5">
                                <select name="detail[0][barang_id]" class="form-control barang-id" required>
                                    <option value="">- Pilih Barang -</option>
                                    @foreach($barangs as $barang)
                                        <option value="{{ $barang->barang_id }}" data-harga="{{ $barang->harga }}">{{ $barang->barang_nama }} (Rp {{ number_format($barang->harga, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                                <small class="error-barang_id error-text form-text text-danger"></small>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="detail[0][jumlah]" class="form-control jumlah" placeholder="Jumlah" required min="1">
                                <small class="error-jumlah error-text form-text text-danger"></small>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-sm btn-success add-barang"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function(){
        let itemCount = 1;

        // Tambah item barang baru
        $(document).on('click', '.add-barang', function() {
            const newItem = `
                <div class="row mb-2 detail-barang-item">
                    <div class="col-md-5">
                        <select name="detail[${itemCount}][barang_id]" class="form-control barang-id" required>
                            <option value="">- Pilih Barang -</option>
                            @foreach($barangs as $barang)
                                <option value="{{ $barang->barang_id }}" data-harga="{{ $barang->harga }}">{{ $barang->barang_nama }} (Rp {{ number_format($barang->harga, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                        <small class="error-barang_id error-text form-text text-danger"></small>
                    </div>
                    <div class="col-md-4">
                        <input type="number" name="detail[${itemCount}][jumlah]" class="form-control jumlah" placeholder="Jumlah" required min="1">
                        <small class="error-jumlah error-text form-text text-danger"></small>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-sm btn-danger remove-barang"><i class="fa fa-minus"></i></button>
                    </div>
                </div>`;
            $('#detail-barang').append(newItem);
            itemCount++;
        });

        // Hapus item barang
        $(document).on('click', '.remove-barang', function() {
            if ($('.detail-barang-item').length > 1) {
                $(this).closest('.detail-barang-item').remove();
            }
        });

        // Validasi form dengan jQuery Validate
        $("#form-tambah").validate({
            rules: {
                penjualan_kode: { required: true, maxlength: 50 },
                pembeli: { required: true, maxlength: 100 },
                penjualan_tanggal: { required: true, date: true },
                user_id: { required: true, number: true },
                'detail[0][barang_id]': { required: true, number: true },
                'detail[0][jumlah]': { required: true, number: true, min: 1 }
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if(response.status){
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            window.dataPenjualan.ajax.reload();
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function(prefix, val){
                                if (prefix.includes('detail')) {
                                    const [_, index, field] = prefix.match(/detail\[(\d+)\]\[(\w+)\]/);
                                    $(`.detail-barang-item:eq(${index}) .error-${field}`).text(val[0]);
                                } else {
                                    $('#error-' + prefix).text(val[0]);
                                }
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error){
                        console.error(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menyimpan data penjualan.'
                        });
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                if (element.attr('name').includes('detail')) {
                    const [_, index, field] = element.attr('name').match(/detail\[(\d+)\]\[(\w+)\]/);
                    $(`.detail-barang-item:eq(${index}) .error-${field}`).html(error);
                } else {
                    element.closest('.form-group').append(error);
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>