<?php
session_start();
require("../auth.php");
//for identify user role is admin or not 
if (!$user_logged_in || $user_role !== 'admin') {
  header("Location: ../login/login.php");
  exit;
}
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard'; //auto default section is dashboard section
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap">
  <title>Admin Control Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/admin.css">
  <link rel="stylesheet" href="../css/delete_user.css">
  <script src="delete_user.js" defer></script>
  <link rel="stylesheet" href="../css/update_status.css">
  <script src="update_status.js" defer></script>
  <script src="delete_product.js" defer></script>
</head>

<body>
  <div class="container">
    <div class="sidebar">
      <h2>Admin Control Panel</h2>
      <ul>
        <li><a href="?section=dashboard" class="<?php echo ($section === 'dashboard') ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="?section=member" class="<?php echo ($section === 'member') ? 'active' : ''; ?>">Member Management</a></li>
        <li><a href="?section=product" class="<?php echo ($section === 'product') ? 'active' : ''; ?>">Product Management</a></li>
        <li><a href="?section=order" class="<?php echo ($section === 'order') ? 'active' : ''; ?>">Order Management</a></li>
        <li><a href="?section=activitylogs" class="<?php echo ($section === 'activitylogs') ? 'active' : ''; ?>">Order Activity logs</a></li>
      </ul>
      <div class="logout">
        <a href="../login/logout.php">Logout</a>
      </div>
    </div>

    <div class="main-content">
      <div class="header">
        <?php
        switch ($section) {
          case 'dashboard':
            echo 'Dashboard';
            break;
          case 'member':
            echo 'Member Management';
            break;
          case 'product':
            echo 'Product Management';
            break;
          case 'order':
            echo 'Order Management';
            break;
          case 'activitylogs':
            echo 'Order Activity Logs';
            break;
          default:
            echo 'Dashboard';
            break;
        }
        ?>

      </div>
      <div id="content">
        <?php
        if ($section === 'dashboard') {
          //get total customers from sql
          $stmt = $db->query("SELECT COUNT(*) AS total_customers FROM users WHERE role = 'customer'");
          $total_customers = $stmt->fetch(PDO::FETCH_ASSOC)['total_customers'];
          //get total products from sql
          $stmt = $db->query("SELECT COUNT(*) AS total_products FROM products");
          $total_product = $stmt->fetch(PDO::FETCH_ASSOC)['total_products'];
          //get total orders from sql
          $stmt = $db->query("SELECT COUNT(*) AS total_orders FROM orders");
          $total_order = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];
          //get total sales from sql
          $stmt = $db->query("SELECT SUM(price * quantity) AS total_revenue FROM orderitems WHERE status IN ('paid', 'shipping')");
          $total_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;
          //get top sales product from sql
          $sql = "SELECT p.name AS product_name,SUM(oi.quantity) AS total_sales
                  FROM orderitems oi
                  JOIN products p ON oi.product_id = p.product_id
                  WHERE oi.status IN ('paid', 'shipping')
                  GROUP BY oi.product_id
                  ORDER BY total_sales DESC
                  LIMIT 5";
          $stmt = $db->query($sql);
          $topSelling = $stmt->fetchAll(PDO::FETCH_ASSOC);
          //analys data for chart.js
          $salesData = $db->query("SELECT DATE(o.created_at) AS sale_date, SUM(o.total_price) AS total_revenue
                                   FROM orders o
                                   JOIN orderitems oi ON oi.order_id = o.order_id
                                   WHERE oi.status IN ('paid', 'shipping')
                                   GROUP BY DATE(o.created_at)
                                   ORDER BY sale_date ASC")->fetchAll(PDO::FETCH_ASSOC);
          // prepare two array day and sales(revenue) for chart.js
          $dates = [];
          $revenues = [];
          foreach ($salesData as $row) {
            $dates[] = $row['sale_date'];
            $revenues[] = $row['total_revenue'];
          }
        ?>
          <div class="overview">
            <div class="card">
              <h3>Total Customers</h3>
              <p><?php echo $total_customers; ?></p>
            </div>
            <div class="card">
              <h3>Total Products</h3>
              <p><?php echo $total_product; ?></p>
            </div>
            <div class="card">
              <h3>Total Orders</h3>
              <p><?php echo $total_order; ?></p>
            </div>
            <div class="card">
              <h3>Total Sales</h3>
              <p>RM<?php echo number_format($total_revenue, 2); ?></p>
            </div>
          </div>
          <div class="content-row">
            <div class="chart-container">
              <div class="chart-title">Sales Revenue & Trends</div>
              <div class="chart-wrapper"><canvas id="salesChart"></canvas></div>
            </div>
            <script>
              window.chartDates = <?= json_encode($dates) ?>; //date array change to javascript array
              window.chartRevenues = <?= json_encode($revenues) ?>; //revenue array change to javascript array
            </script>
            <script src="sales_chart.js"></script>
            <div class="top-selling">
              <h3>Top-Selling Products</h3>
              <table>
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Sales</th>
                  </tr>
                </thead>
                <tbody>
                <tbody>
                  <?php if (empty($topSelling)): ?>
                    <tr>
                      <td colspan="2">No sales data available.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($topSelling as $product): ?>
                      <tr>
                        <td><?= htmlspecialchars($product['product_name']) ?></td>
                        <td><?= $product['total_sales'] ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php
        } elseif ($section === 'member') {
          $search = isset($_GET['search']) ? trim($_GET['search']) : '';
          if ($search) {
            $stmt = $db->prepare("SELECT * FROM users WHERE name LIKE :search OR email LIKE :search OR phone LIKE :search");
            $stmt->execute([':search' => "%$search%"]);
          } else {
            $stmt = $db->query("SELECT * FROM users");
          }
          $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
          <h3>User Management</h3>
          <form method="GET" class="search-form">
            <input type="hidden" name="section" value="member">
            <input type="text" name="search" placeholder="Search by name, email, or phone..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
            <a class="create-btn" href="create_user.php">Create User</a>
          </form>
          <table>
            <thead>
              <tr>
                <th>Profile Photo</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Birthday</th>
                <th>Phone</th>
                <th>DETAILS</th>
                <th>Delete User</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $user): ?>
                <tr>
                  <td><?= $user['profile_picture'] ? "<img src='" . htmlspecialchars($user['profile_picture']) . "' width='50'>" : "N/A"; ?></td>
                  <td><?= htmlspecialchars($user['user_id']); ?></td>
                  <td><?= htmlspecialchars($user['name']); ?></td>
                  <td><?= htmlspecialchars($user['email']); ?></td>
                  <td><?= htmlspecialchars($user['role']); ?></td>
                  <td><?= htmlspecialchars($user['dob']); ?></td>
                  <td><?= htmlspecialchars($user['phone']); ?></td>
                  <td><a class="edit-btn" href="user_detail.php?user_id=<?= $user['user_id']; ?>">DETAILS</a></td>
                  <td><a class="delete-btn" href="delete_user.php?user_id=<?= $user['user_id']; ?>" data-username="<?= htmlspecialchars($user['name']) ?>">Delete</a></td>
                  </td>
                </tr>
                <div id="deleteModal" class="modal">
                  <div class="modal-content">
                    <p id="modalMessage">Confirm delete?</p>
                    <button id="confirmDelete">Confirm</button>
                    <button id="cancelDelete">Cancel</button>
                  </div>
                </div>
                <div id="successMsg" class="success-popup"></div>
                <div id="errorMsg" class="error-popup"></div>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php
        } elseif ($section === 'product') {
          $search = isset($_GET['search']) ? trim($_GET['search']) : '';

          if ($search) {
            $stmt = $db->prepare("SELECT p.product_id, p.name AS product_name, p.price, p.stock,b.brand_name, c.category_name,
                                 (SELECT image_url 
                                  FROM productimages 
                                  WHERE product_id = p.product_id 
                                  ORDER BY image_id ASC 
                                  LIMIT 1) AS image
                                  FROM products p
                                  JOIN brands b ON p.brand_id = b.brand_id
                                  JOIN categories c ON p.category_id = c.category_id
                                  WHERE p.name LIKE :search OR b.brand_name LIKE :search OR p.price LIKE :search OR p.product_id LIKE :search 
                                  ORDER BY p.product_id ASC");
            $stmt->execute([':search' => "%$search%"]);
          } else {
            $stmt = $db->query("SELECT p.product_id, p.name AS product_name, p.price, p.stock,b.brand_name, c.category_name,
                              (SELECT image_url 
                               FROM productimages 
                               WHERE product_id = p.product_id 
                               ORDER BY image_id ASC LIMIT 1) AS image
                               FROM products p
                               JOIN brands b ON p.brand_id = b.brand_id
                               JOIN categories c ON p.category_id = c.category_id
                               ORDER BY p.product_id ASC");
          }
          // Fetch all product records as an associative array
          $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
          <div class="product-management">
            <h3>Product Management</h3>
            <form method="GET" class="product-search-form">
              <input type="hidden" name="section" value="product">
              <input type="text" name="search" placeholder="Search product name, id, or brand..." value="<?= htmlspecialchars($search) ?>">
              <button type="submit">Search</button>
              <a class="createprod-btn" href="create_product.php">Create Product</a>
            </form>
            <!--i dont want use table format hehe -->
            <div class="product-list-header">
              <div class="product-col id">Product ID</div>
              <div class="product-col image">Image</div>
              <div class="product-col name">Name</div>
              <div class="product-col brand">Brand</div>
              <div class="product-col stock">Stock</div>
              <div class="product-col details">Details</div>
              <div class="product-col details">Delete</div>
            </div>

            <?php foreach ($products as $product): ?>
              <div class="product-list-item">
                <div class="product-col id"><?= $product['product_id'] ?></div>
                <div class="product-col image">
                  <img src="<?= htmlspecialchars($product['image']) ?>" alt="Product Image">
                </div>
                <div class="product-col name"><?= $product['product_name'] ?></div>
                <div class="product-col brand"><?= $product['brand_name'] ?></div>
                <div class="product-col stock"><?= $product['stock'] ?></div>
                <div class="product-col details">
                  <a href="prodmanagement.php?product_id=<?= $product['product_id'] ?>">
                    <button class="product-view-btn">View Details</button>
                  </a>
                </div>
                <div class="product-col details">
                  <button class="product-delete-btn" onclick="confirmDelete(<?= $product['product_id'] ?>)">Delete</button>
                </div>
              </div>
              <!-- Delete Modal -->
              <div id="deleteModal" class="confirm-modal">
                <div class="confirm-modal-content">
                  <p>Confirm Delete Product?</p>
                  <button id="confirmDelete">Confirm</button>
                  <button id="cancelDelete">Cancel</button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
      </div>
      <?php
        } elseif ($section === 'order') {
          // 如果是查看单个订单详情，则显示详情页（保持原有代码）
          if (isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['order_id'])) {
            $order_id = $_GET['order_id'];
            $stmt = $db->prepare("SELECT p.product_id,p.name AS product_name,oi.quantity,oi.price,oi.status,o.order_id,u.user_id AS customer_id,
                                  u.name AS customer_name,u.address,u.email,u.phone,u.postcode,u.state,img.image_url AS image
                                  FROM orderitems oi
                                  JOIN orders o ON oi.order_id = o.order_id
                                  JOIN users u ON o.user_id = u.user_id
                                  JOIN products p ON oi.product_id = p.product_id
                                  LEFT JOIN (
                                  SELECT product_id, MIN(image_id) AS min_id
                                  FROM productimages
                                  GROUP BY product_id) pm ON pm.product_id = p.product_id
                                  LEFT JOIN productimages img ON img.image_id = pm.min_id
                                  WHERE oi.order_id = ?");
            $stmt->execute([$order_id]);
            $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
      ?>
        <h3>Order Details (ID: <?= htmlspecialchars($order_id); ?>)</h3>
        <!-- 这里使用单一 form 包裹整个订单详情更新部分 -->
        <?php if (!empty($order_items)):
              // 从第一项中取出用户资料（因为一笔订单只会有一个用户）
              $first = $order_items[0];
        ?>
          <div class="order-customer-info">
            <h4>Customer Details</h4>

            <div class="customer-grid">
              <div class="info-group"><strong>ID:</strong> <?= $first['customer_id'] ?></div>
              <div class="info-group"><strong>Name:</strong> <?= $first['customer_name'] ?></div>

              <div class="info-group"><strong>Email:</strong> <?= $first['email'] ?></div>
              <div class="info-group"><strong>Phone:</strong> <?= $first['phone'] ?></div>

              <div class="info-group"><strong>Address:</strong> <?= $first['address'] ?></div>
              <div class="info-group"><strong>Postcode:</strong> <?= $first['postcode'] ?></div>
              <div class="info-group"><strong>State:</strong> <?= $first['state'] ?></div>
            <?php endif; ?>
            </div>
          </div>
          <form id="updateStatusForm" method="POST">
            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id); ?>">
            <table>
              <thead>
                <tr>
                  <th>Product Image</th>
                  <th>Product ID</th>
                  <th>Product</th>
                  <th>Quantity</th>
                  <th>Price</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($order_items as $item): ?>
                  <tr>
                    <td>
                      <img src="<?= htmlspecialchars($item['image']); ?>" alt="Product Image" style="width: 60px; height: 60px; object-fit: contain; border-radius: 5px;">
                    <td><?= htmlspecialchars($item['product_id']); ?></td>
                    <td><?= htmlspecialchars($item['product_name']); ?></td>
                    <td><?= htmlspecialchars($item['quantity']); ?></td>
                    <td>RM<?= number_format($item['price'], 2); ?></td>
                    <td>
                      <select name="status[<?= $item['product_id']; ?>]" class="status-select">
                        <option value="paid" <?= $item['status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                        <option value="shipping" <?= $item['status'] == 'shipping' ? 'selected' : ''; ?>>Shipping</option>
                        <option value="refund" <?= $item['status'] == 'refund' ? 'selected' : ''; ?>>Refund</option>
                      </select>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <div class="order-details-actions">
              <button type="submit" class="btn-update">Update Status</button>
              <button type="button" class="btn-return" onclick="window.location.href='?section=order'">Back to Orders</button>
            </div>
          </form>
          <div id="statusSuccess" class="status-popup success"></div>
          <div id="statusError" class="status-popup error"></div>
        </div>

  <?php
          } else {
            // 在显示订单列表之前，查询图表数据：统计每日订单数量（动态图表数据）
            $stmt_chart = $db->query("
                      SELECT DATE(created_at) AS order_date, COUNT(*) AS order_count 
                      FROM orders 
                      GROUP BY DATE(created_at) 
                      ORDER BY order_date ASC
                  ");
            $chart_data = $stmt_chart->fetchAll(PDO::FETCH_ASSOC);
            $chart_labels = [];
            $chart_counts = [];
            foreach ($chart_data as $row) {
              $chart_labels[] = $row['order_date'];
              $chart_counts[] = $row['order_count'];
            }
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            if ($search) {
              $stmt = $db->prepare("
                SELECT o.order_id, u.name AS customer_name, o.total_price, o.created_at,
                       GROUP_CONCAT(DISTINCT oi.status) AS order_status
                FROM orders o
                JOIN users u ON o.user_id = u.user_id
                JOIN orderitems oi ON oi.order_id = o.order_id
                WHERE o.order_id LIKE :search OR u.name LIKE :search
                GROUP BY o.order_id
                ORDER BY o.created_at DESC
              ");
              $stmt->execute([':search' => "%$search%"]);
            } else {
              $stmt = $db->query("
                SELECT o.order_id, u.name AS customer_name, o.total_price, o.created_at,
                       GROUP_CONCAT(DISTINCT oi.status) AS order_status
                FROM orders o
                JOIN users u ON o.user_id = u.user_id
                JOIN orderitems oi ON oi.order_id = o.order_id
                GROUP BY o.order_id
                ORDER BY o.created_at DESC
              ");
            }
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);  // ✅ 只保留这一次就好
  ?>
    <!-- 新的 Order Management 页面 -->
    <div class="order-management">
      <h3>Order Management</h3>
      <!-- 订单列表区域 -->
      <div class="order-list">
        <form method="GET" class="search-form">
          <input type="hidden" name="section" value="order">
          <input type="text" name="search" placeholder="Search by Order ID or Customer Name..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
          <button type="submit">Search</button>
        </form>
        <div class="order-list-header">
          <div class="order-col order-id">Order ID</div>
          <div class="order-col customer">Customer</div>
          <div class="order-col total">Total Price</div>
          <div class="order-col date">Order Date</div>
          <div class="order-col details">Details</div>
        </div>
        <?php foreach ($orders as $order): ?>
          <div class="order-list-item">
            <div class="order-col order-id"><?= htmlspecialchars($order['order_id']); ?></div>
            <div class="order-col customer"><?= htmlspecialchars($order['customer_name']); ?></div>
            <div class="order-col total">RM<?= number_format($order['total_price'], 2); ?></div>
            <div class="order-col date"><?= htmlspecialchars($order['created_at']); ?></div>
            <div class="order-col details">
              <form method="GET" action="">
                <input type="hidden" name="section" value="order">
                <input type="hidden" name="action" value="view">
                <input type="hidden" name="order_id" value="<?= $order['order_id']; ?>">
                <button type="submit">View Details</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  <?php
          }
          // 引入 update_status 功能（用于订单详情状态更新）

        } elseif ($section === 'activitylogs') {
          $search = isset($_GET['search']) ? trim($_GET['search']) : '';

          if ($search) {
            $stmt = $db->prepare("
              SELECT osl.*, u.name AS admin_name ,p.name
              FROM order_status_logs osl
              JOIN users u ON osl.admin_id = u.user_id
              JOIN products p ON osl.product_id = p.product_id
              WHERE u.name LIKE :search OR osl.order_id LIKE :search OR osl.old_status LIKE :search OR osl.new_status LIKE :search
              ORDER BY osl.changed_at DESC
            ");
            $stmt->execute([':search' => "%$search%"]);
          } else {
            $stmt = $db->query("
              SELECT osl.*, u.name AS admin_name, p.name
              FROM order_status_logs osl
                  JOIN products p ON osl.product_id = p.product_id
              JOIN users u ON osl.admin_id = u.user_id
              ORDER BY osl.changed_at DESC
            ");
          }

          // ✅ 无论哪种方式都 fetch 一次
          $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
  ?>
  <div class="order-management">
    <h3>Order Activity Logs</h3>
    <div class="order-list">
      <form method="GET" class="search-form">
        <input type="hidden" name="section" value="activitylogs">
        <input type="text" name="search" placeholder="Search admin name, order ID or status..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit">Search</button>
      </form>

      <div class="order-list-header">
        <div class="order-col">Admin</div>
        <div class="order-col">Order ID</div>
        <div class="order-col">Product Name</div>
        <div class="order-col">Product ID</div>
        <div class="order-col">Old Status</div>
        <div class="order-col">New Status</div>
        <div class="order-col">Changed At</div>
      </div>

      <?php foreach ($logs as $log): ?>
        <div class="order-list-item">
          <div class="order-col"><?= htmlspecialchars($log['admin_name']) ?></div>
          <div class="order-col"><?= htmlspecialchars($log['order_id']) ?></div>
          <div class="order-col"><?= htmlspecialchars($log['name']) ?></div>
          <div class="order-col"><?= htmlspecialchars($log['product_id']) ?></div>
          <div class="order-col"><?= htmlspecialchars($log['old_status']) ?></div>
          <div class="order-col"><?= htmlspecialchars($log['new_status']) ?></div>
          <div class="order-col"><?= htmlspecialchars($log['changed_at']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php
        } else
?>
  </div>
  </div>
  </div>


</body>

</html>