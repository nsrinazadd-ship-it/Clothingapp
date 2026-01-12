@extends('layouts.app')

@section('title', 'تعديل المنتج')

@section('content')
    <h2>تعديل المنتج</h2>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>اسم المنتج</label>
            <input type="text" name="name" value="{{ $product->name }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>الوصف</label>
            <textarea name="description" class="form-control" rows="4" required>{{ $product->description }}</textarea>
        </div>

        <div class="mb-3">
            <label>الصورة الحالية</label><br>
            <img src="{{ asset('storage/' . $product->image) }}" width="120">
        </div>

        <div class="mb-3">
            <label>تغيير الصورة</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <div class="mb-3">
            <label>السعر</label>
            <input type="number" step="0.01" name="price" value="{{ $product->price }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>التركيبات (لون × مقاس × كمية)</label>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>اللون</th>
                        <th>المقاس</th>
                        <th>الكمية</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->variants as $variant)
                        <tr>
                            <td>
                                <select name="variants[{{ $variant->id }}][color_id]" class="form-control" required>
                                    @foreach($colors as $color)
                                        <option value="{{ $color->id }}" @selected($variant->color_id == $color->id)>
                                            {{ $color->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="variants[{{ $variant->id }}][size_id]" class="form-control" required>
                                    @foreach($sizes as $size)
                                        <option value="{{ $size->id }}" @selected($variant->size_id == $size->id)>
                                            {{ $size->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number"
                                       name="variants[{{ $variant->id }}][stock_quantity]"
                                       value="{{ $variant->stock_quantity }}"
                                       class="form-control" required>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mb-3">
            <label>القسم الرئيسي</label>
            <select name="category_id" class="form-control" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected($product->category_id == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>الفئة (رجال / نساء / أطفال)</label>
            <select name="subcategory_id" class="form-control" required>
                @foreach($subcategories as $sub)
                    <option value="{{ $sub->id }}" @selected($product->subcategory_id == $sub->id)>
                        {{ $sub->gender }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
    </form>
@endsection
