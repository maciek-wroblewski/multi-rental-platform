<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ItemController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['index', 'show'])
        ];
    }

    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Item::with(['owner', 'category']);

        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('location') && $request->location != '') {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $items = $query->latest()->paginate(12);

        return view('items.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_day' => 'required|numeric|min:0|max:999999.99',
            'location' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $validated['owner_id'] = Auth::id();
        $validated['status'] = 'available';

        Item::create($validated);

        return redirect()->route('items.index')->with('success', 'Przedmiot został pomyślnie dodany do wypożyczalni.');
    }


    public function show(Item $item)
    {
        $item->load(['owner', 'category', 'reviews.user']);
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        if ($item->owner_id !== Auth::id()) {
            abort(403, 'Nie masz uprawnień do edycji tego przedmiotu.');
        }

        $categories = Category::all();
        return view('items.edit', compact('item', 'categories'));
    }


    public function update(Request $request, Item $item)
    {

        if ($item->owner_id !== Auth::id()) {
            abort(403, 'Nie masz uprawnień do modyfikacji tego przedmiotu.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_day' => 'required|numeric|min:0|max:999999.99',
            'location' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:available,rented',
        ]);

        $item->update($validated);

        return redirect()->route('items.show', $item->id)->with('success', 'Dane przedmiotu zostały zaktualizowane.');
    }

    public function destroy(Item $item)
    {
        if ($item->owner_id !== Auth::id()) {
            abort(403, 'Nie masz uprawnień do usunięcia tego przedmiotu.');
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Przedmiot został usunięty z wypożyczalni.');
    }
}
