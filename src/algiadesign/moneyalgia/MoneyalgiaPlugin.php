<?php

namespace algiadesign\moneyalgia;

use pocketmine\plugin\PluginBase;

use algiadesign\moneyalgia\provider\SQLiteProvider;

class MoneyalgiaPlugin extends PluginBase
{
    public function onEnable()
    {
        $provider = new SQLiteProvider($this);
        $provider->open();
        MoneyalgiaAPI::getInstance()->load($provider);
    }
}
