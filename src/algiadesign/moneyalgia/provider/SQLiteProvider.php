<?php
namespace algiadesign\moneyalgia\provider;

use algiadesign\moneyalgia\MoneyalgiaPlugin;
use SQLite3;

class SQLiteProvider implements Provider
{
    /** @var MoneyalgiaPlugin */
    private $plugin;
    /** @var SQLite3 */
    private $db;

    public function __construct(MoneyalgiaPlugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function open(): bool
    {
        $file = $this->plugin->getDataFolder() . "moneyalgia.db";
        if (file_exists($file)) {
            $this->db = new SQLite3($file, SQLITE3_OPEN_READWRITE);
        } else {
            $this->db = new SQLite3($file, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        }

        return $this->db->exec('CREATE TABLE IF NOT EXISTS money (id TEXT PRIMARY KEY NOT NULL, amount INTEGER NOT NULL)');
    }

    public function accountExists(string $id): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM money WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_TEXT);
        $data = $stmt->execute();
        $result = [];
        while ($d = $data->fetchArray(SQLITE3_ASSOC)) {
            $result[] = $d;
        }

        return \count($result) > 0;
    }

    public function createAccount(string $id, int $amount): bool
    {
        if (!$this->accountExists($id)) {
            $stmt = $this->db->prepare("INSERT INTO money (id, amount) VALUES (:id, :amount)");
            $stmt->bindValue(':id', $id, SQLITE3_TEXT);
            $stmt->bindValue(':amount', $amount, SQLITE3_INTEGER);

            return $stmt->execute() === false ? false : true;
        }

        return false;
    }

    public function removeAccount(string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM money id = :id");
        $stmt->bindValue(':id', $id);

        return $stmt->execute() === false ? false : true;
    }

    public function getAmount(string $id): ?int
    {
        $stmt = $this->db->prepare("SELECT amount FROM money WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_TEXT);
        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        return empty($result) ? null : $result["amount"];
    }

    public function setAmount(string $id, int $amount): bool
    {
        $stmt = $this->db->prepare("UPDATE money SET amount = :amount WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_TEXT);
        $stmt->bindvalue(':amount', $amount, SQLITE3_INTEGER);

        return $stmt->execute() === false ? false : true;
    }

    public function getAll(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM money");
        $data = $stmt->execute();
        $result = [];
        while ($d = $data->fetchArray(SQLITE3_ASSOC)) {
            $result[] = $d;
        }

        return $result;
    }

    public function save(): bool
    {
        return true;
    }

    public function close(): bool
    {
        if ($this->db instanceof SQLite3) {
            $this->db->close();

            return true;
        }

        return false;
    }
}
