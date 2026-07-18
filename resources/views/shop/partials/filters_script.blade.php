<script>
    $(function() {
        document.getElementById('sortby').addEventListener('change', function() {
            var url = new URL(window.location.href);
            url.searchParams.set('sort', this.value);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        });

        if (typeof noUiSlider === 'object') {
            var priceSlider = document.getElementById('price-slider');

            if (priceSlider && priceSlider.noUiSlider) {
                priceSlider.noUiSlider.updateOptions({
                    start: [{{ (int) ($minPrice ?: 10) }}, {{ (int) ($maxPrice ?: 100000) }}],
                    range: {
                        min: 10,
                        max: 100000
                    },
                    step: 100,
                    margin: 100,
                    format: wNumb({
                        decimals: 0,
                        prefix: '₹'
                    })
                });

                priceSlider.noUiSlider.on('change', function(values) {
                    var min = parseInt(values[0], 10);
                    var max = parseInt(values[1], 10);

                    if (!isNaN(min)) {
                        document.getElementById('min_price').value = min;
                    }

                    if (!isNaN(max)) {
                        document.getElementById('max_price').value = max;
                    }
                });
            }
        }
    });
</script>
