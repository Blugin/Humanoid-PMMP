<?php

namespace kim\presenthumanoid\event;

use kim\presenthumanoid\entity\Humanoid;
use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\Player;

class PlayerClickHumanoidEvent extends PlayerEvent implements Cancellable{

	public const LEFT_CLICK = InventoryTransactionPacket::USE_ITEM_ON_ENTITY_ACTION_ATTACK;
	public const RIGHT_CLICK = InventoryTransactionPacket::USE_ITEM_ON_ENTITY_ACTION_INTERACT;
	public static $handlerList = null;
	/** @var Humanoid */
	protected $humanoid;

	/** @var int */
	protected $action;

	public function __construct(Player $player, Humanoid $humanoid, int $action = self::LEFT_CLICK){
		$this->player = $player;
		$this->humanoid = $humanoid;
		$this->action = $action;
	}

	/** @return Humanoid */
	public function getHumanoid() : Humanoid{
		return $this->humanoid;
	}

	/** @return int */
	public function getAction() : int{
		return $this->action;
	}
}