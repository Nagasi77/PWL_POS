@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
           <button onclick="modalAction('{{ url('kategori/import') }}')" class="btn btn-sm btn-info mt-1" style="border:0cm" >
                <i class=""></i>Import EXC
            </button>
            <a href="{{ url('/kategori/export_excel') }}" class="btn btn-sm btn-primary mt-1" style="background-color: rgb(0, 90, 30); border:0cm">
                <i class=""></i>Export EXC
            </a>
            <a href="{{ url('/kategori/export_pdf') }}" class="btn btn-sm btn-warning mt-1" style="background-color: rgb(212, 0, 0); border:0cm; color:rgb(255, 255, 255) ">
                <i class=""></i> Export PDF
            </a>
            <button onclick="modalAction('{{ url('kategori/create_ajax') }}')" class="btn btn-sm btn-success mt-1" style="background-color: rgb(12, 206, 22); border:0cm; color:rgb(255, 255, 255) ">
                Tambah Barang
            </button>
        </div>
    </div>
    <div class="card-body">
        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <!-- Filter -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label">Filter</label>
                    <div class="col-3">
                        <select class="form-control" id="kategori_id" name="kategori_id">
                            <option value="">Semua</option>
                            @foreach($kategories as $item)
                            <option value="{{ $item->kategori_id }}">{{ $item->kategori_nama }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Kategori ID</small>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-bordered table-striped table-hover table-sm" id="table_kategori">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode Kategori</th>
                    <th>Nama Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal Global untuk AJAX (gunakan id "myModal") -->
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false"></div>
@endsection

@push('css')
@endpush

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function() {
            $('#myModal').modal('show');
        });
    }

    var dataKategori;
    $(document).ready(function() {
        dataKategori = $('#table_kategori').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: "{{ route('kategori.list') }}",
                type: "POST",
                data: function(d) {
                    d.kategori_id = $('#kategori_id').val();
                }
            },
            columns: [{
                    data: "DT_RowIndex",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "kategori_kode",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "kategori_nama",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "aksi",
                    orderable: false,
                    searchable: false,
                    className: "text-center"
                }
            ]
        });
        $('#kategori_id').on('change', function() {
            dataKategori.ajax.reload();
        });
    });
</script>
@endpush