<?php

namespace presentkim\humanoid\entity;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\Player;
use pocketmine\entity\{
  Entity, Skin
};
use pocketmine\network\mcpe\protocol\{
  MovePlayerPacket, PlayerSkinPacket, types\ContainerIds, AddPlayerPacket, MobEquipmentPacket
};
use pocketmine\nbt\tag\StringTag;
use pocketmine\item\Item;
use pocketmine\utils\UUID;

class Humanoid extends Entity{

    /** @var UUID */
    protected $uuid;

    /** @var Item */
    protected $heldItem;

    /** @var Skin */
    protected $skin;

    /** @var float */
    public $width = 0.6;

    /** @var float */
    public $height = 1.8;

    /** @var float */
    public $eyeHeight = 1.62;

    /** @var float */
    protected $baseOffset = 1.62;

    protected function initEntity(){
        parent::initEntity();

        $this->uuid = UUID::fromRandom();

        $this->setHeldItem($this->namedtag->hasTag('HeldItem') ? Item::nbtDeserialize($this->namedtag->getCompoundTag('HeldItem')) : Item::get(Item::AIR));

        $skinData = $this->namedtag->hasTag('SkinData') ? $this->namedtag->getString('SkinData') : str_repeat("\x00", 8192);
        $geometryName = $this->namedtag->hasTag('GeometryName') ? $this->namedtag->getString('GeometryName') : '';
        $this->setSkin(new Skin('humanoid', $skinData, '', $geometryName));

        $this->setSneaking($this->namedtag->hasTag('Sneak') ? (bool) $this->namedtag->getByte('Sneak') : false);
        $this->setScale($this->namedtag->hasTag('Scale') ? $this->namedtag->getFloat('Scale') : 1);
    }

    /** @return UUID */
    public function getUniqueId(){
        return $this->uuid;
    }

    /** @return Item */
    public function getHeldItem(){
        return clone $this->heldItem;
    }

    /** @param Item $heldItem */
    public function setHeldItem(Item $heldItem){
        $this->heldItem = $heldItem;

        $pk = new MobEquipmentPacket();
        $pk->entityRuntimeId = $this->id;
        $pk->item = $heldItem;
        $pk->inventorySlot = $pk->hotbarSlot = 0;
        $pk->windowId = ContainerIds::INVENTORY;
        $this->server->broadcastPacket($this->getViewers(), $pk);
    }

    /** @return Skin */
    public function getSkin() : Skin{
        return $this->skin;
    }

    /** @param Skin $skin */
    public function setSkin(Skin $skin){
        if (!$skin->isValid()) {
            throw new \InvalidStateException('Specified skin is not valid, must be 8KiB or 16KiB');
        }

        $this->skin = $skin;
        $this->skin->debloatGeometryData();
        $this->sendSkin($this->getViewers());
    }

    /** @param Player[] | null $targets */
    public function sendSkin(array $targets = null){
        $pk = new PlayerSkinPacket();
        $pk->uuid = $this->getUniqueId();
        $pk->skin = $this->skin;
        $this->server->broadcastPacket($targets ?? $this->hasSpawned, $pk);
    }

    public function saveNBT(){
        parent::saveNBT();

        $this->namedtag->setTag($this->heldItem->nbtSerialize(-1, 'HeldItem'));
        $this->namedtag->setTag(new StringTag('SkinData', $this->skin->getSkinData()));
        $this->namedtag->setTag(new StringTag('GeometryName', $this->skin->getGeometryName()));

        $this->namedtag->setTag(new ByteTag('Sneak', (int) $this->isSneaking()));
        $this->namedtag->setTag(new FloatTag('Scale', $this->getScale()));
    }

    /** @param \pocketmine\Player $player */
    protected function sendSpawnPacket(Player $player) : void{
        if (!$this->skin->isValid()) {
            throw new \InvalidStateException((new \ReflectionClass($this))->getShortName() . ' must have a valid skin set');
        }

        $pk = new AddPlayerPacket();
        $pk->uuid = $this->getUniqueId();
        $pk->username = $this->getNameTag();
        $pk->entityRuntimeId = $this->id;
        $pk->position = $this->asVector3();
        $pk->motion = null;
        $pk->yaw = $this->yaw;
        $pk->pitch = $this->pitch;
        $pk->item = $this->heldItem;
        $pk->metadata = $this->propertyManager->getAll();
        $player->dataPacket($pk);

        $this->sendSkin([$player]);
    }

    /** @param EntityDamageEvent $source */
    public function attack(EntityDamageEvent $source){
        $source->setCancelled(true);
    }

    /**
     * @param float $dx
     * @param float $dy
     * @param float $dz
     *
     * @return bool
     */
    public function move(float $dx, float $dy, float $dz) : bool{
        return false;
    }

    /**
     * @param Vector3    $pos
     * @param float|null $yaw
     * @param float|null $pitch
     *
     * @return bool
     */
    public function teleport(Vector3 $pos, float $yaw = null, float $pitch = null) : bool{
        if (parent::teleport($pos, $yaw, $pitch)) {
            $yaw = $yaw ?? $this->yaw;
            $pitch = $pitch ?? $this->pitch;

            $pk = new MovePlayerPacket();
            $pk->entityRuntimeId = $this->getId();
            $pk->position = $this->getOffsetPosition($pos);
            $pk->pitch = $pitch;
            $pk->headYaw = $yaw;
            $pk->yaw = $yaw;
            $pk->mode = MovePlayerPacket::MODE_TELEPORT;

            $this->server->broadcastPacket($targets ?? $this->hasSpawned, $pk);
            $this->spawnToAll();
            return true;
        }
        return false;
    }
}
