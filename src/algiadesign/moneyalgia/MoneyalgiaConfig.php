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

    public function __construct(MoneyalgiaPlugin $plugin)
    {
        $this->config = $plugin->getConfig();
        $this->unit = $this->config->get("unit", "￥");
        $this->defaultAmount = $this->config->get("default-amount", 0);
        $this->provider = $this->config->get("provider", "json");
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit)
    {
        $this->unit = $unit;
    }

    public function getDefaultAmount(): int
    {
        return $this->defaultAmount;
    }

    public function setDefaultAmount(int $amount)
    {
        $this->defaultAmount = $amount;
    }

    public function getProviderName(): string
    {
        return $this->provider;
    }

    public function save()
    {
        $this->config->set("unit", $this->unit);
        $this->config->set("default-amount", $this->defaultAmount);
        $this->config->save();
    }
}
