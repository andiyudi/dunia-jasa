<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // // Ambil semua kategori dari database
        // $categories = Category::all();
        $user = Auth::user();
        $partnerCount = $user->partners()->count(); // Hitung jumlah partner yang dimiliki user
          // Hitung jumlah tender terkait user melalui partner_user_id
        $tenderCount = \DB::table('tenders')
        ->join('partner_user', 'tenders.partner_user_id', '=', 'partner_user.id')
        ->where('partner_user.user_id', $user->id)
        ->count();
        // Hitung jumlah submit (quotations) yang terkait tender yang sama, berdasarkan partner_user_id dan tender_id
        $quotationSubmitCount = DB::table('quotations')
        ->join('partner_user', 'quotations.partner_user_id', '=', 'partner_user.id')
        ->join('tender_items', 'quotations.tender_item_id', '=', 'tender_items.id')  // Menggabungkan dengan tender_items
        ->join('tenders', 'tender_items.tender_id', '=', 'tenders.id')  // Menggabungkan dengan tenders untuk mendapatkan tender_id
        ->where('partner_user.user_id', $user->id)
        ->groupBy('tenders.id')  // Mengelompokkan berdasarkan tender_id
        ->count();
        return view('dashboard', compact('partnerCount', 'tenderCount', 'quotationSubmitCount'));
        // Kirim data kategori ke view 'dashboard'
        // return view('dashboard');
    }

    // public function refreshCategories()
    // {
    //     $categories = Category::all();
    //     return response()->json($categories);
    // }


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
        //
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
