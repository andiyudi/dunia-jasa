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
}
