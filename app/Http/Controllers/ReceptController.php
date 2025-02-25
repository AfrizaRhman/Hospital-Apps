<?php

namespace App\Http\Controllers;

use App\Models\Recept;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;

class ReceptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function datatable()
    {
        $query = Recept::select(['id', 'dokter', 'obat', 'bentuk', 'jumlah', 'pemakaian', 'created_at']);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('bentuk', function ($row) {
                $bentuk = json_decode($row->bentuk, true) ?? [];
                return is_array($bentuk) ? implode(', ', $bentuk) : $bentuk; // Pastikan data adalah array
            })
            ->editColumn('obat', function ($row) {
                $obat = json_decode($row->obat, true) ?? [];
                return is_array($obat) ? implode(', ', $obat) : $obat; // Pastikan data adalah array
            })
            ->editColumn('jumlah', function ($row) {
                $jumlah = json_decode($row->jumlah, true) ?? [];
                return is_array($jumlah) ? implode(', ', $jumlah) : $jumlah; // Pastikan data adalah array
            })
            ->editColumn('pemakaian', function ($row) {
                $pemakaian = json_decode($row->pemakaian, true) ?? [];
                return is_array($pemakaian) ? implode(', ', $pemakaian) : $pemakaian; // Pastikan data adalah array
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d-m-Y H:i');
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <a href="' . route('recepts.download-pdf', $row->id) . '" class="btn btn-success btn-sm">
                            <i class="fa-solid fa-file-pdf"></i> PDF
                        </a>
                        <form action="' . route('recepts.destroy', $row->id) . '" method="POST" onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus ini?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-trash-can"></i> Hapus
                            </button>
                        </form>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function index()
    {
        $query = Recept::query();
        $totalRecepts = $query->count();
        return view('receptdata', compact('totalRecepts'));
    }

    public function create()
    {
        return view('recept');
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'dokter' => 'required|string|max:255',
            'obat' => 'required|array',
            'obat.*' => 'string|max:255', // Validasi setiap elemen array
            'bentuk' => 'required|array',
            'bentuk.*' => 'in:tablet,sirup,kapsul', // Validasi setiap elemen array
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1', // Validasi setiap elemen array
            'pemakaian' => 'required|array',
            'pemakaian.*' => 'string|max:255', // Validasi setiap elemen array
        ]);

        // Simpan data obat sebagai JSON
        $data = [
            'dokter' => $request->dokter,
            'obat' => json_encode($request->obat),
            'bentuk' => json_encode($request->bentuk),
            'jumlah' => json_encode($request->jumlah),
            'pemakaian' => json_encode($request->pemakaian),
        ];

        // Simpan data ke database
        Recept::create($data);

        return redirect()->route('recepts.index')->with('success', 'Resep berhasil ditambahkan.');
    }

    public function destroy(Recept $recept)
    {
        try {
            $recept->delete(); // Soft delete
            return redirect()->route('recepts.history')->with('success', 'Resep berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('recepts.history')->with('error', 'Gagal menghapus resep!');
        }
    }

    public function restore($id)
    {
        try {
            $recept = Recept::withTrashed()->findOrFail($id); // Cari data yang di-soft delete
            $recept->restore(); // Restore data
            return redirect()->route('recepts.index')->with('success', 'Resep berhasil dipulihkan!');
        } catch (\Exception $e) {
            return redirect()->route('recepts.history')->with('error', 'Gagal memulihkan resep!');
        }
    }

    public function forceDelete($id)
    {
        try {
            $recept = Recept::withTrashed()->findOrFail($id); // Cari data yang di-soft delete
            $recept->forceDelete(); // Hapus permanen
            return redirect()->route('recepts.history')->with('success', 'Resep berhasil dihapus permanen!');
        } catch (\Exception $e) {
            return redirect()->route('recepts.history')->with('error', 'Gagal menghapus resep permanen!');
        }
    }

    public function history()
    {
        // Ambil data yang di-soft delete
        $deletedRecepts = Recept::onlyTrashed()->get();
        $totalHistory = $deletedRecepts->count(); // Hitung total history
        return view('history4', compact('deletedRecepts', 'totalHistory'));
    }

    public function downloadPDF($id)
    {
        // Ambil data dari database berdasarkan ID
        $recept = Recept::findOrFail($id);

        // Decode data JSON
        $recept->obat = json_decode($recept->obat, true) ?? [];
        $recept->bentuk = json_decode($recept->bentuk, true) ?? [];
        $recept->jumlah = json_decode($recept->jumlah, true) ?? [];
        $recept->pemakaian = json_decode($recept->pemakaian, true) ?? [];

        // Load view ke PDF
        $pdf = Pdf::loadView('printrecept', compact('recept'));

        // Download PDF
        return $pdf->download('resep-' . $recept->id . '.pdf');
    }
}
