<?php

namespace Routing\Core\Database;
use PDO;

class PostgresDB
{
    private string $host;
    private int $port;
    private string $dbname;
    private string $user;
    private string $password;

    private PDO $connection;
    private string $table;


    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'];
        $this->port = $_ENV['DB_PORT'];
        $this->dbname = $_ENV['DB_NAME'];
        $this->user = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASSWORD'];
    }

    /**
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * @throws \Exception
     */
    public function connect(): void
    {
        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname};user={$this->user};password={$this->password}";
        try {
            $this->connection = new PDO($dsn);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \Exception('Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function insert(array $values): array
    {
        $fields = array_keys($values);
        $fieldList = implode(', ', $fields);
        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $valueList = array_values($values);

        $sql = "INSERT INTO " . $this->table . " ($fieldList) VALUES ($placeholders)";
        $stmt = $this->connection->prepare($sql);
        return ['status' => $stmt->execute($valueList)];
    }

    public function getLatsID(): false|string
    {
        return $this->connection->lastInsertId();
    }

    public function update(array $fields, array $values, array $where): bool
    {
        $setClause = implode(' = ?, ', $fields) . ' = ?';
        $query = "UPDATE " . $this->table . " SET {$setClause}";

        if (!empty($where)) {
            $query .= " WHERE ";
            $conditions = [];
            foreach ($where as $item) {
                $conditions[] = "{$item['column']} {$item['operator']} ?";
            }
            $query .= implode(' AND ', $conditions);
        }

        $stmt = $this->connection->prepare($query);
        $params = array_merge($values, array_column($where, 'value'));
        return $stmt->execute($params);
    }

    public function delete($where): bool
    {
        $query = "DELETE FROM " . $this->table;

        $query .= " WHERE ";
        $conditions = [];
        foreach ($where as $item) {
            $conditions[] = "{$item['column']} {$item['operator']} {$item['value']}";
        }
        $query .= implode(' AND ', $conditions);

        $stmt = $this->connection->prepare($query);
        return $stmt->execute();
    }

    public function select(string $columns, array $where, array $join = [], string $having = '', string $orderBy = '', string $limit = '', $groupBy = ''): array
    {
        $query = "SELECT {$columns} FROM {$this->table}";

        if (!empty($join)) {
            foreach ($join as $item) {
                $query .= " {$item['type']} {$item['table']} ON {$item['V1']} {$item['operator']} {$item['V2']}";
            }
        }

        if (!empty($where)) {
            $query .= " WHERE ";
            $conditions = [];
            foreach ($where as $item) {
                $conditions[] = "{$item['column']} {$item['operator']} {$item['value']}";
            }
            $query .= implode(' AND ', $conditions);
        }

        if (!empty($groupBy)) {
            $query .= " GROUP BY {$groupBy}";
        }

        if (!empty($having)) {
            $query .= " HAVING {$having}";
        }

        if (!empty($orderBy)) {
            $query .= " ORDER BY {$orderBy}";
        }

        if (!empty($limit)) {
            $query .= " LIMIT {$limit}";
        }

        $stmt = $this->connection->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
