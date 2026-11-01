<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('category', 'recipe')
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->q, fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->when($request->category, fn ($q, $c) => $q->whereHas('category', fn ($cq) => $cq->where('slug', $c)->orWhere('name', $c)))
            ->orderBy('name')
            ->paginate(24)
            ->withQueryString();

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.form', [
            'product' => new Product(['is_active' => true, 'is_available' => true, 'type' => 'food']),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        unset($data['image']);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }
        $product = Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produit créé: '.$product->name);
    }

    public function edit(Product $product): View
    {
        return view('products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request);
        unset($data['image']);
        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }
        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produit mis à jour.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produit supprimé.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50'],
            'type' => ['required', 'in:food,drink'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'track_stock' => ['sometimes', 'boolean'],
            'stock_qty' => ['nullable', 'numeric', 'min:0'],
            'stock_alert' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
            'is_available' => ['sometimes', 'boolean'],
        ]) + [
            'track_stock' => $request->boolean('track_stock'),
            'is_active' => $request->boolean('is_active', true),
            'is_available' => $request->boolean('is_available', true),
            'cost_price' => $request->input('cost_price', 0),
            'stock_qty' => $request->input('stock_qty', 0),
            'stock_alert' => $request->input('stock_alert', 5),
            'unit' => $request->input('unit', 'unit'),
        ];
    }
}
