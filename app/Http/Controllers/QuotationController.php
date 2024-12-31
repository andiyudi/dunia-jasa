<?php

namespace App\Http\Controllers;

use App\Models\Type;
use App\Models\Tender;
use App\Models\Partner;
use App\Models\Quotation;
use App\Models\TenderItem;
use Illuminate\Http\Request;
use App\Models\QuotationFiles;
use App\Models\QuotationPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Get the current user's ID
            $currentUserId = auth()->id();

            // Eager load necessary relationships and join with the partner_user table to get the user_id of the creator
            $data = Tender::with(['partner', 'category', 'documents.type', 'items.quotations']) // Eager load 'quotations' for items
                ->select('tenders.*', 'partner_user.user_id as creator_id')
                ->leftJoin('partner_user', 'tenders.partner_user_id', '=', 'partner_user.id')
                ->where('partner_user.user_id', '!=', $currentUserId) // Exclude tenders created by the current user
                ->latest()
                ->get();

            foreach ($data as $tender) {
                $tender->is_submitted = false; // Default: belum ada quotation

                foreach ($tender->items as $item) {
                    // Ambil semua partner_user_id untuk user login dari tabel pivot
                    $partnerUserIds = auth()->user()->partners->pluck('pivot.id')->toArray();

                    // Cek apakah ada quotation dengan partner_user_id milik user login
                    $quotation = $item->quotations()->whereIn('partner_user_id', $partnerUserIds)->first();

                    if ($quotation) {
                        $tender->is_submitted = true;
                        break; // Jika ditemukan, keluar dari loop
                    }
                }
            }

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
                    return $data->category->name ?? 'Unknown';
                })
                ->addColumn('document', function($data) {
                    $url = route('tender.documents', $data->id);
                    return '<button class="view-documents btn btn-primary btn-sm" data-url="' . $url . '">Download</button>';
                })
                ->addColumn('action', function($data){
                    $route = 'quotation';
                    $disabled = ($data->is_submitted || $data->status == 1) ? 'disabled' : ''; // Tambahkan kondisi untuk status == 1 // Disable button if already submitted
                    return view('tender.quotation.action', compact('route', 'data', 'disabled'));
                })
                // ->addColumn('is_submitted', function ($data) {
                //     return $data->is_submitted ? 'Yes' : 'No';
                // })
                ->rawColumns(['status', 'action', 'document'])
                ->make(true);
        }

        return view('tender.quotation.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            // Decrypt the tender_id from the query parameter
            $tenderId = decrypt($request->input('tender_id'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Invalid tender ID.');
        }

        // Retrieve tender information or return an error if not found
        $tender = Tender::find($tenderId);

        if (!$tender) {
            return redirect()->back()->withErrors('Tender not found.');
        }

        // Retrieve the name of the first partner associated with the tender
        $excludedPartnerName = $tender->partner->first()->name;

        // Retrieve partners associated with the logged-in user, excluding those with the same name as the tender's partner
        $userPartners = auth()->user()->partners->filter(function ($partner) use ($excludedPartnerName) {
            return $partner->name !== $excludedPartnerName;
        });

        // Pass $tender and filtered $userPartners to the view
        return view('tender.quotation.create', compact('tender', 'userPartners'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        // dd($request->all());
        // Validasi data untuk items
        $validatedData = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'items' => 'required|array',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.delivery_time' => 'required|string',
            'items.*.remark' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf|max:2048',
            'note' => 'nullable|string|max:255',
            'terms_of_payment' => 'nullable|string|max:255',
        ]);

        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Cari partner_user_id berdasarkan partner_id dan user yang sedang masuk
            $partnerUser = auth()->user()->partners()->where('partners.id', $validatedData['partner_id'])->first();

            if (!$partnerUser) {
                throw new \Exception('Invalid partner-user association.');
            }

            $partnerUserId = $partnerUser->pivot->id; // Ambil ID dari tabel pivot

             // Pengecekan apakah ada user_id lain yang sudah submit quotation dengan partner_id yang sama
            $existingQuotations = Quotation::whereIn('tender_item_id', array_keys($validatedData['items']))
                ->whereHas('partnerUser.partner', function ($query) use ($validatedData) {
                    $query->where('id', $validatedData['partner_id']);
                })->exists();


            if ($existingQuotations) {
                throw new \Exception('Another user from this partner has already submitted a quotation.');
            }

             // Iterasi melalui setiap item dalam array
            foreach ($validatedData['items'] as $itemId => $item) {
                // Pastikan item_id ada dalam tender_items
                $tenderItem = TenderItem::findOrFail($itemId);

                // Hitung total harga untuk item
                $totalPrice = $item['price'] * $tenderItem->quantity;

                // Simpan quotation ke database dan ambil instance yang baru dibuat
                Quotation::create([
                    'tender_item_id' => $itemId,
                    'partner_user_id' => $partnerUserId,
                    'price' => $item['price'],
                    'total_price' => $totalPrice,
                    'delivery_time' => $item['delivery_time'],
                    'remark' => $item['remark'] ?? null,
                ]);
            }
            // Simpan data ke quotation_payment
            QuotationPayment::create([
                'tender_id' => $tenderItem->tender_id,
                'partner_id' => $validatedData['partner_id'],
                'terms_of_payment' => $validatedData['terms_of_payment'] ?? null,
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();

                // Cari atau buat folder di Google Drive
                $partner = Partner::findOrFail($validatedData['partner_id']);
                $folderName = $partner->name . '-' . $partner->id;
                $folderId = $this->createOrFindGoogleDriveFolder($folderName);

                // Upload file ke Google Drive
                Storage::disk('google')->putFileAs($folderId, $file, $fileName);

                // Generate URL file di Google Drive
                $filePath = Storage::disk('google')->url($folderId . '/' . $fileName);

                $typeId = Type::where('category', 'Quotation')->value('id');

                // Simpan informasi file ke database
                QuotationFiles::create([
                    'tender_id' => $tenderItem->tender_id,
                    'partner_id' => $validatedData['partner_id'],
                    'type_id' => $typeId,
                    'name' => $fileName,
                    'path' => $filePath,
                    'note' => $validatedData['note'] ?? null,
                ]);
            }

             // Commit transaksi jika semua berhasil
            DB::commit();

             // Redirect kembali dengan pesan sukses
            return to_route('quotation.index')->with('success', 'Quotations submitted successfully.');
            } catch (\Exception $e) {
             // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();

             // Log error jika diperlukan
            \Log::error('Error creating quotations: ' . $e->getMessage());

             // Redirect kembali dengan pesan error
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
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
        // Step 1: Find or create the "Quotation" folder
        $parentFolderName = 'QUOTATION';
        $parentFolderId = null;

        // Check if the "Quotation" folder exists
        $folders = Storage::disk('google')->listContents('/', false);
        foreach ($folders as $folder) {
            if ($folder->type() === 'dir' && basename($folder->path()) === $parentFolderName) {
                $parentFolderId = $folder->path();
                break;
            }
        }

        // If "Quotation" folder doesn't exist, create it
        if (!$parentFolderId) {
            Storage::disk('google')->makeDirectory($parentFolderName);
            // List contents again to get the newly created "Quotation" folder ID
            $folders = Storage::disk('google')->listContents('/', false);
            foreach ($folders as $folder) {
                if ($folder->type() === 'dir' && basename($folder->path()) === $parentFolderName) {
                    $parentFolderId = $folder->path();
                    break;
                }
            }
        }

        if (!$parentFolderId) {
            throw new \Exception('Failed to create or find "Quotation" folder.');
        }

        // Step 2: Find or create the partner-specific folder within the "Quotation" folder
        $partnerFolderId = null;
        $partnerFolders = Storage::disk('google')->listContents($parentFolderId, false);

        foreach ($partnerFolders as $folder) {
            if ($folder->type() === 'dir' && basename($folder->path()) === $folderName) {
                $partnerFolderId = $folder->path();
                break;
            }
        }

        // If the partner folder doesn't exist, create it inside the "Quotation" folder
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


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
