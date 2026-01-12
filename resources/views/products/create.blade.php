<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>إضافة منتج</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f6fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            background: white;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #2f3640;
        }

        label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #dcdde1;
            border-radius: 5px;
            font-size: 14px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #00a8ff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #0097e6;
        }

        .success-message {
            color: green;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>إضافة منتج جديد</h1>

        @if(session('success'))
            <p class="success-message">{{ session('success') }}</p>
        @endif

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <label>Category:</label>
            <select name="category_id" required>
                <option value="">اختر الفئة</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select><br>

            <label>SubCategory (Gender):</label>
            <select name="subcategory_id" required>
                <option value="">اختر النوع</option>
                @foreach($subcategories as $subcategory)
                    <option value="{{ $subcategory->id }}">{{ $subcategory->gender }}</option>
                @endforeach
            </select><br>


            <label>اسم المنتج:</label>
            <input type="text" name="name" required>

            <label>اللون</label>
            <input type="text" name="color" required>
            <label> الحجم:</label>
            <input type="text" name="size" required>

            <label>الوصف:</label>
            <textarea name="description" required></textarea>

            <label>الصورة:</label>
            <input type="file" name="image" accept="image/*" required>

            <label>السعر:</label>
            <input type="number" step="0.01" name="price" required>

            <label>الكمية:</label>
            <input type="number" name="quantity" required>

            <button type="submit">إضافة المنتج</button>
        </form>
    </div>

</body>

</html>
