<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        // Cek apakah ini adalah request AJAX untuk DataTables
        if ($request->ajax()) {
            $partners = Partner::query();

            // Filter berdasarkan kategori jika ada
            if ($request->has('category') && $request->category != '') {
                $partners->whereHas('categories', function($query) use ($request) {
                    $query->where('category_id', $request->category);
                });
            }

            // Filter berdasarkan brand jika ada
            if ($request->has('brand') && $request->brand != '') {
                $partners->whereRaw('LOWER(brand) LIKE ?', ['%' . strtolower($request->brand) . '%']);
            }

            // Mengembalikan data untuk DataTables
            return DataTables::of($partners)
                ->addColumn('categories', function($data) {
                    $categories = $data->categories->pluck('name');
                    return $categories->map(function($category, $index) {
                        return ($index + 1) . '.' . $category;
                    })->implode('<br>'); // Menambahkan nomor dengan line break
                })
                ->addColumn('brand', function($data) {
                    // Jika 'brand' adalah JSON, gunakan json_decode
                    $brands = is_string($data->brand) ? json_decode($data->brand) : [$data->brand];
                    return collect($brands)->map(function($brand, $index) {
                        return ($index + 1) . '.' . $brand;
                    })->implode('<br>'); // Menambahkan nomor dengan line break
                })
                ->addColumn('email', function($data) {
                    // Jika 'email' adalah JSON, gunakan json_decode
                    $emails = is_string($data->email) ? json_decode($data->email) : [$data->email];
                    return collect($emails)->map(function($email, $index) {
                        return ($index + 1) . '.' . $email;
                    })->implode('<br>'); // Menambahkan nomor dengan line break
                })
                ->addColumn('contact', function($data) {
                    // Jika 'contact' adalah JSON, gunakan json_decode
                    $contacts = is_string($data->contact) ? json_decode($data->contact) : [$data->contact];
                    return collect($contacts)->map(function($contact, $index) {
                        return ($index + 1) . '.' . $contact;
                    })->implode('<br>'); // Menambahkan nomor dengan line break
                })
                ->addColumn('pic', function($data) {
                    // Jika 'pic' adalah JSON, gunakan json_decode
                    $pics = is_string($data->pic) ? json_decode($data->pic) : [$data->pic];
                    return collect($pics)->map(function($pic, $index) {
                        return ($index + 1) . '.' . $pic;
                    })->implode('<br>'); // Menambahkan nomor dengan line break
                })
                ->addColumn('action', function($data){
                    $route = 'partner';
                    return view('partner.action', compact('route', 'data'));
                })
                ->addIndexColumn()
                ->rawColumns(['categories', 'brand', 'email', 'pic', 'contact'])
                ->make(true);
        }

        // Jika bukan AJAX request, kembalikan view
        $categories = Category::all(); // Pastikan Anda mengambil data kategori
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Cek apakah partner dengan nama tersebut sudah ada
        $partner = Partner::where('name', $request->input('name'))->first();

        // Jika partner sudah ada, kita akan mengecualikan validasi unique berdasarkan id
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                $partner ? Rule::unique('partners', 'name')->ignore($partner->id) : 'unique:partners,name',
            ],  // Nama wajib diisi dan unik kecuali untuk data yang sedang di-update
            'npwp' => 'required|string|max:15', // NPWP wajib dan maksimal 15 karakter
            'description' => 'required|string', // Deskripsi bisa kosong
            'brand' => 'required|array', // Brand harus berupa array (karena JSON array)
            'brand.*' => 'required|string|max:255', // Setiap item dalam array brand wajib diisi
            'email' => 'required|array', // Email harus berupa array
            'email.*' => 'required|email|max:255', // Setiap item dalam array email harus format email yang valid
            'contact' => 'required|array', // Kontak harus berupa array
            'contact.*' => 'required|string|max:15', // Setiap item dalam array kontak maksimal 15 karakter
            'pic' => 'required|array', // PIC harus berupa array
            'pic.*' => 'required|string|max:255', // Setiap item dalam array PIC wajib diisi
            'category' => 'required|array', // Kategori bisa kosong tapi jika ada harus array
            'category.*' => 'exists:categories,id' // Setiap kategori harus ada dalam tabel kategori
        ]);
        DB::beginTransaction();
        try {
            // Upsert partner data based on 'name' uniqueness
            $partner = Partner::updateOrCreate(
                ['name' => $validatedData['name']], // Search criteria
                [
                    'npwp' => $validatedData['npwp'],
                    'description' => $request->input('description'), // Bisa null
                    'brand' => json_encode($validatedData['brand']), // Simpan brand sebagai JSON array
                    'email' => json_encode($validatedData['email']), // Simpan email sebagai JSON array
                    'contact' => json_encode($validatedData['contact']), // Simpan kontak sebagai JSON array
                    'pic' => json_encode($validatedData['pic']), // Simpan PIC sebagai JSON array
                ]
            );

            // Upsert into user_partner pivot table
            $partner->users()->syncWithoutDetaching(auth()->id());

            // Insert into category_partner pivot table if categories are provided
            if ($request->has('category')) {
                $partner->categories()->sync($validatedData['category']);
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

        // Display error alert
        Alert::error('Error', 'Failed to upsert vendor. Please try again.');

        // Redirect back with error message
        return redirect()->back()->withInput(); // Use withInput() to preserve form data
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
