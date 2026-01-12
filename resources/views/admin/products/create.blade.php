@extends('layouts.app')

@section('title', 'إضافة منتج جديد')

@section('content')
    <h2>إضافة منتج جديد</h2>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>اسم المنتج</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>الوصف</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label>الصورة</label>
            <input type="file" name="image" class="form-control" accept="image/*" required>
        </div>

        <div class="mb-3">
            <label>السعر</label>
            <input type="number" name="price" step="0.01" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>القسم الرئيسي</label>
            <select name="category_id" class="form-control" required>
                <option value="">-- اختر القسم --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>الفئة (رجال / نساء / أطفال)</label>
            <select name="subcategory_id" class="form-control" required>
                <option value="">-- اختر الفئة --</option>
                @foreach($subcategories as $sub)
                    <option value="{{ $sub->id }}">{{ $sub->gender }}</option>
                @endforeach
            </select>
        </div>

        <h4>التركيبات (لون - مقاس - كمية)</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>اللون</th>
                    <th>المقاس</th>
                    <th>الكمية</th>
                    <th>حذف</th>
                </tr>
            </thead>
            <tbody id="variants_table_body">
                <tr>
                    <td>
                        <select name="variants[0][color_id]" class="form-control" required>
                            <option value="">اختر اللون</option>
                            @foreach($colors as $color)
                                <option value="{{ $color->id }}">{{ $color->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="variants[0][size_id]" class="form-control" required>
                            <option value="">اختر المقاس</option>
                            @foreach($sizes as $size)
                                <option value="{{ $size->id }}">{{ $size->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="variants[0][stock_quantity]" class="form-control" min="0" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row">حذف</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="button" id="add_variant" class="btn btn-secondary mb-3">إضافة تركيبة جديدة</button>

        <button type="submit" class="btn btn-success">إضافة</button>
    </form>

    <script>
        let variantIndex = 1;

        document.getElementById('add_variant').addEventListener('click', function() {
            const tbody = document.getElementById('variants_table_body');
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
                <td>
                    <select name="variants[${variantIndex}][color_id]" class="form-control" required>
                        <option value="">اختر اللون</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="variants[${variantIndex}][size_id]" class="form-control" required>
                        <option value="">اختر المقاس</option>
                        @foreach($sizes as $size)
                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="variants[${variantIndex}][stock_quantity]" class="form-control" min="0" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">حذف</button>
                </td>
            `;

            tbody.appendChild(newRow);
            variantIndex++;

            // إضافة حدث حذف الصف
            newRow.querySelector('.remove-row').addEventListener('click', function() {
                this.closest('tr').remove();
            });
        });

        // حذف الصف الموجود افتراضياً إذا ضغط حذف
        document.querySelectorAll('.remove-row').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('tr').remove();
            });
        });
    </script>
@endsection
