@if(is_null($barang))
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    Data yang Anda cari tidak ditemukan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
@else
    <!-- Tambahkan logging untuk debugging -->
    @php
        \Illuminate\Support\Facades\Log::info('Menampilkan show_ajax.blade.php untuk barang ID: ' . $barang->barang_id);
        \Illuminate\Support\Facades\Log::info('Data kategori di show_ajax: ' . json_encode($barang->kategori));
    @endphp

    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Data Barang -->
                <h6>Data Barang</h6>
                <table class="table table-bordered table-striped table-hover table-sm" style="table-layout: fixed; width: 100%;">
                    <colgroup>
                        <col style="width: 30%;">
                        <col style="width: 70%;">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>ID Barang</th>
                        <td>{{ $barang->barang_id }}</td>
                    </tr>
                    <tr>
                        <th>Kode Barang</th>
                        <td>{{ $barang->barang_kode }}</td>
                    </tr>
                    <tr>
                        <th>Nama Barang</th>
                        <td>{{ $barang->barang_nama }}</td>
                    </tr>
                    <tr>
                        <th>Harga Beli</th>
                        <td>{{ $barang->harga_beli }}</td>
                    </tr>
                    <tr>
                        <th>Harga Jual</th>
                        <td>{{ $barang->harga_jual }}</td>
                    </tr>
                    </tbody>
                </table>

                <hr>

                <!-- Data Kategori -->
                <h6>Data Kategori</h6>
                <table class="table table-bordered table-striped table-hover table-sm" style="table-layout: fixed; width: 100%;">
                    <colgroup>
                        <col style="width: 30%;">
                        <col style="width: 70%;">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>ID Kategori</th>
                        <td>{{ $barang->kategori ? $barang->kategori->kategori_id : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Kode Kategori</th>
                        <td>{{ $barang->kategori ? $barang->kategori->kategori_kode : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Nama Kategori</th>
                        <td>{{ $barang->kategori ? $barang->kategori->kategori_nama : '-' }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
@endif