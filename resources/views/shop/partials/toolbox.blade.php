<div class="toolbox">
    <div class="toolbox-left">
        <div class="toolbox-info">
            Showing <span>{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of
                {{ $products->total() }}</span> Products
        </div><!-- End .toolbox-info -->
    </div><!-- End .toolbox-left -->

    <div class="toolbox-right">
        <div class="toolbox-sort">
            <label for="sortby">Sort by:</label>
            <div class="select-custom">
                <select name="sort" id="sortby" class="form-control">
                    <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="price_low" {{ $sort === 'price_low' ? 'selected' : '' }}>Price: Low
                        to High</option>
                    <option value="price_high" {{ $sort === 'price_high' ? 'selected' : '' }}>Price:
                        High to Low</option>
                    <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Name: A to Z
                    </option>
                </select>
            </div>
        </div><!-- End .toolbox-sort -->
    </div><!-- End .toolbox-right -->
</div><!-- End .toolbox -->
