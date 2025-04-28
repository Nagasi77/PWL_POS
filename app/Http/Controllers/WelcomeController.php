<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\StokModel;
use App\Models\PenjualanDetailModel;
use App\Models\KategoriModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class WelcomeController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            Log::debug('Memulai fungsi index di WelcomeController'); 

            $breadcrumbs = (object) [
                'title' => 'Selamat Datang',
                'list'  => ['Home', 'Welcome'],
            ];
            $activeMenu = 'dashboard';

            // Total stok masuk dan terjual
            $totalStokMasuk = StokModel::sum('stok_jumlah');
            $totalStokTerjual = PenjualanDetailModel::sum('jumlah');

            // Hitung total stok siap (stok masuk - stok terjual)
            $totalStokSiap = $totalStokMasuk - $totalStokTerjual;

            // Logging untuk debugging
            Log::info('Total Stok Masuk: ' . $totalStokMasuk);
            Log::info('Total Stok Terjual: ' . $totalStokTerjual);
            Log::info('Total Stok Siap: ' . $totalStokSiap);

            // Data per kategori untuk grafik
            $stokMasuk = StokModel::select('m_barang.kategori_id', DB::raw('SUM(stok_jumlah) as total_masuk'))
                ->join('m_barang', 't_stok.barang_id', '=', 'm_barang.barang_id') // Ubah m_stok menjadi t_stok
                ->groupBy('m_barang.kategori_id');
            $stokTerjual = PenjualanDetailModel::select('m_barang.kategori_id', DB::raw('SUM(jumlah) as total_terjual'))
                ->join('m_barang', 't_penjualan_detail.barang_id', '=', 'm_barang.barang_id')
                ->groupBy('m_barang.kategori_id');

            $ringkasan = KategoriModel::from('m_kategori as kategori')
                ->select(
                    'kategori.kategori_nama',
                    DB::raw('COALESCE(masuk.total_masuk, 0) as total_masuk'),
                    DB::raw('COALESCE(terjual.total_terjual, 0) as total_terjual'),
                    DB::raw('COALESCE(masuk.total_masuk, 0) - COALESCE(terjual.total_terjual, 0) as stok_siap')
                )
                ->leftJoinSub($stokMasuk, 'masuk', function ($join) {
                    $join->on('kategori.kategori_id', '=', 'masuk.kategori_id');
                })
                ->leftJoinSub($stokTerjual, 'terjual', function ($join) {
                    $join->on('kategori.kategori_id', '=', 'terjual.kategori_id');
                })
                ->orderBy('kategori.kategori_nama')
                ->get();

            return view('welcome', compact('breadcrumbs', 'activeMenu', 'totalStokMasuk', 'totalStokTerjual', 'totalStokSiap', 'ringkasan'));
        } catch (Exception $e) {
            Log::error('Error loading dashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat dashboard.');
        }
    }
}