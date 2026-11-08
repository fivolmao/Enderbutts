<?php

declare(strict_types=1);

namespace Fivo\Enderbutts\Entity;

use pocketmine\nbt\tag\CompoundTag;
use quark\entity\Entity;
use quark\entity\Location;
use quark\entity\projectile\EnderPearl;

class EnderPearlEntity extends EnderPearl {

    public function __construct(Location $location, ?Entity $shootingEntity, ?CompoundTag $nbt = null) {
        parent::__construct($location, $shootingEntity, $nbt);
    }

}