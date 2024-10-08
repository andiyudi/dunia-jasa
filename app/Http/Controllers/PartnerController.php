<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Type;
use App\Models\Partner;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
            })
            ->orderBy('created_at', 'desc'); // Order by created_at descending to get the most recent first
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
                // Ambil semua nama brand yang diinputkan
                $inputBrands = $validatedData['brand'];

                // Hapus brand yang tidak ada dalam input terbaru
                $partner->brands()->whereNotIn('name', $inputBrands)->delete();

                // Loop untuk menambahkan atau mempertahankan brand yang ada
                foreach ($inputBrands as $brandName) {
                    // Cek apakah brand sudah ada
                    $existingBrand = $partner->brands()->where('name', $brandName)->first();

                    // Jika brand belum ada, buat yang baru
                    if (!$existingBrand) {
                        $partner->brands()->create(['name' => $brandName]);
                    }
                }
            }

            DB::commit();

            // Return success message and redirect
            return redirect()->route('partner.index')->with('success', 'Vendor data successfully stored');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            \Log::error('Error upserting vendor: ' . $e->getMessage());

            // Display error alert and redirect back
            return redirect()->back()->withInput()->with('error', 'Failed to upsert vendor. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($encryptPartnerId)
    {
        $id = decrypt($encryptPartnerId);
        $partner = Partner::with(['files' => function($query) {
            $query->orderBy('created_at', 'desc'); // Mengurutkan file terbaru di atas
        }, 'users', 'categories', 'brands'])->find($id);
        return view ('partner.show', compact('partner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($encryptPartnerId)
    {
        $id = decrypt($encryptPartnerId);
        dd($id);
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
    public function destroy($id)
    {
        $userId = Auth::id();

        try {
            $partner = Partner::findOrFail($id);

            // Detach the user from the partner
            $partner->users()->detach($userId);

            // Check if there are no more users attached to the partner
            if ($partner->users()->count() == 0) {
                $partner->brands()->delete();  // Assuming 'brands' is the relationship method in Partner
                $partner->categories()->detach(); // Remove all relationships with categories
                $partner->delete(); // Delete the partner if no users are left
                return redirect()->route('partner.index')->with('success', 'User has been removed and the partner has been deleted.');
            }

            return redirect()->route('partner.index')->with('success', 'User has been removed from the partner.');
        } catch (\Exception $e) {
            \Log::error('Error removing user from partner: ' . $e->getMessage());
            return redirect()->route('partner.index')->with('error', 'Failed to remove user from partner. Please try again.');
        }
    }

    public function upload($encryptPartnerId)
    {
        $id = decrypt($encryptPartnerId);
        $partner = Partner::find($id);

        $types = Type::all();

        return view('partner.upload', compact('partner', 'types'));
    }

    public function save(Request $request, $id)
    {
        // Dekripsi partner_id dari route
        $partnerId = $id;

        // Validate the incoming request
        $request->validate([
            'company_profile' => 'required|file|mimes:pdf|max:2048', // PDF file with a max size of 2MB
            'type_id' => 'required|exists:types,id', // Validates that the selected type exists in the types table
            'notes' => 'nullable|string|max:255', // Optional notes with a max length of 255 characters
        ]);

        try {
            // Begin DB transaction
            DB::transaction(function () use ($request, $partnerId) {
                // Check if a file is uploaded
                if ($request->hasFile('company_profile')) {
                    // Get the uploaded file
                    $file = $request->file('company_profile');

                    // Define the file name (use time to ensure unique file names)
                    $fileName = time() . '_' . $file->getClientOriginalName();

                    // Get the partner details
                    $partner = Partner::findOrFail($partnerId);
                    $folderName = $partner->name . '-' . $partnerId; // folder name format: partner-name-id

                    // Create folder if it doesn't exist
                    $folderId = $this->createOrFindGoogleDriveFolder($folderName);

                    // Store the file in Google Drive inside the partner folder
                    Storage::disk('google')->putFileAs($folderId, $file, $fileName); // Upload to the partner's folder

                    // Generate the public URL for the uploaded file
                    $filePath = Storage::disk('google')->url($folderId . '/' . $fileName); // Construct the URL

                    // Save the file information to the database
                    $fileData = [
                        'partner_id' => $partnerId, // Ambil partner_id dari route
                        'type_id' => $request->input('type_id'),
                        'name' => $fileName,
                        'path' => $filePath, // Path on Google Drive for download
                        'note' => $request->input('notes'), // Optional notes
                    ];

                    File::create($fileData); // Create a new entry in the files table
                }
            });

            // Commit the transaction and redirect back with a success message
            return redirect()->route('partner.index')->with('success', 'File uploaded successfully');

        } catch (\Exception $e) {
            // Rollback transaction and handle any errors during the upload process
            return redirect()->back()->with('error', 'Failed to upload file: ' . $e->getMessage());
        }
    }

    /**
     * Creates or finds a folder on Google Drive by folder name
     *
     * @param string $folderName
     * @return string Folder ID
     */
    private function createOrFindGoogleDriveFolder($folderName)
    {
        // Check if the folder exists in Google Drive
        $folders = Storage::disk('google')->listContents('/', false);

        // Manually search for a folder with the specific folder name
        $folderId = null;
        foreach ($folders as $folder) {
            if ($folder->type() === 'dir' && basename($folder->path()) === $folderName) {
                $folderId = $folder->path();
                break;
            }
        }

        // If the folder is found, return the folder ID
        if ($folderId) {
            return $folderId;
        } else {
            // If the folder does not exist, create it
            Storage::disk('google')->makeDirectory($folderName);

            // List contents again to get the newly created folder ID
            $folders = Storage::disk('google')->listContents('/', false);
            foreach ($folders as $folder) {
                if ($folder->type() === 'dir' && basename($folder->path()) === $folderName) {
                    return $folder->path();
                }
            }
        }

        throw new \Exception('Failed to create or find Google Drive folder.');
    }

}
