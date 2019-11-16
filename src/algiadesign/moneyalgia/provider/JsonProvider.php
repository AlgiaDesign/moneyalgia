<?php
namespace algiadesign\moneyalgia\provider;

use pocketmine\utils\Config;

use algiadesign\moneyalgia\MoneyalgiaPlugin;

class JsonProvider implements Provider
{
    /** @var Config */
    private $config;
    /** @var MoneyalgiaPlugin */
    private $plugin;
    /** @var array */
    private $money = [];

    public function __construct(MoneyalgiaPlugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function open(): bool
    {
        $this->config = new Config($this->plugin->getDataFolder() . "moneyalgia.json", Config::JSON);
        $this->money = $this->config->getAll();

        return true;
    }

    public function accountExists(string $id): bool
    {
        return isset($this->money[$id]);
    }

    public function createAccount(string $id, int $amount): bool
    {
        if (!$this->accountExists($id)) {
            $this->money[$id] = $amount;

            return true;
        }

        return false;
    }

    public function removeAccount(string $id): bool
    {
        if ($this->accountExists($id)) {
            unset($this->money[$id]);

            return true;
        }

        return false;
    }

    public function getAmount(string $id): ?int
    {
        return $this->accountExists($id) ? $this->money[$id] : null;
    }

    public function setAmount(string $id, int $amount): bool
    {
        if ($this->accountExists($id)) {
            $this->money[$id] = $amount;

            return true;
        }

        return false;
    }

    public function getAll(): array
    {
        return $this->money;
    }

    public function save(): bool
    {
        $this->config->setAll($this->money);
        $this->config->save();

        return true;
    }

    public function close(): bool
    {
        $this->save();
        $this->config = null;

        return true;
    }
}
