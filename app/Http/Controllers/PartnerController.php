<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\Facades\DataTables;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $partner = Partner::with('categories') // Eager load categories
            ->orderByDesc('created_at')
            ->get();
            return DataTables::of($partner)
            ->addColumn('categories', function($data) {
                // Display the related categories with numbering
                $categories = $data->categories->pluck('name');
                return $categories->map(function($category, $index) {
                    return ($index + 1) . '. ' . $category;
                })->implode('<br>'); // Adding number with line break
            })
            ->addColumn('brand', function($data) {
                // Decode JSON and display it properly
                $brands = json_decode($data->brand);
                return implode(', ', $brands);
            })
            ->addColumn('email', function($data) {
                $emails = json_decode($data->email);
                return implode(', ', $emails);
            })

            ->addColumn('contact', function($data) {
                $contacts = json_decode($data->contact);
                return implode(', ', $contacts);
            })

            ->addColumn('pic', function($data) {
                $pics = json_decode($data->pic);
                return implode(', ', $pics);
            })
            ->addColumn('action', function($data){
                $route = 'partner';
                return view ('partner.action', compact ('route', 'data'));
            })
            ->addindexcolumn()
            ->rawColumns(['categories'])
            ->make(true);
        }

        // $partner = Partner::all();
        return view ('partner.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('partner.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Upsert partner data based on 'name' uniqueness
            $partner = Partner::updateOrCreate(
                ['name' => $request->input('name')], // Search criteria
                [
                    'npwp' => $request->input('npwp'),
                    'description' => $request->input('description'),
                    'brand' => json_encode($request->input('brand')), // Storing brands as a JSON array
                    'email' => json_encode($request->input('email')), // Storing email as a JSON array
                    'contact' => json_encode($request->input('contact')), // Storing contact as a JSON array
                    'pic' => json_encode($request->input('pic')), // Storing PIC as a JSON array
                ]
            );

            // Upsert into user_partner pivot table
            $partner->users()->syncWithoutDetaching(auth()->id());

            // Insert into category_partner pivot table if categories are provided
            if ($request->has('category')) {
                $partner->categories()->sync($request->input('category'));
            }

            // Commit the transaction if everything is successful
            DB::commit();

            // Redirect to vendor index with success message
            Alert::success('Success', 'Vendor data successfully stored');
            return redirect()->route('partner.index');
            // ->with('success', 'Vendor upserted successfully.');

        } catch (\Exception $e) {
            // Rollback the transaction in case of any error
            DB::rollBack();

            // Log the error (optional)
            \Log::error('Error upserting vendor: ' . $e->getMessage());

            // Redirect back with error message
            return redirect()->back()->withErrors(['error' => 'Failed to upsert vendor. Please try again.']);
        }
    }




    /**
     * Display the specified resource.
     */
    public function show(Partner $partner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Partner $partner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Partner $partner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partner $partner)
    {
        //
    }
}
