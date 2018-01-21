<?php

namespace presentkim\humanoid\command\subcommands\simple;


use pocketmine\command\CommandSender;
use pocketmine\Player;
use presentkim\humanoid\{
  command\SimpleSubCommand, HumanoidMain as Plugin, event\PlayerClickHumanoidEvent, util\Translation
};
use presentkim\humanoid\task\{
  PlayerTask, HumanoidSetTask
};

class SetSneakCommand extends SimpleSubCommand{

    public function __construct(){
        parent::__construct('sneak');
    }

    public function onCommand(CommandSender $sender, array $args){
        if ($sender instanceof Player) {
            PlayerTask::registerTask(new class($sender, implode(' ', $args)) extends HumanoidSetTask{

                /** @var string */
                private $name;

                public function __construct(Player $player, string $name){
                    parent::__construct($player);
                    $this->name = $name;
                }

                public function onClickHumanoid(PlayerClickHumanoidEvent $event){
                    $humanoid = $event->getHumanoid();
                    $humanoid->setSneaking(!$humanoid->isSneaking());
                    $this->player->sendMessage(Plugin::$prefix . Translation::translate('humanoid-set-sneak@success'));

                    $event->setCancelled(true);
                    $this->cancel();
                }
            });
            return true;
        } else {
            $sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@in-game'));
            return false;
        }
    }
}