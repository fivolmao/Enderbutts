<?php

declare(strict_types=1);

namespace Fivo\Enderbutts\Listeners;

use Fivo\Enderbutts\Entity\EnderPearlEntity;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use quark\entity\projectile\EnderPearl;
use quark\event\entity\ProjectileLaunchEvent;
use quark\event\Listener;
use quark\item\VanillaItems;
use quark\player\Player;

class PlayerListener implements Listener {

    private array $riding = [];

    public function onProjectileLaunch(ProjectileLaunchEvent $event): void {
        $entity = $event->getEntity();
        $player = $entity->getOwningEntity();

        $event->getEntity()->flagForDespawn();
        $event->cancel();

        if($entity instanceof EnderPearl) {
            if($player instanceof Player) {

                $player->getInventory()->getItem($player->getInventory()->getHeldItemIndex())->pop();
                $player->getInventory()->setItem($player->getInventory()->getHeldItemIndex(), VanillaItems::ENDER_PEARL());

                if(($this->riding[$player->getXuid()] ?? null) !== null && $this->riding[$player->getXuid()]->isCollided === false) {
                    if($this->riding[$player->getXuid()] instanceof EnderPearlEntity) {
                        $this->broadcastRiding($player, $this->riding[$player->getXuid()]);
                        $this->riding[$player->getXuid()]->setMotion($player->getDirectionVector()->multiply(1.75));
                    }
                } else {
                    $newEntity = new EnderPearlEntity($entity->getLocation(), $entity->getOwningEntity());
                    $newEntity->setMotion($entity->getMotion()->multiply(2.5 / 1.5));
                    $newEntity->spawnToAll();

                    $this->broadcastRiding($player, $newEntity);
                    $this->riding[$player->getXuid()] = $newEntity;
                }
            }
        }

    }

    private function broadcastRiding(Player $player, EnderPearlEntity $entity): void {
        $networkProperties = $player->getNetworkProperties();
        $networkProperties->setGenericFlag(EntityMetadataFlags::RIDING, true);
        $networkProperties->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, new Vector3(0, 1.25, 0));

        $pk = new SetActorLinkPacket();
        $pk->link = new EntityLink($entity->getId(), $player->getId(), EntityLink::TYPE_RIDER, false, true, 0);
        foreach ($entity->getViewers() as $v) $v->getNetworkSession()->sendDataPacket($pk);
    }

}