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
            ->when(!auth()->user()->is_admin, function ($query) {
                // Filter partners by the authenticated user only if not admin
                $query->whereHas('users', function($q) {
                    $q->where('user_id', auth()->id());
                });
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
                ->editColumn('is_verified', function ($data) {
                    if ($data->is_verified == 0) {
                        return '<span class="badge bg-info">Process</span>';
                    } elseif ($data->is_verified == 1) {
                        return '<span class="badge bg-success">Verified</span>';
                    }
                    return '<span class="badge text-bg-dark">Unknown</span>';
                })
                ->addIndexColumn()
                ->rawColumns(['categories', 'brand', 'email', 'pic', 'contact', 'is_verified'])
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
        $validatedData = $request->validate(['name' => 'required|string']);

        // Strip any potential HTML/PHP tags from 'name'
        $cleanName = strip_tags($validatedData['name']);

        // Search for vendor by name and include brands and categories
        $vendor = Partner::where('name', $cleanName)
            ->with(['brands', 'categories']) // Include brands and categories relationships
            ->first();

        // If vendor is found
        if ($vendor) {
            return response()->json([
                'exists' => true,
                'npwp' => e($vendor->npwp), // Escape NPWP to avoid XSS
                'description' => e($vendor->description), // Escape description
                'categories' => $vendor->categories->pluck('id'), // Get related category IDs
                'brands' => $vendor->brands->pluck('name')->map('e'), // Escape each brand name
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
            'npwp' => 'required|string|max:20',
            'description' => 'required|string', // not allow null for description
            'brand' => 'required|array', // Brands must be an array
            'brand.*' => 'required|string|max:255', // Each brand must be a string
            'category' => 'required|array', // Categories must be an array
            'category.*' => 'exists:categories,id', // Categories must exist in the database
        ]);

        // Bersihkan tag PHP atau HTML dari input
        $validatedData['name'] = strip_tags($validatedData['name']);
        $validatedData['npwp'] = strip_tags($validatedData['npwp']);
        $validatedData['description'] = strip_tags($validatedData['description']);

        foreach ($validatedData['brand'] as &$brandName) {
            $brandName = strip_tags($brandName);
        }

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
                    $partner->brands()->updateOrCreate(
                        ['name' => $brandName], // Kondisi untuk update atau create
                        ['name' => $brandName] // Data yang akan dibuat atau diperbarui
                    );
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

    public function verify($encryptPartnerId)
    {
        $id = decrypt($encryptPartnerId);
        DB::beginTransaction(); // Memulai transaksi

        try {
            // Cari partner berdasarkan ID
            $partner = Partner::findOrFail($id);

            // Jika sudah diverifikasi, batalkan verifikasi (set is_verified = false)
            // Jika belum, lakukan verifikasi (set is_verified = true)
            $partner->is_verified = !$partner->is_verified;
            $partner->save();

            DB::commit(); // Commit transaksi jika semua berjalan lancar

            $message = $partner->is_verified ? 'Partner verified successfully!' : 'Partner verification canceled!';
            return redirect()->route('partner.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback jika terjadi kesalahan

            return redirect()->route('partner.index')->with('error', 'Failed to update verification status. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($encryptPartnerId)
    {
        $id = decrypt($encryptPartnerId);
        $categories = Category::all();
        $partner = Partner::findOrFail($id);
        return view('partner.edit', compact('partner', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $encryptPartnerId)
    {
        $id = decrypt($encryptPartnerId);
        $partner = Partner::findOrFail($id);

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'npwp' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|array',
            'category.*' => 'exists:categories,id',
            'brand' => 'required|array',
            'brand.*' => 'string|max:255',
        ]);

        // Bersihkan tag HTML atau PHP dari input untuk mencegah XSS
        $validatedData['name'] = strip_tags($validatedData['name']);
        $validatedData['npwp'] = strip_tags($validatedData['npwp']);
        $validatedData['description'] = strip_tags($validatedData['description']);

        foreach ($validatedData['brand'] as &$brandName) {
            $brandName = strip_tags($brandName);
        }

        // Check if the name already exists (excluding the current partner)
        $existingPartner = Partner::where('name', $validatedData['name'])
                                ->where('id', '!=', $id)
                                ->first();

        if ($existingPartner) {
            return back()->withErrors(['name' => 'This name already exists. Please choose a different name.'])
                        ->withInput();
        }

        // Start database transaction
        DB::beginTransaction();

        try {
            // Update the partner
            $partner->update([
                'name' => $validatedData['name'],
                'npwp' => $validatedData['npwp'],
                'description' => $validatedData['description'],
            ]);

            // Sync categories
            $partner->categories()->sync($validatedData['category']);

            // Update brands
            if ($request->has('brand')) {
                $inputBrands = $validatedData['brand'];

                // Remove brands not in the new input
                $partner->brands()->whereNotIn('name', $inputBrands)->delete();

                // Add or keep existing brands
                foreach ($inputBrands as $brandName) {
                    $partner->brands()->firstOrCreate(['name' => $brandName]);
                }
            } else {
                // If no brands are provided, remove all existing brands
                $partner->brands()->delete();
            }

            DB::commit();
            return redirect()->route('partner.index')->with('success', 'Partner updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'An error occurred while updating the partner. Please try again.'])
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function remove($encryptPartnerId)
    {
        $id = decrypt($encryptPartnerId);
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
                return redirect()->route('partner.index')->with('success', 'You have been removed from the partner, and the partner has been deleted as it has no more associated users.');
            }

            return redirect()->route('partner.index')->with('success', 'You have been removed from the partner.');
        } catch (\Exception $e) {
            \Log::error('Error removing user from partner: ' . $e->getMessage());
            return redirect()->route('partner.index')->with('error', 'Failed to remove you from the partner. Please try again.');
        }
    }

    public function destroy($encryptPartnerId)
    {
        $id = decrypt($encryptPartnerId);
        // Ensure only admin can perform this action
        if (!Auth::user()->is_admin) {
            return redirect()->route('partner.index')->with('error', 'You do not have permission to delete partners.');
        }

        try {
            // Find the partner or throw a 404
            $partner = Partner::findOrFail($id);

            // Start a database transaction
            DB::beginTransaction();

            // Log the deletion attempt
            \Log::info("Admin user " . Auth::id() . " is attempting to delete partner " . $id);

            // Delete related brands
            $deletedBrands = $partner->brands()->delete();
            \Log::info("Deleted {$deletedBrands} related brands for partner {$id}");

            // Detach all categories
            $detachedCategories = $partner->categories()->detach();
            \Log::info("Detached {$detachedCategories} categories from partner {$id}");

            // Detach all users
            $detachedUsers = $partner->users()->detach();
            \Log::info("Detached {$detachedUsers} users from partner {$id}");

            // Delete the partner
            $partner->delete();
            \Log::info("Successfully deleted partner {$id}");

            // Commit the transaction
            DB::commit();

            return redirect()->route('partner.index')->with('success', 'Partner has been successfully deleted.');
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollBack();

            // Log the error
            \Log::error('Error deleting partner: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return redirect()->route('partner.index')->with('error', 'Failed to delete partner. Please try again or contact support.');
        }
    }

    public function upload($encryptPartnerId)
    {
        $id = decrypt($encryptPartnerId);
        $partner = Partner::find($id);

        $types = Type::all();

        return view('partner.upload', compact('partner', 'types'));
    }

    public function save(Request $request, $encryptPartnerId)
    {
        // decrypt partner_id dari route
        $id = decrypt($encryptPartnerId);
        $partnerId = $id;

        // Validate the incoming request
        $request->validate([
            'company_profile' => 'required|file|mimes:pdf|max:2048', // PDF file with a max size of 2MB
            'type_id' => 'required|exists:types,id', // Validates that the selected type exists in the types table
            'notes' => 'required|string|max:255', // Optional notes with a max length of 255 characters
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

                      // Sanitize the 'notes' field to remove any potential XSS risk
                    $sanitizedNotes = strip_tags($request->input('notes'));

                    // Save the file information to the database
                    $fileData = [
                        'partner_id' => $partnerId, // Ambil partner_id dari route
                        'type_id' => $request->input('type_id'),
                        'name' => $fileName,
                        'path' => $filePath, // Path on Google Drive for download
                        'note' => $sanitizedNotes, // Sanitized notes
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
        // Step 1: Find or create the "Vendors" folder
        $parentFolderName = 'VENDORS';
        $parentFolderId = null;

        // Check if the "Vendors" folder exists
        $folders = Storage::disk('google')->listContents('/', false);
        foreach ($folders as $folder) {
            if ($folder->type() === 'dir' && basename($folder->path()) === $parentFolderName) {
                $parentFolderId = $folder->path();
                break;
            }
        }

        // If "Vendors" folder doesn't exist, create it
        if (!$parentFolderId) {
            Storage::disk('google')->makeDirectory($parentFolderName);
            // List contents again to get the newly created "Vendors" folder ID
            $folders = Storage::disk('google')->listContents('/', false);
            foreach ($folders as $folder) {
                if ($folder->type() === 'dir' && basename($folder->path()) === $parentFolderName) {
                    $parentFolderId = $folder->path();
                    break;
                }
            }
        }

        if (!$parentFolderId) {
            throw new \Exception('Failed to create or find "Vendors" folder.');
        }

        // Step 2: Find or create the partner-specific folder within the "Vendors" folder
        $partnerFolderId = null;
        $partnerFolders = Storage::disk('google')->listContents($parentFolderId, false);

        foreach ($partnerFolders as $folder) {
            if ($folder->type() === 'dir' && basename($folder->path()) === $folderName) {
                $partnerFolderId = $folder->path();
                break;
            }
        }

        // If the partner folder doesn't exist, create it inside the "Vendors" folder
        if (!$partnerFolderId) {
            Storage::disk('google')->makeDirectory($parentFolderId . '/' . $folderName);
            // List contents again to get the newly created partner folder ID
            $partnerFolders = Storage::disk('google')->listContents($parentFolderId, false);
            foreach ($partnerFolders as $folder) {
                if ($folder->type() === 'dir' && basename($folder->path()) === $folderName) {
                    return $folder->path();
                }
            }
        }

        return $partnerFolderId;
    }


    public function fileDelete($fileId)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Find the file
            $file = File::findOrFail($fileId);

            // Delete the file from storage
            if (Storage::exists($file->path)) {
                Storage::delete($file->path);
            }

            // Delete the file record from the database
            $file->delete();

            // Commit the transaction
            DB::commit();

            return response()->json(['success' => true, 'message' => 'File deleted successfully']);
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            // Log the error
            \Log::error('Error deleting file: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to delete file'], 500);
        }
    }

}
