<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\TenderItem;

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
            $data = Tender::with(['partner', 'category', 'documents.type'])
                ->select('tenders.*', 'partner_user.user_id as creator_id')
                ->leftJoin('partner_user', 'tenders.partner_user_id', '=', 'partner_user.id')
                ->where('partner_user.user_id', '!=', $currentUserId) // Exclude tenders created by the current user
                ->latest()
                ->get();

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
                    return view('tender.quotation.action', compact('route', 'data'));
                })
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
        dd($request->all());
        // Validate the form inputs
        $validatedData = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'item_id' => 'required|exists:tender_items,id',
            'price' => 'required|numeric|min:0',
            'delivery_time' => 'required|string',
            'remark' => 'nullable|string',
        ]);

        // Find the tender item to get the quantity
        $tenderItem = TenderItem::findOrFail($validatedData['item_id']);

        // Calculate total price
        $totalPrice = $validatedData['price'] * $tenderItem->quantity;

        // Create the quotation
        $quotation = Quotation::create([
            'tender_item_id' => $validatedData['item_id'],
            'partner_user_id' => $validatedData['partner_id'],
            'price' => $validatedData['price'],
            'total_price' => $totalPrice,
            'delivery_time' => $validatedData['delivery_time'],
            'remark' => $validatedData['remark'],
        ]);

        return redirect()->back()->with('success', 'Quotation submitted successfully.');
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
