<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Patient;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;

class ComplaintController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function datatable()
    {
        $complaints = Complaint::with('patient')
            ->select(['id', 'patient_id', 'age', 'complaint', 'diagnosis', 'ruangan', 'created_at']);

        return DataTables::of($complaints)
            ->addIndexColumn()
            ->addColumn('name', function ($row) {
                return $row->patient ? $row->patient->name : 'Tidak Diketahui';
            })
            ->addColumn('age', function ($row) {
                return $row->patient ? $row->patient->age : '-';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d-m-Y H:i:s');
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <a href="' . route('complaints.download-pdf', $row->id) . '" class="btn btn-success btn-sm">
                            <i class="fa-solid fa-file-pdf"></i> PDF
                        </a>
                        <form action="' . route('complaints.destroy', $row->id) . '" method="POST" onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus ini?\')" class="d-inline">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-trash-can"></i> Hapus
                            </button>
                        </form>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function index()
    {
        $query = Complaint::query();
        $totalPatients = $query->count();
        $complaints = Complaint::with('patient')->get();
        return view('complaintdata', compact('totalPatients'));
    }

    public function create()
    {
        $patients = Patient::all();
        return view('complaint', compact('patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'age'        => 'required|integer|min:0',
            'complaint'  => 'required|string|max:500',
            'diagnosis'  => 'required|string|max:500',
            'ruangan'    => 'required|in:rawat inap,icu,bersalin,hcu,nicu,picu,isolasi,rehabilitas',
        ]);

        Complaint::create($validated);

        return redirect()->route('complaints.index')->with('success', 'Keluhan berhasil ditambahkan.');
    }

    public function edit(Complaint $complaint)
    {
        $patients = Patient::all();
        return view('complaints.edit', compact('complaint', 'patients'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'age'        => 'required|integer|min:0',
            'complaint'  => 'required|string|max:500',
            'diagnosis'  => 'required|string|max:500',
            'ruangan'    => 'required|in:rawat inap,icu,bersalin,hcu,nicu,picu,isolasi,rehabilitas',
        ]);

        $complaint->update($validated);

        return redirect()->route('complaints.index')->with('success', 'Keluhan berhasil diperbarui.');
    }

    public function destroy(Complaint $complaint)
    {
        try {
            $complaint->delete(); // Soft delete
            return redirect()->route('complaints.history')->with('success', 'Keluhan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('complaints.history')->with('error', 'Gagal menghapus keluhan!');
        }
    }

    public function restore($id)
    {
        try {
            $complaint = Complaint::withTrashed()->findOrFail($id); // Cari data yang di-soft delete
            $complaint->restore(); // Restore data
            return redirect()->route('complaints.index')->with('success', 'Keluhan berhasil dipulihkan!');
        } catch (\Exception $e) {
            return redirect()->route('complaints.history')->with('error', 'Gagal memulihkan keluhan!');
        }
    }

    public function forceDelete($id)
    {
        try {
            $complaint = Complaint::withTrashed()->findOrFail($id); // Cari data yang di-soft delete
            $complaint->forceDelete(); // Hapus permanen
            return redirect()->route('complaints.history')->with('success', 'Keluhan berhasil dihapus permanen!');
        } catch (\Exception $e) {
            return redirect()->route('complaints.history')->with('error', 'Gagal menghapus keluhan permanen!');
        }
    }

    public function history()
    {
        // Ambil data yang di-soft delete
        $deletedComplaints = Complaint::onlyTrashed()->with('patient')->get();
        $totalHistory = Complaint::onlyTrashed()->count();
        return view('history3', compact('deletedComplaints', 'totalHistory'));
    }

    public function downloadPDF($id)
    {
        // Ambil data dari database berdasarkan ID
        $complaint = Complaint::with('patient')->findOrFail($id);

        // Load view ke PDF
        $pdf = Pdf::loadView('printcomplaint', compact('complaint'));

        // Download PDF
        return $pdf->download('data-komplain-' . $complaint->id . '.pdf');
    }
}
