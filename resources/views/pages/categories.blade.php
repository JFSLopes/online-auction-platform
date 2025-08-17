<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/categories.css') }}">
    <script type="text/javascript" src={{ url('js/adminCategory.js') }} defer></script>
    <title>{{ config('app.name', 'Admin Page - Category List') }}</title>
</head>
<body>
    <main class="container">
        <button class="go-back" onclick="window.history.back()">Go Back</button>
        <section class="categories">
            @foreach($subCategoriesByCategory as $categoryName => $subcategories)
                <div class="category-group">

                    @php
                        $baseDirectory = public_path('images/category/');
                        $imageFiles = glob($baseDirectory . $subcategories[0]->catid . '.*');
                        $imageUrl = !empty($imageFiles) 
                            ? asset('images/category/' . basename($imageFiles[0]))
                            : asset('images/category/default-category.png');  
                    @endphp

                    <form action="{{route('deleteCategory',['adminId' => Auth::id()])}}" method="POST">
                        @csrf
                        <div class="category-image-container">
                            <input type="hidden" name="catid" value="{{ $subcategories[0]->catid }}">
                            <img src="{{ $imageUrl }}" alt="Category Image" class="image-category">
                            <button type="submit" class="btn btn-delete" id="delete-category" data-target="{{ $subcategories[0]->catid }}">Delete <i class="fa-solid fa-trash"></i></button>
                        </div>
                    </form>

                    <h2 class="category-title">{{ $categoryName }}</h2>
                    <div class="subcategory-list">
                        @foreach($subcategories as $subcategory)
                            <div class="subcategory-card">
                                <div class="subcategory-icon">
                                    <i class="{{ $subcategory->icon }}"></i>
                                </div>
                                <form class="subcategory-info" action="{{route('deleteSubcategory',['adminId' => Auth::id()])}}" method="POST">
                                    @csrf
                                    <div class="subcategory-info">
                                        <div class="subcategory-name">{{ $subcategory->subcategoryname }}</div>
                                        <input type="hidden" name="subcatid" value="{{ $subcategory->subcatid }}">
                                        <div class="subcategory-actions">
                                            <button type="submit" class="btn btn-delete" id="delete-subcategory" data-target="{{ $subcategory->subcatid }}">Delete <i class="fa-solid fa-trash"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                        <form class="subcategory-added" action="{{route('addSubcategory',['adminId' => Auth::id()])}}" method="POST">
                            @csrf
                            <div class="subcategory-info">
                                <div class="subcategory-name">Add Subcategory</div>
                                <div class="subcategory-add-actions">
                                    <input type="text" class="input-category" name="subcategoryName" placeholder="Subcategory Name" required>
                                    <input type="hidden" name="catid" value="{{ $subcategories[0]->catid }}">
                                    <button type="submit" class="btn btn-add" data-target="{{ $subcategory->catid }}">Add <i class="fa-solid fa-plus"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

            <div class="category-group">
                <div class="subcategory-list">
                    <div class="category-added">
                        <form class="subcategory-info" action="{{route('addCategory',['adminId' => Auth::id()])}}" method="POST">
                            @csrf
                            <div class="subcategory-name">Create New Category</div>
                            <div class="subcategory-add-actions">
                                <input type="text" class="input-category" placeholder="Enter Category Name" name="categoryname"required>
                                <button type="submit" class="btn btn-add">Add <i class="fa-solid fa-plus"></i></button>
                            </div>
                        </form>  
                    </div>
                </div>
            </div>          
        </section>
    </main>
</body>
</html>