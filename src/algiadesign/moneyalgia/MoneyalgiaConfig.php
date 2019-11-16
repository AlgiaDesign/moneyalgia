<?php
namespace algiadesign\moneyalgia;

use pocketmine\utils\Config;

class MoneyalgiaConfig
{
    /** @var Config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getUnit(): string
    {
        return (string) $this->config->get('unit', 'ALGIA');
    }

    public function getDefaultAmount(): int
    {
        return (int) $this->config->get('default-amount', 0);
    }

    public function getProviderName(): string
    {
        return (string) $this->config->get('provider', 'json');
    }
}
