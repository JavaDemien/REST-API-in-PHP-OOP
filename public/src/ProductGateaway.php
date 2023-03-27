<?php 

class ProductGateaway
{
    private PDO $connection;

    public function __construct(Database $database)
    {
        $this->connection = $database->getConnection();
    }

    public function getAll(): array
    {
        $sql = "SELECT * 
                FROM product";
        $statement = $this->connection->query($sql);

        $data = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {

            $row['is_available'] = (bool) $row['is_available'];

            $data[] = $row;
        }

        return $data;
    }

    public function create(array $data): string
    {
        $sql = "INSERT INTO product (name, size, is_available)
                VALUES (:name, :size, :is_available)";

        $statement = $this->connection->prepare($sql);

        $statement->bindValue(":name", $data['name'], PDO::PARAM_STR);
        $statement->bindValue(":size", $data['size'] ?? 0, PDO::PARAM_INT);
        $statement->bindValue(":is_available", (bool) $data['is_available'] ?? false, PDO::PARAM_BOOL);

        $statement->execute();

        return $this->connection->lastInsertId();

    }

    public function get(string $id): array | false
    {
        $sql = "SELECT *
                FROM product
                WHERE id = :id";
        
        $statement = $this->connection->prepare($sql);

        $statement->bindValue(":id", $id, PDO::PARAM_INT);

        $statement->execute();

        $data = $statement->fetch(PDO::FETCH_ASSOC);

        if ($data !== false) {
            $data['is_available'] = (bool) $data['is_available'];
        }

        return $data;
    }

    public function update(array $current, array $new): int
    {
        $sql = "UPDATE product
                SET name = :name, size = :size, is_available = :is_available
                WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        $statement->bindValue(':name', $new['name'] ?? $current['name'], PDO::PARAM_STR);
        $statement->bindValue(':size', $new['size'] ?? $current['size'], PDO::PARAM_INT);
        $statement->bindValue(':is_available', $new['is_available'] ?? $current['is_available'], PDO::PARAM_BOOL);

        $statement->bindValue(':id', $current['id'], PDO::PARAM_INT);

        $statement->execute();

        return $statement->rowCount();
    }

    public function delete(string $id): int
    {
        $sql = "DELETE FROM product
                WHERE id = :id";

        $statement = $this->connection->prepare($sql);

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        $statement->execute();

        return $statement->rowCount();
    }
}
?>