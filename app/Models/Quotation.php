<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    /** @use HasFactory<\Database\Factories\QuotationFactory> */
    use HasFactory;
    protected $fillable = [
        'tender_item_id',
        'partner_user_id',
        'price',
        'total_price',
        'delivery_time',
        'terms_price',
        'remark',
    ];

    public function tender_item()
    {
        return $this->belongsTo(TenderItem::class, 'tender_item_id');
    }

    public function partnerUser()
    {
        return $this->belongsTo(PartnerUser::class, 'partner_user_id');
    }
}
