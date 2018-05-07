<?php

namespace blugin\humanoid\command\subcommands\simple;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use blugin\humanoid\Humanoid as Plugin;
use blugin\humanoid\act\PlayerAct;
use blugin\humanoid\act\child\SetHumanoidPositionAct;
use blugin\humanoid\command\SimpleSubCommand;
use blugin\humanoid\util\Translation;

class SetPositionCommand extends SimpleSubCommand{

    public function __construct(){
        parent::__construct('position');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        if ($sender instanceof Player) {
            if (!isset($args[0]) || $args[0] === '*') {
                $pos = $sender->asPosition();
            } elseif (!isset($args[1])) {
                $player = Server::getInstance()->getPlayer($args[0]);
                if ($player === null) {
                    $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@invalid-player', $args[0]));
                    return false;
                } else {
                    $pos = $player->asPosition();
                }
            } elseif (isset($args[2])) {
                $x = is_numeric($args[0]) ? (float) $args[0] : null;
                if ($x === null) {
                    $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@invalid', $args[0]));
                    return false;
                }
                $y = is_numeric($args[1]) ? (float) $args[1] : null;
                if ($y === null) {
                    $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@invalid', $args[1]));
                    return false;
                }
                $z = is_numeric($args[2]) ? (float) $args[2] : null;
                if ($z === null) {
                    $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@invalid', $args[2]));
                    return false;
                }
                if (isset($args[3])) {
                    $level = Server::getInstance()->getLevelByName($args[3]);
                    if ($level === null) {
                        $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@invalid', $args[3]));
                        return false;
                    }
                } else {
                    $level = $sender->getLevel();
                }
                $pos = new Position($x, $y, $z, $level);
            } else {
                $sender->sendMessage(Server::getInstance()->getLanguage()->translateString("commands.generic.usage", [$this->usage]));
                return false;
            }
            PlayerAct::registerAct(new SetHumanoidPositionAct($sender, $pos));
            return true;
        } else {
            $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@in-game'));
            return false;
        }
    }
}