<?php

namespace algiadesign\moneyalgia;

use pocketmine\plugin\PluginBase;

use algiadesign\moneyalgia\provider\SQLiteProvider;
use algiadesign\moneyalgia\provider\JsonProvider;

class MoneyalgiaPlugin extends PluginBase
{
    /** @var \algiadesign\moneyalgia\provider\Provider */
    private $provider;
    /** @var \aligadesign\moneyalgia\MoneyalgiaConfig */
    private $config;

    public function onEnable()
    {
        $this->config = new MoneyalgiaConfig($this);

        switch ($this->config->getProviderName()) {
            case "json":
                $this->provider = new JsonProvider($this);
                break;
            case "sqlite":
                $this->provider = new SQLiteProvider($this);
                break;
            default:
                break;
        }
        $this->provider->open();

        MoneyalgiaAPI::getInstance()->load($this->provider);
    }

    public function onDisable()
    {
        $this->provider->save();
        $this->provider->close();
        $this->config->save();
    }
}
