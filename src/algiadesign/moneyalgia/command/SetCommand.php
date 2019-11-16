<?php
namespace algiadesign\moneyalgia\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use algiadesign\moneyalgia\lang\MessageContainer;
use algiadesign\moneyalgia\MoneyalgiaAPI;

class SetCommand extends Command
{
    public function __construct()
    {
        parent::__construct('maset', MessageContainer::get('command.maset.description'), MessageContainer::get('command.maset.usage'));
        $this->setPermission('moneyalgia.command.maset');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$this->testPermission($sender)) {
            return false;
        }

        if (count($args) < 2 || !is_numeric($args[1])) {
            $sender->sendMessage(MessageContainer::get('command.maset.usage'));

            return false;
        }
        $args[1] = (int) floor($args[1]);

        $instance = MoneyalgiaAPI::getInstance();
        if (!$instance->accountExists($args[0])) {
            $sender->sendMessage(MessageContainer::get('player.notfound', [$args[0]]));

            return false;
        }
        $instance->setAmount($args[0], $args[1]);
        $sender->sendMessage(MessageContainer::get('command.maset.set', [$args[0], $args[1]]));

        return true;
    }
}
