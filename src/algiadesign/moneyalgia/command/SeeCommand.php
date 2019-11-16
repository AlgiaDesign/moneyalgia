<?php
namespace algiadesign\moneyalgia\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

use algiadesign\moneyalgia\MoneyalgiaAPI;
use algiadesign\moneyalgia\lang\MessageContainer;

class SeeCommand extends Command
{
    public function __construct()
    {
        parent::__construct('masee', MessageContainer::get('command.masee.description'), MessageContainer::get('command.masee.usage'));
        $this->setPermission('moneyalgia.command.masee');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$this->testPermission($sender)) {
            return false;
        }

        $name = isset($args[0]) ? $args[0] : null;
        $name = $name === null && $sender instanceof Player ? $sender->getName() : $name;
        if ($name === null) {
            $sender->sendMessage(MessageContainer::get('command.masee.usage'));
            return false;
        }

        $money = MoneyalgiaAPI::getInstance()->getAmount($name);
        $sender->sendMessage($money !== null ? MessageContainer::get('command.masee.show', [$name, $money]) : MessageContainer::get('player.notfound', [$name]));

        return true;
    }
}