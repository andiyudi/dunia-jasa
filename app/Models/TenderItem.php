<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderItem extends Model
{
    protected $fillable = [
        'tender_id',
        'description',
        'specification',
        'quantity',
        'satuan',
        'unit',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }
}