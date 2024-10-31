<?php

namespace App\Http\Controllers;

use App\Models\Type;
use App\Models\Tender;
use App\Models\Partner;
use App\Models\Category;
use App\Models\TenderItem;
use Illuminate\Http\Request;
use App\Models\TenderDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TenderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Tender::with('partner', 'category', 'documents.type')->latest()->get(); // Tambahkan eager loading
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
                ->addColumn('document', function($data) {
                    $url = route('tender.documents', $data->id);
                    return '<button class="view-documents btn btn-primary btn-sm" data-url="' . $url . '">Download</button>';
                })
                ->addColumn('action', function($data){
                    $route = 'tender';
                    return view('tender.action', compact('route', 'data'));
                })
                ->rawColumns(['status', 'action', 'document'])
                ->make(true);
        }
        return view('tender.index');
    }

    public function getDocuments(Tender $tender)
    {
        $documentsHtml = '<table class="table table-bordered"><thead><tr><th>Type</th><th>Name</th><th>Action</th></tr></thead><tbody>';
        $documentsHtml .= $tender->documents->map(function ($doc) {
            $downloadButton = "<a href='{$doc->path}' target='_blank' class='btn btn-primary btn-sm'>Download</a>";
            return "<tr><td>{$doc->type->name}</td><td>{$doc->name}</td><td>{$downloadButton}</td></tr>";
        })->implode('');
        $documentsHtml .= '</tbody></table>';

        return response()->json($documentsHtml);

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
        $types = Type::where('category', 'Tender')->get();

        return view('tender.create', compact('partners', 'categories', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction(); // Memulai transaksi database

        try {
            // Validasi input dari form
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'estimation' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'partner_id' => 'required|exists:partners,id',
                // Validasi array item tender
                'items.description.*' => 'required|string|max:255',
                'items.specification.*' => 'required|string|max:255',
                'items.quantity.*' => 'required|integer|min:1',
                'items.unit.*' => 'required|string|max:255',
                'items.delivery.*' => 'required|string|max:255',
                //validasi file
                'types.*' => 'required|file|mimes:pdf|max:2048', // Adjust mime types and size as needed
                'notes.*' => 'required|string|max:255', // Adjust max length as needed
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
                // Jika partnerUser tidak ditemukan, batalkan transaksi
                DB::rollBack();
                return redirect()->back()->withErrors(['error' => 'You do not have permission to use this partner.']);
            }

            // Jika partnerUser ditemukan, lanjutkan
            $validatedData['partner_user_id'] = $partnerUser->id;

            // Hapus partner_id karena kita tidak lagi membutuhkannya
            unset($validatedData['partner_id']);
            $validatedData['name'] = strip_tags($validatedData['name']);
            $validatedData['location'] = strip_tags($validatedData['location']);
            $validatedData['estimation'] = strip_tags($validatedData['estimation']);

            // Buat tender baru menggunakan data yang tervalidasi
            $tender = Tender::create($validatedData);

            // Simpan item tender ke dalam tabel tender_items
            foreach ($request->items['description'] as $index => $description) {
                $description = strip_tags($description);
                $specification = strip_tags($request->items['specification'][$index]);
                $quantity = (int)$request->items['quantity'][$index];
                $unit = strip_tags($request->items['unit'][$index]);
                $delivery = strip_tags($request->items['delivery'][$index]);
                TenderItem::create([
                    'tender_id'     => $tender->id,
                    'description'   => $description,
                    'specification' => $specification,
                    'quantity'      => $quantity,
                    'unit'          => $unit,
                    'delivery'      => $delivery,
                ]);
            }

            // Simpan file ke dalam tabel tender_documents
            foreach ($request->types as $typeId => $file) {
                if ($file->isValid()) {
                    // Panggil metode upload untuk menyimpan file ke Google Drive
                    $this->upload($tender->id, $file, $typeId, $request->notes[$typeId] ?? null);
                }
            }
            // Jika semua proses berhasil, lakukan commit pada transaksi
            DB::commit();

            // Redirect ke route index dengan pesan sukses
            return redirect()->route('tender.index')->with('success', 'Tender created successfully.');
        } catch (\Exception $e) {
            // Jika ada error, rollback semua perubahan yang dilakukan
            DB::rollBack();

            // Kembalikan pesan error
            return redirect()->back()->withErrors(['error' => 'There was an error creating the tender: ' . $e->getMessage()]);
        }
    }

    public function upload($tenderId, $file, $typeId, $notes)
    {
        // Begin DB transaction
        DB::transaction(function () use ($file, $tenderId, $typeId, $notes) {
            // Define the file name (use time to ensure unique file names)
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Get the tender details
            $tender = Tender::findOrFail($tenderId);
            $tenderName = $tender->name; // Assuming 'name' is the column that stores the tender's name

            // Create a folder name for the tender
            $folderName = "{$tenderName}-{$tenderId}"; // Use tender name and ID in the folder name

            // Create folder if it doesn't exist
            $folderId = $this->createOrFindGoogleDriveFolder($folderName);

            // Store the file in Google Drive inside the tender folder
            Storage::disk('google')->putFileAs($folderId, $file, $fileName); // Upload to the tender's folder

            // Generate the public URL for the uploaded file
            $filePath = Storage::disk('google')->url($folderId . '/' . $fileName); // Construct the URL

            // Sanitize the 'notes' field to remove any potential XSS risk
            $sanitizedNotes = strip_tags($notes);

            // Save the file information to the database
            TenderDocument::create([
                'tender_id' => $tenderId,
                'type_id' => $typeId,
                'name' => $fileName,
                'path' => $filePath, // Path on Google Drive for download
                'note' => $sanitizedNotes, // Sanitized notes
            ]);
        });
    }


    /**
 * Creates or finds a folder on Google Drive by folder name
 *
 * @param string $folderName
 * @return string Folder ID
 */

    private function createOrFindGoogleDriveFolder($folderName)
    {
        // Step 1: Find or create the "Tenders" folder
        $parentFolderName = 'TENDERS';
        $parentFolderId = null;

        // Check if the "Tenders" folder exists
        $folders = Storage::disk('google')->listContents('/', false);
        foreach ($folders as $folder) {
            if ($folder->type() === 'dir' && basename($folder->path()) === $parentFolderName) {
                $parentFolderId = $folder->path();
                break;
            }
        }

        // If "Tenders" folder doesn't exist, create it
        if (!$parentFolderId) {
            Storage::disk('google')->makeDirectory($parentFolderName);
            // List contents again to get the newly created "Tenders" folder ID
            $folders = Storage::disk('google')->listContents('/', false);
            foreach ($folders as $folder) {
                if ($folder->type() === 'dir' && basename($folder->path()) === $parentFolderName) {
                    $parentFolderId = $folder->path();
                    break;
                }
            }
        }

        if (!$parentFolderId) {
            throw new \Exception('Failed to create or find "Tenders" folder.');
        }

        // Step 2: Find or create the partner-specific folder within the "Vendors" folder
        $tenderFolderId = null;
        $tenderFolders = Storage::disk('google')->listContents($parentFolderId, false);

        foreach ($tenderFolders as $folder) {
            if ($folder->type() === 'dir' && basename($folder->path()) === $folderName) {
                $tenderFolderId = $folder->path();
                break;
            }
        }

        // If the partner folder doesn't exist, create it inside the "Vendors" folder
        if (!$tenderFolderId) {
            Storage::disk('google')->makeDirectory($parentFolderId . '/' . $folderName);
            // List contents again to get the newly created partner folder ID
            $tenderFolders = Storage::disk('google')->listContents($parentFolderId, false);
            foreach ($tenderFolders as $folder) {
                if ($folder->type() === 'dir' && basename($folder->path()) === $folderName) {
                    return $folder->path();
                }
            }
        }
        return $tenderFolderId;
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

}
