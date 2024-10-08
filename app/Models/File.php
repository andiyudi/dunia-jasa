<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'type_id',
        'name',
        'path',
        'note',
    ];

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }
}
