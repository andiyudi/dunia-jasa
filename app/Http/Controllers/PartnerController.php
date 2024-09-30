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
    public function index(Request $request)
    {
        // Check if this is an AJAX request for DataTables
        if ($request->ajax()) {
            $partners = Partner::query()
            ->with('categories', 'brands', 'users') // Eager load relationships
            ->whereHas('users', function($query) {
                // Filter partners by the authenticated user
                $query->where('user_id', auth()->id());
            });

            // Filter by category if provided
            if ($request->has('category') && $request->category != '') {
                $partners->whereHas('categories', function($query) use ($request) {
                    $query->where('category_id', $request->category);
                });
            }

            // Filter by brand if provided
            if ($request->has('brand') && $request->brand != '') {
                $partners->whereHas('brands', function($query) use ($request) {
                    $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->brand) . '%']);
                });
            }

            // Return data for DataTables
            return DataTables::of($partners)
                ->addColumn('categories', function($data) {
                    $categories = $data->categories->pluck('name');
                    return $categories->map(function($category, $index) {
                        return ($index + 1) . '.' . $category;
                    })->implode('<br>'); // Numbered list with line breaks
                })
                ->addColumn('brand', function($data) {
                    // Fetch brands from the brands table (one-to-many relationship)
                    $brands = $data->brands->pluck('name');
                    return $brands->map(function($brand, $index) {
                        return ($index + 1) . '.' . $brand;
                    })->implode('<br>'); // Numbered list with line breaks
                })
                ->addColumn('email', function($data) {
                    // Fetch emails from the related users table (many-to-many relationship)
                    $emails = $data->users->pluck('email');
                    return $emails->map(function($email, $index) {
                        return ($index + 1) . '.' . $email;
                    })->implode('<br>'); // Numbered list with line breaks
                })
                ->addColumn('contact', function($data) {
                    // Assuming 'contact' is a field in the users table, you can retrieve it from there
                    $contacts = $data->users->pluck('phone');
                    return $contacts->map(function($contact, $index) {
                        return ($index + 1) . '.' . $contact;
                    })->implode('<br>'); // Numbered list with line breaks
                })
                ->addColumn('pic', function($data) {
                    // Fetch PICs from the related users table (many-to-many relationship)
                    $pics = $data->users->pluck('name'); // Assuming 'name' is the PIC
                    return $pics->map(function($pic, $index) {
                        return ($index + 1) . '.' . $pic;
                    })->implode('<br>'); // Numbered list with line breaks
                })
                ->addColumn('action', function($data){
                    $route = 'partner';
                    return view('partner.action', compact('route', 'data'));
                })
                ->addIndexColumn()
                ->rawColumns(['categories', 'brand', 'email', 'pic', 'contact'])
                ->make(true);
        }

        // If not an AJAX request, return the view
        $categories = Category::all(); // Fetch all categories for filtering
        return view('partner.index', compact('categories'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('partner.create', compact('categories'));
    }

    public function checkName(Request $request)
    {
        // Validate that 'name' is present in the request
        $request->validate(['name' => 'required|string']);

        // Search for vendor by name and include brands and categories
        $vendor = Partner::where('name', $request->name)
            ->with(['brands', 'categories']) // Include brands and categories relationships
            ->first();

        // If vendor is found
        if ($vendor) {
            return response()->json([
                'exists' => true,
                'npwp' => $vendor->npwp,
                'description' => $vendor->description,
                'categories' => $vendor->categories->pluck('id'), // Get related category IDs
                'brands' => $vendor->brands->pluck('name'), // Get related brand names
            ]);
        }

        // If vendor is not found
        return response()->json(['exists' => false]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request input
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'npwp' => 'required|string|max:15',
            'description' => 'required|string', // Allow null for description
            'brand' => 'required|array', // Brands must be an array
            'brand.*' => 'required|string|max:255', // Each brand must be a string
            'category' => 'required|array', // Categories must be an array
            'category.*' => 'exists:categories,id', // Categories must exist in the database
        ]);

        DB::beginTransaction();

        try {
            // Check if the partner exists by name
            $partner = Partner::where('name', $validatedData['name'])->first();

            if ($partner) {
                // Update existing partner
                $partner->update([
                    'npwp' => $validatedData['npwp'],
                    'description' => $validatedData['description'],
                ]);
            } else {
                // Create a new partner
                $partner = Partner::create([
                    'name' => $validatedData['name'],
                    'npwp' => $validatedData['npwp'],
                    'description' => $validatedData['description'],
                ]);
            }

            // Sync partner with user (many-to-many)
            $partner->users()->syncWithoutDetaching(auth()->id());

            // Sync partner with categories (many-to-many)
            if ($request->has('category')) {
                $partner->categories()->sync($validatedData['category']);
            }

            // Insert or update the brands (one-to-many)
            if ($request->has('brand')) {
                $partner->brands()->delete(); // Clear existing brands if any
                foreach ($validatedData['brand'] as $brandName) {
                    $partner->brands()->create(['name' => $brandName]);
                }
            }

            DB::commit();

            // Return success message and redirect
            Alert::success('Success', 'Vendor data successfully stored');
            return redirect()->route('partner.index');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            \Log::error('Error upserting vendor: ' . $e->getMessage());

            // Display error alert and redirect back
            Alert::error('Error', 'Failed to upsert vendor. Please try again.');
            return redirect()->back()->withInput();
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
