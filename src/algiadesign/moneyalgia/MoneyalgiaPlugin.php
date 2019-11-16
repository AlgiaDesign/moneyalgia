<?php
namespace algiadesign\moneyalgia;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;

use algiadesign\moneyalgia\MoneyalgiaAPI;
use algiadesign\moneyalgia\provider\SQLiteProvider;
use algiadesign\moneyalgia\provider\JsonProvider;
use algiadesign\moneyalgia\lang\MessageContainer;
use algiadesign\moneyalgia\command\PayCommand;
use algiadesign\moneyalgia\command\SeeCommand;
use algiadesign\moneyalgia\command\SetCommand;

class MoneyalgiaPlugin extends PluginBase implements Listener
{
    /** @var \algiadesign\moneyalgia\provider\Provider */
    private $provider;

    public function onEnable()
    {
        $this->reloadConfig();
        $config = new MoneyalgiaConfig($this->getConfig());

        $this->openProvider($config->getProviderName());
        $this->registerCommands();
        MessageContainer::load($this);
        MoneyalgiaAPI::getInstance()->load($this->provider, $config);

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onDisable()
    {
        $this->provider->close();
    }

    public function onPlayerJoin(PlayerJoinEvent $event)
    {
        $instance = MoneyalgiaAPI::getInstance();
        $player = $event->getPlayer();
        if (!$instance->accountExists($player)) {
            $instance->createAccount($player, $instance->getDefaultAmount());
        }
    }

    private function openProvider(string $name)
    {
        if ($name === 'sqlite') {
            $this->provider = new SQLiteProvider($this);
        } else {
            $this->provider = new JsonProvider($this);
        }
        $this->provider->open();
    }

    private function registerCommands()
    {
        $manager = PermissionManager::getInstance();
        $manager->addPermission(new Permission('moneyalgia.command.mapay', 'Allows the user to run the mapay command', Permission::DEFAULT_TRUE));
        $manager->addPermission(new Permission('moneyalgia.command.masee', 'Allows the user to run the masee command', Permission::DEFAULT_TRUE));
        $manager->addPermission(new Permission('moneyalgia.command.maset', 'Allows the admin to run the maset command', Permission::DEFAULT_OP));

        $map = $this->getServer()->getCommandMap();
        $map->register('moneyalgia', new PayCommand());
        $map->register('moneyalgia', new SeeCommand());
        $map->register('moneyalgia', new SetCommand());
    }
}
