<?php

namespace App\Models;

use PDO;
use PDOException;
use RuntimeException;

class ReportsModel
{
    private $pdo;

    private const PRODUCTS_TABLE = 'products_tb';
    private const ORDERS_TABLE = 'orders_tb';
    private const ORDER_PRODUCTS_TABLE = 'order_products_tb';
    private const CUSTOMER_TABLE = 'customers_tb';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllReports($startDate, $endDate)
    {
        try {
            // Handle cases where startDate or endDate is "n/a"
            if ($startDate === 'n/a') {
                $startDate = $this->getOldestDate();
            }
            if ($endDate === 'n/a') {
                $endDate = $this->getLatestDate();
            }

            $reports = [
                'sales_report' => $this->getSalesReport($startDate, $endDate),
                'orders_report' => $this->getOrdersReport($startDate, $endDate),
                'inventory_report' => $this->getInventoryReport($startDate, $endDate),
                'customer_reports' => $this->getCustomerReport($startDate, $endDate),
            ];

            return [
                'status' => 'success',
                'data' => [
                    'filter_by' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ],
                    'reports' => $reports
                ]
            ];
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    private function getOldestDate()
    {
        $query = "SELECT MIN(createdAt) AS oldest_date FROM " . self::ORDERS_TABLE;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['oldest_date'];
    }

    private function getLatestDate()
    {
        $query = "SELECT MAX(createdAt) AS latest_date FROM " . self::ORDERS_TABLE;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['latest_date'];
    }

    private function getSalesReport($startDate, $endDate)
    {
        $query = "
            SELECT SUM(op.totalPrice) AS total_sales, COUNT(DISTINCT o.id) AS total_orders
            FROM " . self::ORDERS_TABLE . " o
            JOIN " . self::ORDER_PRODUCTS_TABLE . " op ON o.id = op.orderID
            WHERE o.createdAt >= :startDate AND o.createdAt <= :endDate
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['startDate' => $startDate, 'endDate' => $endDate]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getOrdersReport($startDate, $endDate)
    {
        $query = "
            SELECT
                SUM(CASE WHEN o.status = 'pending' THEN 1 ELSE 0 END) AS total_pending_orders,
                SUM(CASE WHEN o.status = 'completed' THEN 1 ELSE 0 END) AS total_completed_orders,
                SUM(CASE WHEN o.status = 'cancelled' THEN 1 ELSE 0 END) AS total_cancelled_orders
            FROM " . self::ORDERS_TABLE . " o
            WHERE o.createdAt >= :startDate AND o.createdAt <= :endDate
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['startDate' => $startDate, 'endDate' => $endDate]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getInventoryReport($startDate, $endDate)
    {
        $lowStockQuery = "
            SELECT p.productName, p.productStock
            FROM " . self::PRODUCTS_TABLE . " p
            WHERE p.productStock < 10
        ";

        $highStockQuery = "
            SELECT p.productName, p.productStock
            FROM " . self::PRODUCTS_TABLE . " p
            WHERE p.productStock >= 10 
        ";

        // Fetch low stock products
        $stmtLow = $this->pdo->prepare($lowStockQuery);
        $stmtLow->execute();
        $lowStockProducts = $stmtLow->fetchAll(PDO::FETCH_ASSOC);

        // Fetch high stock products
        $stmtHigh = $this->pdo->prepare($highStockQuery);
        $stmtHigh->execute();
        $highStockProducts = $stmtHigh->fetchAll(PDO::FETCH_ASSOC);

        return [
            'low_stock_products' => [
                'total' => count($lowStockProducts),
                'lists' => $lowStockProducts
            ],
            'high_stock_products' => [
                'total' => count($highStockProducts),
                'lists' => $highStockProducts
            ]
        ];
    }

    private function getCustomerReport($startDate, $endDate)
    {
        $query = "
            SELECT COUNT(*) AS total
            FROM " . self::CUSTOMER_TABLE . " c
            WHERE c.createdAt >= :startDate AND c.createdAt <= :endDate
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['startDate' => $startDate, 'endDate' => $endDate]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
