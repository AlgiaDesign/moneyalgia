<?php
namespace algiadesign\moneyalgia;

use pocketmine\plugin\PluginBase;

use algiadesign\moneyalgia\provider\SQLiteProvider;
use algiadesign\moneyalgia\provider\JsonProvider;

class MoneyalgiaPlugin extends PluginBase
{
    /** @var \algiadesign\moneyalgia\provider\Provider */
    private $provider;

    public function onEnable()
    {
        $this->reloadConfig();
        $config = new MoneyalgiaConfig($this->getConfig());

        switch ($config->getProviderName()) {
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

        MoneyalgiaAPI::getInstance()->load($this->provider, $config);
    }

    public function onDisable()
    {
        $this->provider->close();
    }
}
