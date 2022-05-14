<?php

declare(strict_types=1);

namespace davidglitch04\iLand\listeners;

use davidglitch04\iLand\iLand;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;

class BlockListener implements Listener {
	protected iLand $iland;

	public function __construct(iLand $iland) {
		$this->iland = $iland;
	}

	public function onDestroy(BlockBreakEvent $event) : void {
		if ($this->iland->getProvider()->testBlock($event) == false) {
			$event->cancel();
		}
	}

	public function onPlace(BlockPlaceEvent $event) : void {
		if ($this->iland->getProvider()->testBlock($event) == false) {
			$event->cancel();
		}
	}
}
