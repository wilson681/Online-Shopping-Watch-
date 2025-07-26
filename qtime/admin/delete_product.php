<?php
require("../auth.php");

if (!isset($_GET['product_id'])) {
  header("Location: admin.php?section=product");
  exit;
}

$product_id = intval($_GET['product_id']);

try {
  $db->beginTransaction();
  //delete all related data
  $db->prepare("DELETE FROM order_status_logs WHERE product_id = ?")->execute([$product_id]);
  $db->prepare("DELETE FROM orderitems WHERE product_id = ?")->execute([$product_id]);
  $db->prepare("DELETE FROM productimages WHERE product_id = ?")->execute([$product_id]);
  $db->prepare("DELETE FROM product_colours WHERE product_id = ?")->execute([$product_id]);
  $db->prepare("DELETE FROM productfeatures WHERE product_id = ?")->execute([$product_id]);
  $db->prepare("DELETE FROM products WHERE product_id = ?")->execute([$product_id]);

  $db->commit();
  
  header("Location: admin.php?section=product&deleted=1");
  exit;

} catch (PDOException $e) {
  $db->rollBack();
  echo "Error deleting product: " . $e->getMessage();
}
