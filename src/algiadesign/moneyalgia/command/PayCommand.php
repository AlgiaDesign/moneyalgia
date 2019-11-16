<?php
namespace algiadesign\moneyalgia\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

use algiadesign\moneyalgia\lang\MessageContainer;
use algiadesign\moneyalgia\MoneyalgiaAPI;

class PayCommand extends Command
{
    public function __construct()
    {
        parent::__construct('mapay', MessageContainer::get('command.mapay.description'), MessageContainer::get('command.mapay.usage'));
        $this->setPermission('moneyalgia.command.mapay');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$this->testPermission($sender)) {
            return false;
        }
        if (!($sender instanceof Player)) {
            return false;
        }

        if (count($args) < 2 || !is_numeric($args[1])) {
            $sender->sendMessage(MessageContainer::get('command.mapay.usage'));

            return false;
        }
        $args[1] = (int) floor($args[1]);

        $instance = MoneyalgiaAPI::getInstance();
        if (!$instance->accountExists($args[0])) {
            $sender->sendMessage(MessageContainer::get('player.notfound', [$args[0]]));

            return true;
        }
        $money = $instance->slice($sender, $args[1]);
        if ($money === null) {
            $sender->sendMessage(MessageContainer::get('command.mapay.not-enough'));

            return true;
        }
        $instance->merge($args[0], $money);
        $sender->sendMessage(MessageContainer::get('command.mapay.pay', [$args[0], $args[1]]));


        return true;
    }
}
