<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\SubCategory;
use App\Models\Color;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $variants = ProductVariant::with(['product.category', 'color', 'size'])->paginate(10);
        return view('admin.products.index', compact('variants'));
    }
    public function create()
    {
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $colors = Color::all();
        $sizes = ProductSize::all();

        return view('admin.products.create', compact('categories', 'subcategories', 'colors', 'sizes'));
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'image' => 'required|image|max:2048',
        'price' => 'required|numeric|min:0',
        'category_id' => 'required|exists:categories,id',
        'subcategory_id' => 'required|exists:sub_categories,id',
        'variants' => 'required|array|min:1',
        'variants.*.color_id' => 'required|exists:colors,id',
        'variants.*.size_id' => 'required|exists:sizes,id',
        'variants.*.stock_quantity' => 'required|integer|min:0', // <-- هنا التعديل
    ]);

    $imagePath = $request->file('image')->store('products', 'public');

    $product = Product::create([
        'name' => $request->name,
        'description' => $request->description,
        'image' => $imagePath,
        'price' => $request->price,
        'category_id' => $request->category_id,
        'subcategory_id' => $request->subcategory_id,
        'quantity' => 0,
    ]);

    $totalQuantity = 0;

    foreach ($request->variants as $variant) {
        $product->variants()->create([
            'color_id' => $variant['color_id'],
            'size_id' => $variant['size_id'],
            'stock_quantity' => $variant['stock_quantity'], // <-- تعديل هنا
        ]);
        $totalQuantity += $variant['stock_quantity']; // <-- تعديل هنا
    }

    // ✅ مراقبة القيمة
     //dd($totalQuantity);

    // تحديث الكمية
    $product->update(['quantity' => $totalQuantity]);

    return redirect()->route('admin.products.index')->with('success', 'تم إضافة المنتج بنجاح.');
}


    

    public function edit(Product $product)
    {
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $colors = Color::all();
        $sizes = ProductSize::all();

        $product->load('variants.color', 'variants.size');

        return view('admin.products.edit', compact('product', 'categories', 'subcategories', 'colors', 'sizes'));
    }

    public function update(Request $request, Product $product)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'image' => 'nullable|image|max:2048',
        'price' => 'required|numeric|min:0',
        'category_id' => 'required|exists:categories,id',
        'subcategory_id' => 'required|exists:sub_categories,id',
        'variants' => 'required|array',
        'variants.*.color_id' => 'required|exists:colors,id',
        'variants.*.size_id' => 'required|exists:sizes,id',
        'variants.*.stock_quantity' => 'required|integer|min:0',
    ]);

    // تحديث الصورة إن وجدت
    if ($request->hasFile('image')) {
        Storage::disk('public')->delete($product->image);
        $product->image = $request->file('image')->store('products', 'public');
    }

    // تحديث بيانات المنتج
    $product->update([
        'name' => $request->name,
        'description' => $request->description,
        'image' => $product->image,
        'price' => $request->price,
        'category_id' => $request->category_id,
        'subcategory_id' => $request->subcategory_id,
    ]);

    // تحديث كل variant بشكل مستقل
    $totalQuantity = 0;
    foreach ($request->variants as $variantId => $data) {
        $variant = $product->variants()->find($variantId);
        if ($variant) {
            $variant->update([
                'color_id' => $data['color_id'],
                'size_id' => $data['size_id'],
                'stock_quantity' => $data['stock_quantity'],
            ]);
            $totalQuantity += $data['stock_quantity'];
        }
    }

    // تحديث الكمية الإجمالية للمنتج
    $product->update(['quantity' => $totalQuantity]);

    return redirect()->route('admin.products.index')->with('success', 'تم تحديث المنتج بنجاح.');
}


    public function destroy(Product $product)
    {
        $product->variants()->delete();
        Storage::disk('public')->delete($product->image);
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'تم حذف المنتج.');
    }
}
