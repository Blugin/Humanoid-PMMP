<?php

namespace blugin\humanoid\command\subcommands;

use pocketmine\Player;
use pocketmine\command\CommandSender;
use blugin\humanoid\Humanoid as Plugin;
use blugin\humanoid\act\PlayerAct;
use blugin\humanoid\act\child\AddHumanoidAct;
use blugin\humanoid\command\{
  SubCommand, PoolCommand
};
use blugin\humanoid\util\Translation;

class AddSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'add');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        if ($sender instanceof Player) {
            PlayerAct::registerAct(new AddHumanoidAct($sender, isset($args[0]) ? implode(' ', $args) : $sender->getNameTag()));
            $sender->sendMessage(Plugin::$prefix . $this->translate('success'));
        } else {
            $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@in-game'));
        }
        return true;
    }
}