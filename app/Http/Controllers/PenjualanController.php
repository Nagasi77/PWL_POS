<?php

namespace App\Http\Controllers;

use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use App\Models\BarangModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;
use App\Helpers\Helper;

if (!function_exists('format_rupiah')) {
    function format_rupiah($value)
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumbs = (object) [
            'title' => 'Daftar Penjualan',
            'list' => ['Home', 'Penjualan'],
        ];

        $page = (object) [
            'title' => 'Daftar penjualan',
        ];

        $activeMenu = 'penjualan';

        return view('penjualan.index', compact('breadcrumbs', 'page', 'activeMenu'));
    }

    public function list(Request $request)
    {
        $penjualan = PenjualanModel::select('penjualan_id', 'penjualan_kode', 'total_harga', 'pembeli', 'penjualan_tanggal', 'user_id')->with('user');

        return DataTables::of($penjualan)
            ->addIndexColumn()
            ->addColumn('user_name', function ($p) {
                return $p->user ? $p->user->nama : '-';
            })
            ->addColumn('total_harga', function ($p) {
                return format_rupiah($p->total_harga);
            })
            ->addColumn('aksi', function ($p) {
                $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $p->penjualan_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function show_ajax($id)
    {
        $penjualan = PenjualanModel::with(['user', 'detail.barang'])->find($id);

        // Hitung total stok masuk, terjual, dan siap
        $totalStokMasuk = $penjualan->detail->sum('barang.stok_masuk');
        $totalStokTerjual = $penjualan->detail->sum('jumlah');
        $totalStokSiap = $totalStokMasuk - $totalStokTerjual;

        // Contoh data ringkasan (sesuaikan dengan kebutuhan Anda)
        $ringkasan = collect([
            (object) ['barang_nama' => 'Barang A', 'total_masuk' => 100, 'total_terjual' => 50, 'stok_siap' => 50],
            (object) ['barang_nama' => 'Barang B', 'total_masuk' => 200, 'total_terjual' => 150, 'stok_siap' => 50],
        ]);

        return view('penjualan.show_ajax', compact('penjualan', 'ringkasan', 'totalStokMasuk', 'totalStokTerjual', 'totalStokSiap'));
    }

    public function export_pdf()
    {
        $penjualan = PenjualanModel::with(['user', 'detail.barang'])->orderBy('penjualan_tanggal', 'desc')->get();

        $pdf = Pdf::loadView('penjualan.export_pdf', ['penjualan' => $penjualan]);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Data_Penjualan_' . date('Ymd_His') . '.pdf');
    }

    public function create_ajax()
    {
        $users = UserModel::all();
        $barangs = BarangModel::all();
        return view('penjualan.create_ajax', compact('users', 'barangs'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $validator = Validator::make($request->all(), [
                'penjualan_kode' => 'required|string|max:50|unique:t_penjualan,penjualan_kode',
                'pembeli' => 'required|string|max:100',
                'penjualan_tanggal' => 'required|date',
                'detail.*.barang_id' => 'required|exists:m_barang,barang_id',
                'detail.*.jumlah' => 'required|numeric|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            try {
                DB::beginTransaction();

                // Simpan data penjualan
                $penjualan = new PenjualanModel();
                $penjualan->penjualan_kode = $request->penjualan_kode;
                $penjualan->pembeli = $request->pembeli;
                $penjualan->penjualan_tanggal = $request->penjualan_tanggal;
                $penjualan->user_id = $request->user()->user_id; // Ambil user_id dari auth
                $penjualan->total_harga = 0; // Akan dihitung setelah detail disimpan
                $penjualan->save();

                // Simpan detail penjualan dan hitung total harga
                $totalHarga = 0;
                foreach ($request->detail as $item) {
                    $barang = BarangModel::find($item['barang_id']);
                    $subtotal = $barang->harga_jual * $item['jumlah'];
                    $totalHarga += $subtotal;

                    $detail = new PenjualanDetailModel();
                    $detail->penjualan_id = $penjualan->penjualan_id;
                    $detail->barang_id = $item['barang_id'];
                    $detail->jumlah = $item['jumlah'];
                    $detail->harga = $barang->harga_jual;
                    $detail->save();
                }

                // Update total harga pada penjualan
                $penjualan->total_harga = $totalHarga;
                $penjualan->save();

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil disimpan.'
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal menyimpan data: ' . $e->getMessage()
                ]);
            }
        }

        return redirect('/');
    }

    public function export_excel()
    {
        // Ambil data penjualan beserta relasi (pastikan relasi sudah didefinisikan di model PenjualanModel)
        $penjualan = PenjualanModel::with(['user', 'detail.barang'])
            ->orderBy('penjualan_tanggal', 'desc')
            ->get();

        // Buat objek Spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Penjualan');
        $sheet->setCellValue('C1', 'Pembeli');
        $sheet->setCellValue('D1', 'Tanggal');
        $sheet->setCellValue('E1', 'User');
        $sheet->setCellValue('F1', 'Total Harga');

        // Buat header bold
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        // Isi data penjualan
        $no = 1;
        $row = 2;
        foreach ($penjualan as $p) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $p->penjualan_kode);
            $sheet->setCellValue('C' . $row, $p->pembeli);
            $sheet->setCellValue('D' . $row, \Carbon\Carbon::parse($p->penjualan_tanggal)->format('Y-m-d'));
            $sheet->setCellValue('E' . $row, $p->user->nama ?? '-');
            $sheet->setCellValue('F' . $row, $p->total_harga);
            $no++;
            $row++;
        }

        // Set auto-size untuk kolom A sampai F
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set judul sheet
        $sheet->setTitle('Data Penjualan');

        // Buat writer untuk file Excel
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Penjualan_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Atur header HTTP untuk file download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        // Tampilkan file Excel untuk diunduh
        $writer->save('php://output');
        exit;
    }
}