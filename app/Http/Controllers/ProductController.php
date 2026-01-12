<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Traits\GeneralTrait;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    use GeneralTrait;
    public function create()
    {
        $categories = Category::all(); // T_Shirt, Dress...
        $subcategories = SubCategory::all(); // men, women, kids
        return view('products.create', compact('categories', 'subcategories'));
    }


    public function store(Request $request)
{
    $request->validate([
        'category_id' => 'required|exists:categories,id',
        'subcategory_id' => 'required|exists:sub_categories,id',
        'name' => 'required|string',
        'color' => 'required|string',
        'size' => 'required|string',
        'description' => 'required|string',
        'image' => 'required|image',
        'price' => 'required|numeric',
        'quantity' => 'required|integer',
    ]);

    $imageName = time().'.'.$request->image->extension();
    $request->image->move(public_path('uploads'), $imageName);

    Product::create([
        'category_id' => $request->category_id,
        'subcategory_id' => $request->subcategory_id,
        'name' => $request->name,
        'color' => $request->color,
        'size' => $request->size,
        'description' => $request->description,
        'image' => 'uploads/' . $imageName,
        'price' => $request->price,
        'quantity' => $request->quantity,
    ]);

    return redirect()->back()->with('success', 'تم إضافة المنتج بنجاح!');
}

public function index()
{
    $userId = auth()->id();

    $products = Product::with(['category', 'subcategory'])
        ->where('quantity', '>=', 1)
        ->get(['id', 'name','image', 'price', 'category_id', 'subcategory_id']) // تحديد الحقول
        ->map(function ($product) use ($userId) {
            // فحص إذا المنتج مفضل لدى المستخدم
            $isFavorite = DB::table('favorites')
                ->where('user_id', $userId)
                ->where('product_id', $product->id)
                ->exists();

            // إرجاع البيانات المطلوبة + حالة المفضلة
            return [
                'id' => $product->id,
                'name' => $product->name,
               
                'image' => $product->image,
                'price' => $product->price,
                'category_id' => $product->category_id,
                'subcategory_id' => $product->subcategory_id,
                'is_favorite' => $isFavorite, // الحالة الجديدة
            ];
        });

    return $this->returnData('products', $products, 'تم جلب المنتجات بنجاح');
}



////////////////show products
public function indexProduct(Request $request)
{
    $categoryId = $request->input('category_id');
    $subcategoryId = $request->input('subcategory_id');

    $query = Product::with(['category', 'subcategory'])
        ->where('quantity', '>=', 1); // فقط المنتجات اللي إلها كمية إجمالية موجبة

    if ($categoryId) {
        $query->where('category_id', $categoryId);
    }

    if ($subcategoryId) {
        $query->where('subcategory_id', $subcategoryId);
    }

    $products = $query->get([
        'id', 'name', 'price', 'image','category_id', 'subcategory_id'
    ]);

    $userId = auth()->id();

    $products->transform(function ($product) use ($userId) {
        $product->is_favorite = $product->favoritedBy()->where('user_id', $userId)->exists();
        return $product;
    });

    return $this->returnData('products', $products, 'تم جلب المنتجات بنجاح');
}







public function search(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized. Please login first.',
        ], 401);
    }

    $query = Product::query();

    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    if ($request->filled('subcategory_id')) {
        $query->where('subcategory_id', $request->subcategory_id);
    }

    $products = $query->paginate(10);

    // للحصول على كل المفضلات دفعة واحدة بدل فحص كل منتج لحاله
    $favoriteIds = $user->favorites()->pluck('product_id')->toArray();

    $formatted = $products->getCollection()->transform(function ($product) use ($favoriteIds) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'image' => $product->image?? null,
            'is_favorite' => in_array($product->id, $favoriteIds),
        ];
    });

    return response()->json([
        'status' => true,
        'message' => 'Products fetched successfully.',
        'data' => [
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'total' => $products->total(),
            'products' => $formatted,
        ]
    ]);
}

public function show($id)
{
    $userId = auth()->id(); // المستخدم الحالي

    // جلب المنتج مع العلاقات
    $product = Product::with([
        'category',
        'subcategory',
        'variants.color',
        'variants.size'
    ])
    ->where('id', $id)
    ->whereHas('variants', function ($query) {
        $query->where('stock_quantity', '>', 0);
    })
    ->first();

    if (!$product) {
        return response()->json([
            'status' => false,
            'message' => 'المنتج غير موجود أو غير متوفر',
        ], 404);
    }

    // فحص إذا المنتج مفضل لدى المستخدم
    $isFavorite = DB::table('favorites')
        ->where('user_id', $userId)
        ->where('product_id', $product->id)
        ->exists();

    // تحضير قائمة المتغيرات (variants)
    $variants = $product->variants->map(function ($variant) {
        return [
            'variant_id' => $variant->id,
            'color_id' => $variant->color->id,
            'color_name' => $variant->color->name,
            'size_id' => $variant->size->id,
            'size_name' => $variant->size->name,
            'stock_quantity' => $variant->stock_quantity,
        ];
    });

    // تحضير الرد النهائي
    $data = [
        'id' => $product->id,
        'name' => $product->name,
        'description' => $product->description,
        'image' => $product->image,
        'price' => $product->price,
        'quantity' => $product->quantity,
        'category_id' => $product->category_id,
        'subcategory_id' => $product->subcategory_id,
        'is_favorite' => $isFavorite,
        'variants' => $variants,
    ];

    return $this->returnData('product', $data, 'تم جلب تفاصيل المنتج بنجاح');
}

///عرض المنتجات المشابهة////





    public function getSimilarProducts($id)
    {
        // جيب المنتج الحالي
        $product = Product::findOrFail($id);

        // جيب المنتج10.168.78.58ات المشابهة واستثني المنتج الحالي
        $similarProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->select('id', 'name', 'image', 'price','category_id','subcategory_id') // لتخفيف البيانات
            ->get();

        return response()->json([
            'product' => $product,
            'similar_products' => $similarProducts
        ]);
    }
}



