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
    /**
     * ZABEZPIECZENIE: Konstruktor kontrolera.
     * Blokuje dostęp do tworzenia, edycji i usuwania dla niezalogowanych użytkowników.
     * Przeglądanie listy (index) oraz szczegółów (show) jest publiczne.
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * READ (Listing, wyszukiwarka i filtrowanie)
     */
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Item::with(['owner', 'category']);

        // Wyszukiwanie frazy po tytule lub opisie
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filtrowanie po kategorii
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        // Filtrowanie po lokalizacji
        if ($request->has('location') && $request->location != '') {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filtrowanie po statusie (dostępny/wypożyczony)
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Paginacja: 12 przedmiotów na stronę (wymóg optymalizacji bazy danych)
        $items = $query->latest()->paginate(12);

        return view('items.index', compact('items', 'categories'));
    }

    /**
     * CREATE (Wyświetlenie formularza dodawania)
     * Przekazuje tylko listę kategorii potrzebną do dropdowna.
     */
    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    /**
     * CREATE (Zapisanie nowego przedmiotu w bazie)
     */
    public function store(Request $request)
    {
        // Pełna walidacja danych wejściowych zgodnie ze strukturą bazy danych
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_day' => 'required|numeric|min:0|max:999999.99',
            'location' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        // Automatyczne przypisanie id zalogowanego użytkownika jako właściciela przedmiotu
        $validated['owner_id'] = Auth::id();
        $validated['status'] = 'available'; // Domyślny status nowego przedmiotu

        Item::create($validated);

        return redirect()->route('items.index')->with('success', 'Przedmiot został pomyślnie dodany do wypożyczalni.');
    }

    /**
     * READ (Wyświetlenie szczegółów jednego przedmiotu)
     * Automatycznie dociąga relacje opinii (reviews) i najmów (rentals), które robi Twój kolega.
     */
    public function show(Item $item)
    {
        $item->load(['owner', 'category', 'reviews.user']);
        return view('items.show', compact('item'));
    }

    /**
     * UPDATE (Wyświetlenie formularza edycji przedmiotu)
     */
    public function edit(Item $item)
    {
        // AUTORYZACJA: Sprawdzenie, czy edytujący to na pewno właściciel przedmiotu
        if ($item->owner_id !== Auth::id()) {
            abort(403, 'Nie masz uprawnień do edycji tego przedmiotu.');
        }

        $categories = Category::all();
        return view('items.edit', compact('item', 'categories'));
    }

    /**
     * UPDATE (Zapisanie zmienionych danych w bazie)
     */
    public function update(Request $request, Item $item)
    {
        // AUTORYZACJA: Sprawdzenie własności przedmiotu
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

    /**
     * DELETE (Usunięcie przedmiotu z systemu)
     */
    public function destroy(Item $item)
    {
        // AUTORYZACJA: Sprawdzenie własności przedmiotu
        if ($item->owner_id !== Auth::id()) {
            abort(403, 'Nie masz uprawnień do usunięcia tego przedmiotu.');
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Przedmiot został usunięty z wypożyczalni.');
    }
}