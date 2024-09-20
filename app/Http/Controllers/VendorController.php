<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $vendor = Vendor::orderByDesc('created_at')
                ->get();
            return DataTables::of($vendor)
            ->addColumn('action', function($data){
                $route = 'vendor';
                return view ('vendor.action', compact ('route', 'data'));
            })
            ->addindexcolumn()
            ->make(true);
        }

        $vendor = Vendor::all();
        return view ('vendor.index', compact('vendor'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('vendor.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|unique:vendors,name',
            'npwp' => 'required|string',
            'description' => 'required',
            'brand' => 'required|array',
            'email' => 'required|email|array',
            'contact' => 'required|array',
            'category' => 'required|array',
            'pic' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            // Insert into vendors table
            $vendor = Vendor::create([
                'name' => $request->input('name'),
                'npwp' => $request->input('npwp'),
                'description' => $request->input('description'),
                'brand' => json_encode($request->input('brand')), // Storing brands as a JSON array
                'email' => json_encode($request->input('email')), // Storing email as a JSON array
                'contact' => json_encode($request->input('contact')), // Storing contact as a JSON array
                'pic' => json_encode($request->input('pic')), // Storing PIC as a JSON array
            ]);

            // Insert into user_vendor pivot table
            $vendor->users()->attach(auth()->id());

            // Insert into category_vendor pivot table
            if ($request->has('category')) {
                $vendor->categories()->attach($request->input('category'));
            }

            // Commit the transaction if everything is successful
            DB::commit();

            // Redirect to vendor index with success message
            return redirect()->route('vendor.index')->with('success', 'Vendor created successfully.');

        } catch (\Exception $e) {
            // Rollback the transaction in case of any error
            DB::rollBack();

            // Log the error (optional)
            \Log::error('Error creating vendor: ' . $e->getMessage());

            // Redirect back with error message
            return redirect()->back()->withErrors(['error' => 'Failed to create vendor. Please try again.']);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Vendor $vendor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        //
    }
}
