<?php
require '../base.php';

// 获取分页参数
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 8;
$offset = ($page - 1) * $items_per_page;

// 获取筛选条件
$min = $_GET['min'] ?? 0;
$max = $_GET['max'] ?? 99999;
$categories = $_GET['category'] ?? [];
$brands     = $_GET['brand'] ?? [];
$colors     = $_GET['color'] ?? [];
$features   = $_GET['feature'] ?? [];

// 构建查询语句
$query = "
    SELECT DISTINCT p.*, 
        (SELECT pi.image_url 
         FROM productimages pi 
         WHERE pi.product_id = p.product_id 
         ORDER BY pi.image_id ASC LIMIT 1) AS image_url
    FROM products p
    LEFT JOIN product_colours pc ON p.product_id = pc.product_id
    LEFT JOIN colors c ON pc.color_id = c.color_id
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    LEFT JOIN categories cat ON p.category_id = cat.category_id
    LEFT JOIN productfeatures pf ON p.product_id = pf.product_id
    LEFT JOIN features f ON pf.feature_id = f.feature_id
    WHERE p.price BETWEEN ? AND ?
";
$params = [$min, $max];

// ✅ 筛选条件拼接
if (!empty($categories)) {
    $query .= " AND cat.category_name IN (" . implode(',', array_fill(0, count($categories), '?')) . ")";
    $params = array_merge($params, $categories);
}
if (!empty($brands)) {
    $query .= " AND b.brand_name IN (" . implode(',', array_fill(0, count($brands), '?')) . ")";
    $params = array_merge($params, $brands);
}
if (!empty($features)) {
    $query .= " AND f.feature_name IN (" . implode(',', array_fill(0, count($features), '?')) . ")";
    $params = array_merge($params, $features);
}
if (!empty($colors)) {
    $query .= " AND c.color_name IN (" . implode(',', array_fill(0, count($colors), '?')) . ")";
    $params = array_merge($params, $colors);
}

$query .= " GROUP BY p.product_id";

// ✅ 获取总记录数
$count_query = "SELECT COUNT(*) FROM ($query) AS total";
$count_stmt = $db->prepare($count_query);
$count_stmt->execute($params);
$total_products = $count_stmt->fetchColumn();
$total_pages = ceil($total_products / $items_per_page);

// ✅ 添加分页
$query .= " LIMIT $items_per_page OFFSET $offset";
$stmt = $db->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- ✅ 包裹整个区域（结构统一） -->
<div class="ajax-product-wrapper">
    <div class="product-list">
        <?php foreach ($products as $product): ?>
            <div class="product-item">
                <a href="product_detail.php?product_id=<?= $product['product_id'] ?>" class="product-link">
                    <div class="product-image">
                        <img src="<?= htmlspecialchars($product['image_url'] ?? 'image/default.jpg') ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    </div>
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                    <p class="product-price">RM <?= number_format($product['price'], 2) ?></p>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- ✅ 分页 -->
    <div class="pagination-wrapper">
        <div class="pagination">
            <?php if ($total_pages > 1): ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="#" class="pagination-link <?= ($i == $page) ? 'active' : '' ?>" data-page="<?= $i ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            <?php else: ?>
                <span style="visibility: hidden;">1</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- ✅ 空层撑高高度（解决按钮浮上来） -->
    <div class="pagination-spacer"></div>
</div>
