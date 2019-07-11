<?php

namespace algiadesign\moneyalgia;

use pocketmine\Player;

use algiadesign\moneyalgia\provider\Provider;
use algiadesign\moneyalgia\money\Money;

class MoneyalgiaAPI
{
    public const SUCCESS = 1;
    public const INVALID_VALUE = 0;
    public const NO_ACCOUNT = -1;
    public const FAILURE = -2;

    public static $instance;

    /**
     * インスタンス取得
     *
     * @return MoneyalgiaAPI
     */
    public static function getInstance(): MoneyalgiaAPI
    {
        if (!isset(self::$instance)) {
            self::$instance = new MoneyalgiaAPI();
        }

        return self::$instance;
    }

    /** @var Provider */
    private $provider;
    /** @var MoneyalgiaConfig */
    private $config;

    private function __construct()
    {
    }

    /**
     * ロード
     *
     * @param Provider $provider
     * @param MoneyalgiaConfig $config
     * @return void
     */
    public function load(Provider $provider, MoneyalgiaConfig $config)
    {
        if (!isset($this->provider)) {
            $this->provider = $provider;
        }
        if (!isset($this->config)) {
            $this->config = $config;
        }
    }

    /**
     * アカウントが存在するか
     *
     * @param string|Player $player プレイヤー
     * @return boolean
     */
    public function accountExists($player): bool
    {
        if ($player instanceof Player) {
            $player = $player->getName();
        }
        $player = strtolower($player);

        return $this->provider->accountExists($player);
    }

    /**
     * アカウント作成
     *
     * @param string|Player $player        プレイヤー
     * @param integer       $defaultAmount 初期所持金
     * @return boolean
     */
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

    /**
     * アカウント削除
     *
     * @param string|Player $player プレイヤー
     * @param string        $reason 退出時に表示するメッセージ
     * @return boolean
     */
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

    /**
     * 所持金取得
     *
     * @param string|Player $player プレイヤー
     * @return integer|null アカウントが無い場合null
     */
    public function getAmount($player): ?int
    {
        if ($player instanceof Player) {
            $player = $player->getName();
        }
        $player = strtolower($player);

        return $this->provider->getAmount($player);
    }

    /**
     * 所持金の設定
     * このAPIのコンセプト(?)を壊すことになるのであまり使わないで欲しい
     *
     * @param string|Player $player プレイヤー
     * @param integer       $amount 量
     * @return integer
     */
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

    /**
     * お金を結合
     *
     * @param Money         $money  結合されるお金
     * @param string|Player $player 結合先のプレイヤー
     * @return integer
     */
    public function merge(Money &$money, $player): int
    {
        if ($player instanceof Player) {
            $player = $player->getName();
        }
        $player = strtolower($player);

        $amount = $this->getAmount($player);
        if ($amount !== null) {
            $this->provider->setAmount($player, $amount + $money->getAmount());
            $money->setAmount(0);

            return self::SUCCESS;
        }

        return self::NO_ACCOUNT;
    }

    /**
     * お金を切り取る
     *
     * @param integer       $amount  量
     * @param string|Player $player  プレイヤー
     * @return Money|null 切り取れない場合null
     */
    public function slice(int $amount, $player): ?Money
    {
        if ($player instanceof Player) {
            $player = $player->getName();
        }
        $player = strtolower($player);

        $money = $this->getAmount($player);
        if ($money !== null) {
            if ($amount <= $money) {
                $this->provider->setAmount($player, $money - $amount);

                return new Money($amount);
            }
        }

        return null;
    }

    /**
     * サーバー残高行き
     *
     * @param Money $money お金
     * @return void
     */
    public function merge2balance(Money &$money)
    {
        $this->config->setBalance($this->getBalance() + $money->getAmount());
        $money->setAmount(0);
    }

    /**
     * サーバー残高からお金を切り取る
     *
     * @param integer $amount 量
     * @return Money|null 切り取れない場合null
     */
    public function sliceFromBalance(int $amount): ?Money
    {
        if ($amount <= $this->getBalance()) {
            $this->config->setBalance($this->getBalance() - $amount);

            return new Money($amount);
        }

        return null;
    }

    /**
     * お金の単位取得
     *
     * @return string
     */
    public function getUnit(): string
    {
        return $this->config->getUnit();
    }

    /**
     * config.ymlで設定されている初期所持金の取得
     *
     * @return integer
     */
    public function getDefaultAmount(): int
    {
        return $this->config->getDefaultAmount();
    }

    /**
     * サーバーの残高の取得
     *
     * @return integer
     */
    public function getBalance(): int
    {
        return $this->config->getBalance();
    }
}
