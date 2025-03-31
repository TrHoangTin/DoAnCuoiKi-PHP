<?php
class OrderModel {
    private $conn;
    private $orderTable = 'orders';
    private $orderDetailTable = 'order_details';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createOrder($account_id, $name, $phone, $address, $payment_method, $total, $items) {
        try {
            $this->conn->beginTransaction();

            // Tạo đơn hàng
            $query = "INSERT INTO {$this->orderTable} 
                     (account_id, name, phone, address, payment_method, total)
                     VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$account_id, $name, $phone, $address, $payment_method, $total]);
            $order_id = $this->conn->lastInsertId();

            // Thêm chi tiết đơn hàng
            foreach ($items as $item) {
                $query = "INSERT INTO {$this->orderDetailTable} 
                         (order_id, product_id, quantity, price)
                         VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
            }

            $this->conn->commit();
            return $order_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function getOrdersByAccount($account_id) {
        $query = "SELECT * FROM {$this->orderTable} 
                 WHERE account_id = ?
                 ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$account_id]);
        return $stmt->fetchAll();
    }

    public function getOrderDetails($order_id) {
        $query = "SELECT od.*, p.name as product_name, p.image 
                 FROM {$this->orderDetailTable} od
                 JOIN product p ON od.product_id = p.id
                 WHERE od.order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll();
    }

    public function updateOrderStatus($order_id, $status) {
        $query = "UPDATE {$this->orderTable} 
                 SET status = ?, updated_at = NOW()
                 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$status, $order_id]);
    }

    public function getOrderById($order_id) {
        $query = "SELECT * FROM {$this->orderTable} WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$order_id]);
        return $stmt->fetch();
    }
}   