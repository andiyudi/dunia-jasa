<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('type.index');
    }

    public function getData()
    {
        // Ambil semua data kategori dari database
        $types = Type::select(['id','name','category']);

        // Return data dalam format DataTables
        return DataTables::of($types)
            ->addColumn('action', function($row){
                return "<button data-id='$row->id' class='btn btn-sm btn-warning edit-type'>Edit</button>
                        <button data-id='$row->id' class='btn btn-sm btn-danger delete-type'>Delete</button>";
            })
            ->addIndexColumn()
            ->rawColumns(['action']) // Supaya kolom action dapat menampilkan HTML
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);

        // Simpan data kategori baru
        Type::create([
            'name' => $request->name,
            'category' => $request->category,
        ]);

        // Return response sukses
        return response()->json(['success' => 'Type created successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Type $type)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $type = Type::find($id);
        return response()->json($type);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);

        $type = Type::find($id);
        $type->update($request->all());

        return response()->json(['success' => 'Type updated successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Cari data tipe berdasarkan ID
        $type = Type::findOrFail($id);

        // Hapus data tipe
        $type->delete();

        // Return response sukses
        return response()->json(['success' => 'Type deleted successfully!']);
    }
}
