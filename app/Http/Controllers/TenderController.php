<?php

namespace App\Http\Controllers;

use App\Models\Type;
use App\Models\Tender;
use App\Models\Partner;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TenderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Tender::with('partner', 'category')->latest()->get(); // Tambahkan eager loading
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function ($data) {
                    if ($data->status == 0) {
                        return '<span class="badge bg-warning">Open</span>';
                    } elseif ($data->status == 1) {
                        return '<span class="badge bg-danger">Close</span>';
                    }
                    return '<span class="badge text-bg-dark">Unknown</span>';
                })
                ->editColumn('partner', function($data) {
                    return $data->partner->first()->name ?? 'Unknown';
                })
                ->editColumn('category', function($data) {
                    return $data->category->name ?? 'Unknown'; // Misalkan ada relasi ke tabel 'Category'
                })
                ->addColumn('action', function($data){
                    $route = 'tender';
                    return view('tender.action', compact('route', 'data'));
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('tender.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        // Check if the user is an admin
        if ($user->is_admin) {
            // Admins can manage all partners or bypass restrictions
            $partners = Partner::all(); // Admin can select any partner
        } else {
            // Regular user: retrieve only verified partners for the logged-in user
            $partners = $user->partners()->where('is_verified', true)->get();

            // If the user is not an admin and has no verified partners, redirect with an error
            if ($partners->isEmpty()) {
                return redirect()->route('tender.index')->with('error', 'You must have at least one verified partner to create a tender.');
            }
        }
        $categories = Category::all();

        return view('tender.create', compact('partners', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validasi input dari form
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'estimation' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'partner_id' => 'required|exists:partners,id',
            ]);

            // Ambil user yang sedang login
            $user = auth()->user();

            // Cari partner_user_id berdasarkan partner_id dan user_id dari tabel pivot partner_user
            $partnerUser = DB::table('partner_user')
                ->where('partner_id', $validatedData['partner_id'])
                ->where('user_id', $user->id)
                ->first();

            // Debugging tambahan jika partnerUser tidak ditemukan
            if (!$partnerUser) {
                // Kode ini akan dijalankan jika tidak ada data di pivot table
                return redirect()->back()->withErrors(['error' => 'You do not have permission to use this partner.']);
            }

            // Jika partnerUser ditemukan, lanjutkan
            $validatedData['partner_user_id'] = $partnerUser->id;

            // Hapus partner_id karena kita tidak lagi membutuhkannya
            unset($validatedData['partner_id']);

            // Buat tender baru menggunakan data yang tervalidasi
            Tender::create($validatedData);

            // Redirect ke route index dengan pesan sukses
            return redirect()->route('tender.index')->with('success', 'Tender created successfully.');
        } catch (\Exception $e) {
            // Jika ada error, tangkap dan kembalikan pesan error
            return redirect()->back()->withErrors(['error' => 'There was an error creating the tender: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($encryptTenderId)
    {
        $id = decrypt($encryptTenderId);
        dd($id);
        $tender = Tender::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($encryptTenderId)
    {
        $id = decrypt($encryptTenderId);
        dd($id);
        $tender = Tender::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $encryptTenderId)
    {
        $id = decrypt($encryptTenderId);
        dd($id);
        $tender = Tender::find($id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($encryptTenderId)
    {
        $id = decrypt($encryptTenderId);
        dd($id);
        $tender = Tender::find($id);
    }

    public function upload($encryptTenderId)
    {
        $id = decrypt($encryptTenderId);
        dd($id);
        $tender = Tender::find($id);

        $types = Type::where('category', 'Tender')->get();
    }
}
