<?php

namespace blugin\humanoid\act\child;

use pocketmine\Player;
use pocketmine\level\Position;
use blugin\humanoid\Humanoid as Plugin;
use blugin\humanoid\act\{
  PlayerAct, ClickHumanoidAct
};
use blugin\humanoid\event\PlayerClickHumanoidEvent;
use blugin\humanoid\util\Translation;

class SetHumanoidPositionAct extends PlayerAct implements ClickHumanoidAct{

    /** @var Position */
    private $pos;

    /**
     * @param Player   $player
     * @param Position $pos
     */
    public function __construct(Player $player, Position $pos){
        parent::__construct($player);
        $this->pos = $pos;
    }

    /** @param PlayerClickHumanoidEvent $event */
    public function onClickHumanoid(PlayerClickHumanoidEvent $event) : void{
        $event->getHumanoid()->teleport($this->pos);
        $this->player->sendMessage(Plugin::$prefix . Translation::translate('humanoid-set@success'));

        $event->setCancelled(true);
        $this->cancel();
    }
}