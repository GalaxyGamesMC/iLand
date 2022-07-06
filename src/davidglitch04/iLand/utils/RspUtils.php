<?php

declare(strict_types=1);

namespace davidglitch04\iLand\utils;

use pocketmine\plugin\PluginBase;
use pocketmine\resourcepacks\ResourcePack;
use ReflectionClass;
use Webmozart\PathUtil\Path;
use function array_search;
use function mb_strtolower;
use function strtolower;
use function unlink;

class RspUtils {
	public function __construct(
		private PluginBase $plugin
	) {
	}

	public function regRsp(ResourcePack $pack) : void {
		$manager = $this->plugin->getServer()->getResourcePackManager();

		$reflection = new ReflectionClass($manager);

		$property = $reflection->getProperty("resourcePacks");
		$property->setAccessible(true);

		$currentResourcePacks = $property->getValue($manager);
		$currentResourcePacks[] = $pack;
		$property->setValue($manager, $currentResourcePacks);

		$property = $reflection->getProperty("uuidList");
		$property->setAccessible(true);
		$currentUUIDPacks = $property->getValue($manager);
		$currentUUIDPacks[strtolower($pack->getPackId())] = $pack;
		$property->setValue($manager, $currentUUIDPacks);

		$property = $reflection->getProperty("serverForceResources");
		$property->setAccessible(true);
		$property->setValue($manager, true);
	}

	public function unRegRsp(ResourcePack $pack) : void {
		$manager = $this->plugin->getServer()->getResourcePackManager();

		$reflection = new ReflectionClass($manager);

		$property = $reflection->getProperty("resourcePacks");
		$property->setAccessible(true);
		$currentResourcePacks = $property->getValue($manager);
		$key = array_search($pack, $currentResourcePacks, true);
		if ($key !== false) {
			unset($currentResourcePacks[$key]);
			$property->setValue($manager, $currentResourcePacks);
		}

		$property = $reflection->getProperty("uuidList");
		$property->setAccessible(true);
		$currentUUIDPacks = $property->getValue($manager);
		if (isset($currentResourcePacks[mb_strtolower($pack->getPackId())])) {
			unset($currentUUIDPacks[mb_strtolower($pack->getPackId())]);
			$property->setValue($manager, $currentUUIDPacks);
		}
		unlink(Path::join($this->plugin->getDataFolder(), $this->plugin->getName() . '.mcpack'));
	}
}
