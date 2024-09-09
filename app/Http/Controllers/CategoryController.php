<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua kategori dari database
        // $categories = Category::all();

        // Kirim data kategori ke view 'dashboard'
        return view('category.index');
    }

    public function getData()
    {
        // Ambil semua data kategori dari database
        $categories = Category::select(['id','name']);

        // Return data dalam format DataTables
        return DataTables::of($categories)
            ->addColumn('action', function($row){
                return "<button data-id='$row->id' class='btn btn-sm btn-warning edit-category'>Edit</button>
                        <button data-id='$row->id' class='btn btn-sm btn-danger delete-category'>Delete</button>";
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
        ]);

        // Simpan data kategori baru
        Category::create([
            'name' => $request->name,
        ]);

        // Return response sukses
        return response()->json(['success' => 'Category created successfully!']);
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
    public function edit($id)
    {
        $category = Category::find($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::find($id);
        $category->update($request->all());

        return response()->json(['success' => 'Category updated successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Cari data kategori berdasarkan ID
        $category = Category::findOrFail($id);

        // Hapus data kategori
        $category->delete();

        // Return response sukses
        return response()->json(['success' => 'Category deleted successfully!']);
    }

}
