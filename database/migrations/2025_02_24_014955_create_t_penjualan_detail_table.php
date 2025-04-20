<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\StokModel;
use App\Models\PenjualanDetailModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class WelcomeController extends Controller
{
    /**
     * Display the dashboard with stock and sales summary.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            $breadcrumbs = (object) [
                'title' => 'Selamat Datang',
                'list'  => ['Home', 'Welcome'],
            ];
            $activeMenu = 'dashboard';

            // Total stok masuk dan terjual
            $totalStokMasuk = StokModel::sum('stok_jumlah');
            $totalStokTerjual = PenjualanDetailModel::sum('jumlah');

            // Logging untuk debugging
            Log::info('Total Stok Masuk: ' . $totalStokMasuk);
            Log::info('Total Stok Terjual: ' . $totalStokTerjual);

            // Data per barang untuk grafik
            $stokMasuk = StokModel::select('barang_id', DB::raw('SUM(stok_jumlah) as total_masuk'))
                ->groupBy('barang_id');
            $stokTerjual = PenjualanDetailModel::select('barang_id', DB::raw('SUM(jumlah) as total_terjual'))
                ->groupBy('barang_id');

            $ringkasan = BarangModel::from('m_barang as barang')
                ->select(
                    'barang.barang_nama',
                    DB::raw('COALESCE(masuk.total_masuk, 0) as total_masuk'),
                    DB::raw('COALESCE(terjual.total_terjual, 0) as total_terjual')
                )
                ->leftJoinSub($stokMasuk, 'masuk', function ($join) {
                    $join->on('barang.barang_id', '=', 'masuk.barang_id');
                })
                ->leftJoinSub($stokTerjual, 'terjual', function ($join) {
                    $join->on('barang.barang_id', '=', 'terjual.barang_id');
                })
                ->orderBy('barang.barang_nama')
                ->get();

            return view('welcome', compact('breadcrumbs', 'activeMenu', 'totalStokMasuk', 'totalStokTerjual', 'ringkasan'));
        } catch (Exception $e) {
            Log::error('Error loading dashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat dashboard.');
        }
    }
}