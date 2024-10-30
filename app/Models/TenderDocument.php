<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderDocument extends Model
{
    protected $fillable = [
        'tender_id',
        'type_id',
        'name',
        'path',
        'note',
    ];

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function tender()
    {
        return $this->belongsTo(Tender::class, 'tender_id');
    }
}
