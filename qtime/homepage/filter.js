document.addEventListener("DOMContentLoaded", function () {
    // ✅ 折叠功能
    const filterHeaders = document.querySelectorAll(".filter-header");
    filterHeaders.forEach(header => {
        header.addEventListener("click", function () {
            const section = this.parentElement;
            section.classList.toggle("open");

            const content = section.querySelector(".filter-content");
            if (content.style.display === "flex" || content.style.display === "block") {
                content.style.display = "none";
            } else {
                content.style.display = section.classList.contains("price-filter") ? "block" : "flex";
                updatePriceTrack();
            }
        });
    });

    // ✅ DOM 元素
    const minSlider = document.getElementById("minPriceRange");
    const maxSlider = document.getElementById("maxPriceRange");
    const minInput = document.getElementById("minPriceInput");
    const maxInput = document.getElementById("maxPriceInput");
    const rangeTrack = document.querySelector(".range-track");

    if (minSlider && maxSlider && minInput && maxInput && rangeTrack) {
        const minGap = 10;
        const maxRange = parseInt(maxSlider.max);

        function updatePriceTrack() {
            const minPercent = (parseInt(minSlider.value) / maxRange) * 100;
            const maxPercent = (parseInt(maxSlider.value) / maxRange) * 100;
            rangeTrack.style.background = `linear-gradient(to right, #ddd ${minPercent}%, #000 ${minPercent}%, #000 ${maxPercent}%, #ddd ${maxPercent}%)`;
        }

        function syncFromSlider() {
            const minVal = parseInt(minSlider.value);
            const maxVal = parseInt(maxSlider.value);
            if (maxVal - minVal < minGap) {
                if (event.target === minSlider) {
                    minSlider.value = maxVal - minGap;
                } else {
                    maxSlider.value = minVal + minGap;
                }
            }
            minInput.value = minSlider.value;
            maxInput.value = maxSlider.value;
            updatePriceTrack();
        }

        function syncFromInput() {
            let minVal = parseInt(minInput.value);
            let maxVal = parseInt(maxInput.value);
            if (isNaN(minVal)) minVal = parseInt(minSlider.min);
            if (isNaN(maxVal)) maxVal = parseInt(maxSlider.max);
            if (maxVal - minVal < minGap) {
                if (event.target === minInput) {
                    minVal = maxVal - minGap;
                } else {
                    maxVal = minVal + minGap;
                }
            }
            minSlider.value = minVal;
            maxSlider.value = maxVal;
            updatePriceTrack();
        }

        function getCheckedValues(name) {
            return Array.from(document.querySelectorAll(`input[name="${name}"]:checked`)).map(cb => cb.value);
        }

        function updateProductList(page = 1) {
            const min = minInput.value;
            const max = maxInput.value;
            const colors = getCheckedValues("color");
            const brands = getCheckedValues("brand");
            const categories = getCheckedValues("category");
            const features = getCheckedValues("feature");

            const params = new URLSearchParams();
            params.append("min", min);
            params.append("max", max);
            params.append("page", page);
            colors.forEach(val => params.append("color[]", val));
            brands.forEach(val => params.append("brand[]", val));
            categories.forEach(val => params.append("category[]", val));
            features.forEach(val => params.append("feature[]", val));

            fetch(`filter_ajax.php?${params.toString()}`)
                .then(res => res.text())
                .then(data => {
                    document.querySelector(".product-list").innerHTML = data;

                    // ✅ 隐藏 PHP 分页
                    const phpPagination = document.querySelector(".php-pagination");
                    if (phpPagination) phpPagination.style.display = "none";

                    // ✅ 重新绑定分页按钮事件
                    document.querySelectorAll(".pagination-link").forEach(link => {
                        link.addEventListener("click", function (e) {
                            e.preventDefault();
                            const targetPage = this.dataset.page;
                            updateProductList(targetPage);
                        });
                    });
                });
        }

        // ✅ 滑动 & 输入更新触发
        minSlider.addEventListener("input", () => { syncFromSlider(); updateProductList(); });
        maxSlider.addEventListener("input", () => { syncFromSlider(); updateProductList(); });
        minInput.addEventListener("input", () => { syncFromInput(); updateProductList(); });
        maxInput.addEventListener("input", () => { syncFromInput(); updateProductList(); });

        // ✅ Checkbox 勾选触发
        document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
            cb.addEventListener("change", () => updateProductList());
        });

        // ✅ 初始化
        updatePriceTrack();
    }
});
