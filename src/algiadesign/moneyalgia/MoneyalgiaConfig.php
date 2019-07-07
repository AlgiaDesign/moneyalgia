<?php

namespace algiadesign\moneyalgia;

class MoneyalgiaConfig
{
    /** @var \pocketmine\utils\Config */
    private $config;
    /** @var string */
    private $unit;
    /** @var int */
    private $defaultAmount;
    /** @var string */
    private $provider;
    /** @var int */
    private $balance;

    public function __construct(MoneyalgiaPlugin $plugin)
    {
        $this->config = $plugin->getConfig();
        $this->unit = $this->config->get('unit', 'ALGIA');
        $this->defaultAmount = $this->config->get('default-amount', 0);
        $this->provider = $this->config->get('provider', 'json');
        $this->balance = $this->config->get('balance', 0);
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getDefaultAmount(): int
    {
        return $this->defaultAmount;
    }

    public function getProviderName(): string
    {
        return $this->provider;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function setBalance(int $amount)
    {
        $this->balance = $amount;
    }

    public function save()
    {
        $this->config->set('balance', $this->balance);
        $this->config->save();
    }
}
