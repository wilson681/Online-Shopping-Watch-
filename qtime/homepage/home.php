<?php
require '../head.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Product List</title>
    <link rel="stylesheet" href="../css/product.css">
</head>

<body>
<div class="page-container">
    <div class="product-container">

        <!-- 🔥 左侧过滤栏 -->
        <aside class="filter-section">
            <?php include 'filter.php'; ?>
        </aside>

        <!-- ✅ 右侧产品 + 分页包裹区（⚠️结构固定） -->
        <div class="product-content-wrapper">

            <!-- 🛍️ 产品列表 -->
            <main class="product-list">
                <?php
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $limit = 8;
                $offset = ($page - 1) * $limit;
                $search = isset($_GET["search"]) ? trim($_GET["search"]) : "";

                // 获取总产品数量
                $countQuery = "
                    SELECT COUNT(DISTINCT p.product_id) AS total
                    FROM products p
                    LEFT JOIN product_colours pc ON p.product_id = pc.product_id
                    LEFT JOIN colors c ON pc.color_id = c.color_id
                    LEFT JOIN categories cat ON p.category_id = cat.category_id
                    LEFT JOIN brands b ON p.brand_id = b.brand_id
                    LEFT JOIN productfeatures pf ON p.product_id = pf.product_id
                    LEFT JOIN features f ON pf.feature_id = f.feature_id
                ";
                if (!empty($search)) {
                    $countQuery .= " WHERE 
                        p.name LIKE :search OR 
                        p.description LIKE :search OR 
                        cat.category_name LIKE :search OR
                        b.brand_name LIKE :search OR
                        c.color_name LIKE :search OR
                        f.feature_name LIKE :search";
                }
                $countStmt = $db->prepare($countQuery);
                if (!empty($search)) {
                    $countStmt->bindValue(":search", "%$search%", PDO::PARAM_STR);
                }
                $countStmt->execute();
                $totalProducts = $countStmt->fetchColumn();
                $totalPages = ceil($totalProducts / $limit);

                // 获取当前页产品
                $query = "
                    SELECT DISTINCT p.*, 
                        (SELECT pi.image_url 
                         FROM productimages pi 
                         WHERE pi.product_id = p.product_id 
                         ORDER BY pi.image_id ASC LIMIT 1) AS image_url
                    FROM products p
                    LEFT JOIN product_colours pc ON p.product_id = pc.product_id
                    LEFT JOIN colors c ON pc.color_id = c.color_id
                    LEFT JOIN categories cat ON p.category_id = cat.category_id
                    LEFT JOIN brands b ON p.brand_id = b.brand_id
                    LEFT JOIN productfeatures pf ON p.product_id = pf.product_id
                    LEFT JOIN features f ON pf.feature_id = f.feature_id
                ";
                if (!empty($search)) {
                    $query .= " WHERE 
                        p.name LIKE :search OR 
                        p.description LIKE :search OR 
                        cat.category_name LIKE :search OR
                        b.brand_name LIKE :search OR
                        c.color_name LIKE :search OR
                        f.feature_name LIKE :search";
                }
                $query .= " GROUP BY p.product_id LIMIT :limit OFFSET :offset";

                $stmt = $db->prepare($query);
                if (!empty($search)) {
                    $stmt->bindValue(":search", "%$search%", PDO::PARAM_STR);
                }
                $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
                $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
                $stmt->execute();
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($products) > 0):
                    foreach ($products as $product): ?>
                        <div class="product-item">
                            <a href="../homepage/product_detail.php?product_id=<?= $product['product_id'] ?>" class="product-link">
                                <div class="product-image">
                                    <img src="<?= !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'image/default.jpg' ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                </div>
                                <h3><?= htmlspecialchars($product['name']) ?></h3>
                                <p><?= htmlspecialchars($product['description']) ?></p>
                                <p class="product-price">RM <?= number_format($product['price'], 2) ?></p>
                            </a>
                        </div>
                    <?php endforeach;
                else: ?>
                    <p class="no-results">No products found.</p>
                <?php endif; ?>
            </main>

            <!-- ✅ 分页按钮 -->
            <div class="pagination-wrapper">
                <?php if ($totalPages > 1): ?>
                    <div class="pagination php-pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="<?= $i == $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php else: ?>
                    <!-- 保留占位，避免布局跳动 -->
                    <div class="pagination" style="visibility: hidden;">1</div>
                <?php endif; ?>
            </div>

        </div> <!-- ✅ product-content-wrapper END -->
    </div> <!-- ✅ product-container END -->

    <?php include '../foot.php'; ?>
</div>
</body>

</html>
