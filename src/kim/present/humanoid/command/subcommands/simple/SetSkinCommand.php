<?php

namespace kim\present\humanoid\command\subcommands\simple;

use kim\present\humanoid\act\child\SetHumanoidSkinAct;
use kim\present\humanoid\act\PlayerAct;
use kim\present\humanoid\command\SimpleSubCommand;
use kim\present\humanoid\Humanoid as Plugin;
use kim\present\humanoid\util\Translation;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;

class SetSkinCommand extends SimpleSubCommand{
	/**
	 * SetSkinCommand constructor.
	 */
	public function __construct(){
		parent::__construct('skin');
	}

	/**
	 * @param CommandSender $sender
	 * @param String[]      $args
	 *
	 * @return bool
	 */
	public function onCommand(CommandSender $sender, array $args) : bool{
		if($sender instanceof Player){
			if(!isset($args[0]) || $args[0] === '*'){
				$skin = $sender->getSkin();
			}else{
				$player = Server::getInstance()->getPlayer($args[0]);
				if($player === null){
					$sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@invalid-player', $args[0]));
					return false;
				}else{
					$skin = $player->getSkin();
				}
			}
			PlayerAct::registerAct(new SetHumanoidSkinAct($sender, $skin));
			return true;
		}else{
			$sender->sendMessage(Plugin::$prefix . Translation::translate('command-generic-failure@in-game'));
			return false;
		}
	}
}