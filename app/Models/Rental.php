<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    protected $fillable = [
        'item_id',
        'renter_id',
        'start_date',
        'end_date',
        'status',
    ];

    public function item() 
    {
        return $this->belongsTo(Item::class);
    }

    public function renter()
    {
        return $this->belongsTo(User::class, 'renter_id');
    }
}
