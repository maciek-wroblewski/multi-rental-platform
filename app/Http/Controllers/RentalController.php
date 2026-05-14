<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rental;
use App\Models\Item;

class RentalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $item = Item::findORFail($request->item_id);

        if ($item->owner_id == Auth::id()) {
            return response()->json([
                'message' => 'You cannot rent your own item.'
            ], 403);
        }

        $overlappingRental = Rental::where('item_id', $request->item_id)->where('status', 'active')
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

    public function returnRetnal(Rental $rental)
    {
        if ($rental->renter_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized.'
            ], 403);
        }

        $rental->status = 'returned';

        $rental->save();

        return response()->json([
            'message' => 'Rental returned succesfully.'
        ]);
    }

    public function cancelRental(Rental $rental)
    {
        if($rental->renter_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized.'
            ], 403);
        }

        if($rental->status !== 'active') {
            return response()->json([
                'message' => 'Only rentals that are active can be cancelled.'
            ], 400);
        }

        $rental->status = 'cancelled';

        $rental->save();

        return response()->json([
            'message' => 'Rental cancelled successfully.'
        ]);
    }
}
