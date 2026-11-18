<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:80'], 'parent_id' => ['nullable', 'exists:categories,id'], 'sort_order' => ['nullable', 'integer']]);
        $data['slug'] = Str::slug($data['name']);

        return response()->json(Category::create($data), 201);
    }

    public function update(Request $request, int $id)
    {
        $c = Category::findOrFail($id);
        $data = $request->validate(['name' => ['sometimes', 'string', 'max:80'], 'parent_id' => ['nullable', 'exists:categories,id'], 'sort_order' => ['nullable', 'integer']]);
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $c->update($data);

        return $c;
    }

    public function destroy(int $id)
    {
        $c = Category::findOrFail($id);
        abort_if($c->children()->exists() || \App\Models\Product::where('category_id', $id)->exists(), 422, 'Kategori masih dipakai.');
        $c->delete();

        return response()->noContent();
    }
}
