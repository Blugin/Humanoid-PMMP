<?php

namespace kim\presenthumanoid\act;

use pocketmine\event\player\PlayerInteractEvent;

interface InteractAct{

    /** @param PlayerInteractEvent $event */
    public function onInteract(PlayerInteractEvent $event) : void;
}