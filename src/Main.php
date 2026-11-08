<?php

declare(strict_types=1);

namespace Fivo\Enderbutts;

use Fivo\Enderbutts\Entity\EnderPearlEntity;
use Fivo\Enderbutts\Listeners\PlayerListener;
use pocketmine\nbt\tag\CompoundTag;
use quark\entity\EntityDataHelper;
use quark\entity\EntityFactory;
use quark\plugin\PluginBase;
use quark\utils\SingletonTrait;
use quark\world\World;

class Main extends PluginBase {

    use SingletonTrait;

    public function onLoad(): void {
        self::setInstance($this);
    }

    public function onEnable(): void {
        self::getInstance()->getServer()->getPluginManager()->registerEvents(new PlayerListener(), $this);

        EntityFactory::getInstance()->register(EnderPearlEntity::class, function (World $world, CompoundTag $nbt): EnderPearlEntity {
            return new EnderPearlEntity(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['EnderPearlEntity']);
    }

}
