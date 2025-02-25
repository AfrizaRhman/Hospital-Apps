<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class PatientController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    public function datatable(Request $request)
    {
        $patients = Patient::select(['id', 'name', 'age', 'gender', 'transaction', 'created_at']);

        return DataTables::of($patients)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('d-m-Y H:i:s') : '-';
            })
            ->addColumn('action', function ($patient) {
                $user = Auth::user();
                $isAdminOrOffice = $user && in_array($user->role, ['admin', 'office']);

                if ($isAdminOrOffice) {
                    return '<div class="d-flex justify-content-center align-items-center gap-2">
                                <a href="' . route('patients.edit', $patient->id) . '" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-file-pen"></i> Edit
                                </a>
                                <form action="' . route('patients.destroy', $patient->id) . '" method="POST" class="d-inline delete-form">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-trash-can"></i> Hapus
                                    </button>
                                </form>
                            </div>';
                }
                return '';
            })
            ->rawColumns(['action', 'created_at'])
            ->make(true);
    }

    public function index()
    {
        $query = Patient::query();
        $totalPatients = $query->count();
        return view('index', compact('totalPatients'));
    }

    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'transaction' => 'required|in:tunai,non-tunai,bpjs',
        ]);

        Patient::create([
            'name' => $validate['name'],
            'age' => $validate['age'],
            'gender' => $validate['gender'],
            'transaction' => $validate['transaction'] ?? '',
        ]);

        return redirect()->route('index')->with('success', 'Pasien berhasil ditambahkan.');
    }

    public function show(Patient $patient)
    {
        return view('patients.show', compact('patient'));
    }

    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        return view('edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'name' => 'required|min:3',
            'age' => 'required|integer',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'transaction' => 'required|in:tunai,non-tunai,bpjs',
        ]);

        $oldData = $patient->getOriginal();
        $patient->update($validated);

        $changes = [];
        foreach ($oldData as $key => $value) {
            if ($value != $patient->$key) {
                $changes[$key] = [
                    'old' => $value,
                    'new' => $patient->$key
                ];
            }
        }

        if (!empty($changes)) {
            Log::info("Data pasien dengan ID {$patient->id} telah diperbarui oleh user: " . auth()->user()->name, $changes);
        }

        return redirect()->route('index')->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(Patient $patient)
    {
        $patient = Patient::findOrFail($patient);
        $patient->delete();
        return redirect()->route('patients.history')->with('delete', 'Success delete data!');
    }

    public function restore($id)
    {
        $patient = Patient::withTrashed()->find($id);
        if ($patient && $patient->trashed()) {
            $patient->restore();
            return redirect()->route('index')->with('success', 'Patient restored!');
        }
        return redirect()->route('patients.history')->with('error', 'Patient not found or not deleted!');
    }

    public function forceDelete($id)
    {
        $patient = Patient::withTrashed()->find($id);
        if (!$patient) {
            return redirect()->route('patients.history')->with('error', 'Patient not found!');
        }

        $patient->forceDelete();
        return redirect()->route('patients.history')->with('success', 'Patient deleted permanently!');
    }

    // Controller Method
    public function history()
    {
        // Ambil data pasien yang sudah dihapus (soft delete)
        $patients = Patient::onlyTrashed()->select('id', 'name', 'age', 'gender', 'transaction', 'created_at')->get();
        $totalHistory = Patient::onlyTrashed()->count();

        return view('history', compact('patients', 'totalHistory'));
    }
}
