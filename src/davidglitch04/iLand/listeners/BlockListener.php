<?php

/*
 *
 *   _____ _                     _
 *  |_   _| |                   | |
 *    | | | |     __ _ _ __   __| |
 *    | | | |    / _` | '_ \ / _` |
 *   _| |_| |___| (_| | | | | (_| |
 *  |_____|______\__,_|_| |_|\__,_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author DavidGlitch04
 * @link https://github.com/David-pm-pl/iLand
 *
 *
*/

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
		if ($this->iland->getLandManager()->testBlock($event) == false) {
			$event->cancel();
		}
	}

	public function onPlace(BlockPlaceEvent $event) : void {
		if ($this->iland->getLandManager()->testBlock($event) == false) {
			$event->cancel();
		}
	}
}
