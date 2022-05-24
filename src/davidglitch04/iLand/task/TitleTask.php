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

namespace davidglitch04\iLand\task;

use davidglitch04\iLand\iLand;
use davidglitch04\iLand\utils\ItemUtils;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\player\Player;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\Task;

class TitleTask extends Task {
	protected Player $player;

	public function __construct(Player $player) {
		$this->player = $player;
	}

	public function onRun() : void {
		if (!$this->player->isConnected() || !iLand::getInstance()->getSessionManager()->inSession($this->player)) {
			throw new CancelTaskException();
		}
		$status = '';
		$statusA = iLand::getInstance()->getSessionManager()->getSession($this->player)->isNull(location: "A");
		$statusB = iLand::getInstance()->getSessionManager()->getSession($this->player)->isNull(location: "B");
		if ($statusA) {
			$status = "A";
		} elseif ($statusB) {
			$status = "B";
		}
		$this->player->sendTitle(iLand::getLanguage()->translateString("title.rangeselector.inmode"), iLand::getLanguage()->translateString("title.rangeselector.selectpoint", [ItemUtils::getItem()->getName(),$status]));
		if (!$statusA && !$statusB && iLand::getInstance()->getSessionManager()->inSession($this->player)) {
			if (iLand::getDefaultConfig()->get("particel-selected", false)) {
				$posA = iLand::getInstance()->getSessionManager()->getSession($this->player)->getPositionA();
				$posB = iLand::getInstance()->getSessionManager()->getSession($this->player)->getPositionB();
				for ($y = 1;$y <= 255;$y++) {
					$this->addBorder(
						player: $this->player,
						x: $posA->getX(),
						y: $y,
						z: $posA->getZ()
					);
					$this->addBorder(
						player: $this->player,
						x: $posB->getX(),
						y: $y,
						z: $posB->getZ()
					);
					$this->addBorder(
						player: $this->player,
						x: $posB->getX(),
						y: $y,
						z: $posA->getZ()
					);
					$this->addBorder(
						player: $this->player,
						x: $posA->getX(),
						y: $y,
						z: $posB->getZ()
					);
				}
			}
			$this->player->sendTitle(iLand::getLanguage()->translateString("title.selectland.complete1"), iLand::getLanguage()->translateString("title.selectland.complete2", [ItemUtils::getItem()->getName()]));
		}
	}

	public function addBorder(
		Player $player,
		float $x,
		float $y,
		float $z) : void {
		$packet = new SpawnParticleEffectPacket();
		$packet->position = new Vector3($x, $y, $z);
		$packet->particleName = "minecraft:villager_happy";
		$packet->molangVariablesJson = '';
		$player->getNetworkSession()->sendDataPacket($packet);
	}
}
