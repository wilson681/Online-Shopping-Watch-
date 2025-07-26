<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter</title>
    <link rel="stylesheet" href="../css/filter.css"> <!-- ✅ 连接 CSS -->
    <script src="filter.js" defer></script> <!-- ✅ 连接 JS -->
</head>
<body>

<div class="filter-container">
    <h2>Filter</h2>

    <!-- Category Filter -->
    <div class="filter-section">
        <div class="filter-header" onclick="toggleFilter(this)">
            Category <span class="toggle-icon">▸</span>
        </div>
        <div class="filter-content">
            <label><input type="checkbox" name="category" value="Chronograph">Chronograph</label>
            <label><input type="checkbox" name="category" value="Digital">Digital</label>
        </div>
    </div>

    <!-- Brand Filter -->
    <div class="filter-section">
        <div class="filter-header" onclick="toggleFilter(this)">
            Brand <span class="toggle-icon">▸</span>
        </div>
        <div class="filter-content">
            <label><input type="checkbox" name="brand" value="Danielwellington">Daniel Wellington</label>
            <label><input type="checkbox" name="brand" value="Gshock">G-shock</label>
            <label><input type="checkbox" name="brand" value="Apple">Apple</label>
        </div>
    </div>

    <!-- Colour Filter -->
    <div class="filter-section">
        <div class="filter-header" onclick="toggleFilter(this)">
            Colour <span class="toggle-icon">▸</span>
        </div>
        <div class="filter-content color-filter">
            <label class="color-option"><input type="checkbox" name="color" value="Black"><span style="background: black;"></span> Black</label>
            <label class="color-option"><input type="checkbox" name="color" value="Silver"><span style="background: silver;"></span> Silver</label>
            <label class="color-option"><input type="checkbox" name="color" value="Blue"><span style="background: blue;"></span> Blue</label>
            <label class="color-option"><input type="checkbox" name="color" value="Green"><span style="background: green;"></span> Green</label>
            <label class="color-option"><input type="checkbox" name="color" value="White"><span style="background: white;"></span> White</label>
        </div>
    </div>

    <!-- Features Filter -->
    <div class="filter-section">
        <div class="filter-header" onclick="toggleFilter(this)">
            Features <span class="toggle-icon">▸</span>
        </div>
        <div class="filter-content">
            <label><input type="checkbox" name="feature" value="Waterproof">Waterproof</label>
            <label><input type="checkbox" name="feature" value="Solar">Solar</label>
            <label><input type="checkbox" name="feature" value="Bluetooth">Bluetooth</label>
        </div>
    </div>

<!-- ✅ Price Range Filter Section -->
<div class="filter-section price-filter">
    <div class="filter-header">Price Range <span class="toggle-icon">▸</span></div>
    
    <div class="filter-content">
        
        <!-- 🔢 输入框 -->
        <div class="price-inputs">
            <input type="number" id="minPriceInput" value="0" min="0" max="1000">
            <input type="number" id="maxPriceInput" value="1000" min="0" max="1000">
        </div>

        <!-- 🎚️ 滑块 -->
        <div class="range-slider">
            <div class="range-track"></div> <!-- ✅ 轨道应该写在两个滑块下面更直观 -->
            <input type="range" id="minPriceRange" min="0" max="1000" value="0" step="1">
            <input type="range" id="maxPriceRange" min="0" max="1000" value="1000" step="1">
        </div>
        
    </div>
</div>




</div>

</body>
</html>
