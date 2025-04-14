<style>
    .import-box {
        max-width: 500px;
        margin: 3rem auto;
        padding: 2rem;
        background: #f9fafb;
        border-radius: 12px;
        position: relative;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        font-family: sans-serif;
    }

    .close-btn {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 1.5rem;
        color: #9ca3af;
        text-decoration: none;
        font-weight: bold;
    }

    .close-btn:hover {
        color: #ef4444;
    }
</style>

<div class="import-box">
    <!-- Tombol X untuk kembali ke halaman sebelumnya -->
    <a href="{{ url()->previous() }}" class="close-btn">&times;</a>

    <h2><i class="fas fa-upload"></i> Import Data Barang</h2>

    <form action="{{ url('/barang/import_ajax') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <p>Silakan upload file Excel sesuai template:</p>

        <a href="{{ asset('assets/templates/template_barang.xlsx') }}" download>
            Download Template Excel
        </a>

        <div style="margin-top: 1rem;">
            <label for="file_barang">File Excel (.xlsx):</label><br>
            <input type="file" id="file_barang" name="file_barang" accept=".xlsx" required>
            <small id="error-file_barang" class="error-text text-danger"></small>
        </div>

        <button type="submit" style="margin-top: 1rem;"> Upload</button>
    </form>
</div>
