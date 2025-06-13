<?php
require_once 'config.php';

class Database {
    private $connection;

    public function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
            $this->connection = new PDO($dsn, DB_USER, DB_PASS);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }

    public function getUserByEmail($email) {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser($name, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->connection->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        return $stmt->execute();
    }

    public function createPremiumSubscription($userId, $plan, $months, $calculations) {
        $startDate = date('Y-m-d H:i:s');
        $endDate = date('Y-m-d H:i:s', strtotime("+$months months"));
        
        $stmt = $this->connection->prepare("INSERT INTO subscriptions (user_id, plan, start_date, end_date, calculation_limit, calculations_used) 
                                           VALUES (:user_id, :plan, :start_date, :end_date, :calculation_limit, 0)");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':plan', $plan);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->bindParam(':calculation_limit', $calculations);
        return $stmt->execute();
    }

    public function getUserSubscription($userId) {
        $stmt = $this->connection->prepare("SELECT * FROM subscriptions WHERE user_id = :user_id AND end_date > NOW()");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function incrementCalculationCount($userId) {
        $stmt = $this->connection->prepare("UPDATE subscriptions SET calculations_used = calculations_used + 1 WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }
}
?>
