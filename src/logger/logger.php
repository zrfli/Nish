<?php
if ($_SERVER['REQUEST_URI'] == '/src/logger/logger.php') {
    header("Location: /");
}

class MisyLogger
{
    private PDO $pdo;
    private string $tableName;
    private ?dbInformation $dbClass = null;

    public function __construct(dbInformation $dbClass)
    {
        $this->dbClass = $dbClass;

        try {
            $this->pdo = new PDO(
                "mysql:host={$this->dbClass->getHost()};dbname={$this->dbClass->getDbName()};port={$this->dbClass->getPort()}",
                $this->dbClass->getUser(),
                $this->dbClass->getPassword()
            );
        } catch (PDOException $e) {
            die(false);
        }

        $this->tableName = 'Ms_Logs';

        $this->createLogsTable();

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    private function tableExists(): bool
    {
        $query = "SHOW TABLES LIKE '{$this->tableName}'";
        $stmt = $this->pdo->query($query);
        return $stmt->rowCount() > 0;
    }

    private function createLogsTable(): void
    {
        if (!$this->tableExists()) {
            $createQuery = "CREATE TABLE {$this->tableName} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                log_level VARCHAR(20) NOT NULL,
                message TEXT NOT NULL,
                details TEXT DEFAULT NULL,
                user_id INT DEFAULT NULL,
                event VARCHAR(20) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";

            $this->pdo->exec($createQuery);
        }
    }

    public function logError(string $message, array $context = [], string $event = null): void
    {
        $details = isset($context['details']) ? $context['details'] : null;
        $userId = isset($context['user_id']) ? $context['user_id'] : null;
        $this->logToTable('ERROR', $message, $details, $userId, $event);
    }

    public function logInfo(string $message, array $context = [], string $event = null): void
    {
        $details = isset($context['details']) ? $context['details'] : null;
        $userId = isset($context['user_id']) ? $context['user_id'] : null;
        $this->logToTable('INFO', $message, $details, $userId, $event);
    }

    private function logToTable(string $logLevel, string $message, ?string $details = null, ?int $userId = null, ?string $event = null): void
    {
        $insertQuery = "INSERT INTO {$this->tableName} (log_level, message, details, user_id, event) VALUES (:log_level, :message, :details, :user_id, :event)";
        $stmt = $this->pdo->prepare($insertQuery);

        $stmt->bindValue(':log_level', $logLevel, PDO::PARAM_STR);
        $stmt->bindValue(':message', $message, PDO::PARAM_STR);
        $stmt->bindValue(':details', $details, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':event', $event, PDO::PARAM_STR);

        $stmt->execute();
    }
}
?>