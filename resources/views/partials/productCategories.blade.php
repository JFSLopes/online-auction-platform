<div id="search-categories">
    @isset($showNone)
    <div class="category-item" data-category-name="none">
        <div class="category-icon">
            <img src="/images/category/none.png">
        </div>
        <div class="category-name">
            <span class="category-name-">None</span>
        </div>
    </div>
    @foreach ($categories as $category)
        <div class="category-item" data-category-name="{{ $category->categoryname }}">
            <div class="category-icon">
                <img src="/images/category/{{$category->catid}}.png">
            </div>
            <div class="category-name">
                <span class="category-name-">{{ $category->categoryname}}</span>
            </div>
        </div>
    @endforeach
    @endisset
</div>