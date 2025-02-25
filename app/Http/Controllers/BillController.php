<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Patient;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;

class BillController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan daftar tagihan dengan DataTables.
     */
    public function datatable(Request $request)
    {
        if ($request->ajax()) {
            // Ambil data bills dengan relasi patient
            $bills = Bill::with('patient')
                ->select(['id', 'patient_id', 'transaction', 'bill', 'created_at'])
                ->orderBy('created_at', 'desc'); // Optional: Urutkan berdasarkan created_at

            return DataTables::of($bills)
                ->addIndexColumn() // Kolom index (nomor urut)
                ->addColumn('name', function ($row) {
                    // Ambil nama pasien dari relasi patient
                    return $row->patient ? $row->patient->name : 'Unknown';
                })
                ->editColumn('created_at', function ($row) {
                    // Format tanggal created_at
                    return $row->created_at ? $row->created_at->format('d-m-Y H:i') : '-';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <a href="' . route('bills.download-pdf', $row->id) . '" class="btn btn-success btn-sm">
                                <i class="fa-solid fa-file-pdf"></i> PDF
                            </a>
                            <form action="' . route('bills.destroy', $row->id) . '" method="POST" onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus ini?\')">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fa-solid fa-trash-can"></i> Hapus
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['action']) // Render HTML di kolom action
                ->make(true);
        }

        // Jika bukan request AJAX, kembalikan response kosong atau error
        return response()->json(['error' => 'Invalid request'], 400);
    }


    /**
     * Menampilkan halaman daftar tagihan.
     */
    public function index()
    {
        $query = Bill::query();
        $totalBills = $query->count();
        return view('billdata', compact('totalBills'));
    }

    /**
     * Menampilkan form tambah tagihan.
     */
    public function create()
    {
        $patients = Patient::select(['id', 'name'])->get();
        return view('bill', compact('patients'));
    }

    /**
     * Menyimpan tagihan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id'  => 'required|exists:patients,id',
            'transaction' => 'required|in:tunai,non-tunai,bpjs',
            'bill'        => 'required|in:lunas,belum-lunas',
        ]);

        Bill::create($request->all());

        return redirect()->route('bills.index')->with('success', 'Tagihan berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit tagihan.
     */
    public function edit(Bill $bill)
    {
        $patients = Patient::select(['id', 'name'])->get();
        return view('editbill', compact('bill', 'patients'));
    }

    /**
     * Mengupdate data tagihan di database.
     */
    public function update(Request $request, Bill $bill)
    {
        $request->validate([
            'patient_id'  => 'required|exists:patients,id',
            'transaction' => 'required|in:tunai,non-tunai,bpjs',
            'bill'        => 'required|in:lunas,belum-lunas',
        ]);

        $bill->update($request->all());

        return redirect()->route('bills.index')->with('success', 'Tagihan berhasil diperbarui.');
    }

    /**
     * Menghapus tagihan dari database.
     */
    public function destroy(Bill $bill)
    {
        try {
            $bill->delete(); // Soft delete
            return redirect()->route('bills.history')->with('success', 'Tagihan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('bills.history')->with('error', 'Gagal menghapus tagihan!');
        }
    }

    public function restore($id)
    {
        try {
            $bill = Bill::withTrashed()->findOrFail($id); // Cari data yang di-soft delete
            $bill->restore(); // Restore data
            return redirect()->route('bills.index')->with('success', 'Tagihan berhasil dipulihkan!');
        } catch (\Exception $e) {
            return redirect()->route('bills.history')->with('error', 'Gagal memulihkan tagihan!');
        }
    }

    public function forceDelete($id)
    {
        try {
            $bill = Bill::withTrashed()->findOrFail($id); // Cari data yang di-soft delete
            $bill->forceDelete(); // Hapus permanen
            return redirect()->route('bills.history')->with('success', 'Tagihan berhasil dihapus permanen!');
        } catch (\Exception $e) {
            return redirect()->route('bills.history')->with('error', 'Gagal menghapus tagihan permanen!');
        }
    }

    public function history()
    {
        // Ambil data yang di-soft delete
        $deletedBills = Bill::onlyTrashed()->with('patient')->get();
        $totalHistory = Bill::onlyTrashed()->count();
        return view('history2', compact('deletedBills', 'totalHistory'));
    }

    public function downloadPDF()
    {
        // Ambil data dari database
        $bills = Bill::with('patient')->get(); // Pastikan relasi patient di-load

        // Load view ke PDF
        $pdf = Pdf::loadView('printbill', compact('bills'));

        // Download PDF
        return $pdf->download('data-tagihan.pdf');
    }
}
