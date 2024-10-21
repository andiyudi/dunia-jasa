<?php

namespace App\Models;

use App\Models\User;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'npwp',
        'is_verified',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function brands()
    {
        return $this->hasMany(Brand::class);
    }

    public function files()
    {
        return $this->hasMany(File::class, 'partner_id');
    }

    public function tenders()
    {
        return $this->hasMany(Tender::class, 'partner_id');
    }
}
