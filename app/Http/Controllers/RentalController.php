<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rental;

class RentalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $overlappingRental = Rental::where('item_id', $request->item_id)
            ->where(function ($query) use ($request) {

            $query->whereBetween('start_date', [
                $request->start_date,
                $request->end_date
            ])

            ->orWhereBetween('end_date', [
                $request->start_date,
                $request->end_date
            ])

            ->orWhere(function ($query) use ($request) {
                $query->where('start_date', '<=', $request->start_date)
                        ->where('end_date', '>=', $request->end_date);
            });
        })
        ->exists();

        if ($overlappingRental) {
            return response()->json([
                'message' => 'Item is already rented for selected dates.'
            ], 409);
        }

        $rental = Rental::create([
            'item_id' => $request->item_id,
            'renter_id' => Auth::id(),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'active',
        ]);

        return $rental;
    }
}
