<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tender extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'estimation',
        'payment',
        'category_id',
        'partner_user_id',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function partner()
    {
        return $this->belongsToMany(Partner::class, 'partner_user', 'id', 'partner_id', 'partner_user_id');
    }

    public function items()
    {
        return $this->hasMany(TenderItem::class);
    }

    public function documents()
    {
        return $this->hasMany(TenderDocument::class, 'tender_id');
    }

    public function files()
    {
        return $this->hasMany(QuotationFiles::class, 'tender_id');
    }

    public function payments()
    {
        return $this->hasMany(QuotationPayment::class, 'tender_id');
    }
}
