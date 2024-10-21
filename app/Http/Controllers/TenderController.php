<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use App\Models\Partner;
use App\Models\Category;
use Illuminate\Http\Request;
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
            $data = Tender::latest()->get();
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
                    return $data->partner->name ?? 'Unknown'; // Misalkan ada relasi ke tabel 'Company'
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
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'estimation' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'partner_id' => 'required|exists:partners,id',
        ]);

        Tender::create($validatedData);

        return redirect()->route('tender.index')->with('success', 'Tender created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tender $tender)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tender $tender)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tender $tender)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tender $tender)
    {
        //
    }
}
