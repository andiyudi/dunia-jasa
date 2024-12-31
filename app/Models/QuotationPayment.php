<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationPayment extends Model
{
    protected $fillable = [
        'tender_id',
        'partner_id',
        'terms_of_payment',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class, 'tender_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }
}
