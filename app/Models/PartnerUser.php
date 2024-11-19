<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PartnerUser extends Pivot
{
    protected $table = 'partner_user';

    // Relasi ke model Partner
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
