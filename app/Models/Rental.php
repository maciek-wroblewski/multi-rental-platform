<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Item;
use App\Models\User;
use Illuminate\Testing\Fluent\Concerns\Has;

class Rental extends Model
{
    use HasFactory;
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
