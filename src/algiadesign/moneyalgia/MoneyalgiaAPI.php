<?php

namespace algiadesign\moneyalgia;

use pocketmine\Player;

use algiadesign\moneyalgia\provider\Provider;

class MoneyalgiaAPI
{
    public const SUCCESS = 1;
    public const INVALID_VALUE = 0;
    public const NO_ACCOUNT = -1;
    public const FAILURE = -2;

    public static $instance;

    public static function getInstance(): MoneyalgiaAPI
    {
        if (!isset(self::$instance)) {
            self::$instance = new MoneyalgiaAPI();
        }

        return self::$instance;
    }

    private $provider;
    private $config;

    private function __construct()
    {
    }

    public function load(Provider $provider, MoneyalgiaConfig $config)
    {
        if (!isset($this->provider)) {
            $this->provider = $provider;
        }
        if (!isset($this->config)) {
            $this->config = $config;
        }
    }

    public function accountExists($player): bool
    {
        if ($player instanceof Player) {
            $player = $player->getName();
        }
        $player = strtolower($player);

        return $this->provider->accountExists($player);
    }

    public function createAccount($player, int $defaultAmount): bool
    {
        if ($player instanceof Player) {
            $player = $player->getName();
        }
        $player = strtolower($player);

        if (!$this->accountExists($player)) {
            return $this->provider->createAccount($player, $defaultAmount);
        }

        return false;
    }

    public function removeAccount($player, string $reason = ""): bool
    {
        if ($player instanceof Player) {
            $player = $player->getName();

            if ($player->isOnline()) {
                $player->kick($reason, false);
            }
        }
        $player = strtolower($player);

        if ($this->accountExists($player)) {
            return $this->provider->removeAccount($player);
        }

        return false;
    }

    public function getAmount($player): ?int
    {
        if ($player instanceof Player) {
            $player = $player->getName();
        }
        $player = strtolower($player);

        return $this->provider->getAmount($player);
    }

    public function setAmount($player, int $amount): int
    {
        if ($amount < 0 || $amount > PHP_INT_MAX) {
            return self::INVALID_VALUE;
        }

        if ($player instanceof Player) {
            $player = $player->getName();
        }
        $player = strtolower($player);

        if ($this->accountExists($player)) {
            if ($this->provider->setAmount($player, $amount)) {
                return self::SUCCESS;
            }
            return self::FAILURE;
        }

        return self::NO_ACCOUNT;
    }

    public function merge(int $amount, $from, $to): int
    {
        if ($from instanceof Player) {
            $from = $from->getName();
        }
        $from = strtolower($from);
        if ($to instanceof Player) {
            $to = $to->getName();
        }
        $to = strtolower($to);

        $fromAmount = $this->getAmount($from);
        $toAmount = $this->getAmount($to);
        if ($fromAmount !== null && $toAmount !== null) {
            if ($fromAmount - $amount < 0) {
                return self::INVALID_VALUE;
            }

            if ($this->provider->setAmount($from, $fromAmount - $amount) && $this->provider->setAmount($to, $toAmount + $amount)) {
                return self::SUCCESS;
            }

            return self::FAILURE;
        }

        return self::NO_ACCOUNT;
    }

    public function getUnit(): string
    {
        return $this->config->getUnit();
    }

    public function getDefaultAmount(): int
    {
        return $this->config->getDefaultAmount();
    }

    public function getBalance(): int
    {
        return $this->config->getBalance();
    }
}
