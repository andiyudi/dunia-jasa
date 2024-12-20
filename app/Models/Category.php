<?php

namespace App\Models;

use App\Models\Tender;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function partners()
    {
        return $this->belongsToMany(Partner::class)->withTimestamps();
    }

    public function tenders()
    {
        return $this->hasMany(Tender::class);
    }
}
