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

namespace davidglitch04\iLand;

use davidglitch04\iLand\object\Land;
use pocketmine\block\Chest;
use pocketmine\block\Furnace;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerBucketEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use function explode;
use function intval;

class LandManager {
	public function __construct() {
		//NOTHING
	}

	public function PositionToString(Position $position) : string {
		$x = (int) $position->getX();
		$y = (int) $position->getY();
		$z = (int) $position->getZ();
		$world = (string) $position->getWorld()->getFolderName();
		$string = $x . "," . $y . "," . $z . "," . $world;
		return $string;
	}

	public function StringToPosition(string $string) : Position {
		$position = explode(",", $string);
		return new Position(
			intval($position[0]),
			intval($position[1]),
			intval($position[2]),
			Server::getInstance()->getWorldManager()->getWorldByName($position[3])
		);
	}

	public function testPlayer(Event $event) : bool {
		if ($event instanceof PlayerInteractEvent) {
			$player = $event->getPlayer();
			$block = $event->getBlock();
			if ($event->getAction() == PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
				$land = iLand::getInstance()->getProvider()->getLandByPosition($player->getPosition());
				if ($land instanceof Land) {
					if ($land->isLeader($player)) {
						return true;
					} elseif ($land->isMember($player)
					&& !$land->getSettings()['allow_open_chest']
					&& $block instanceof Chest) {
						return false;
					} elseif ($land->isMember($player)
					&& !$land->getSettings()['use_furnace']
					&& $block instanceof Furnace) {
						return false;
					}
					return true;
				}
			}
		} elseif ($event instanceof PlayerBucketEvent) {
			$player = $event->getPlayer();
			$land = iLand::getInstance()->getProvider()->getLandByPosition($player->getPosition());
			if ($land instanceof Land) {
				if ($land->isLeader($player)) {
					return true;
				} elseif ($land->isMember($player) && $land->getSettings()['use_bucket']) {
					return true;
				}
				return false;
			}
		} elseif ($event instanceof PlayerDropItemEvent) {
			$player = $event->getPlayer();
			$land = iLand::getInstance()->getProvider()->getLandByPosition($player->getPosition());
			if ($land instanceof Land) {
				if ($land->isLeader($player)) {
					return true;
				} elseif ($land->isMember($player) && $land->getSettings()['allow_dropitem']) {
					return true;
				}
				return false;
			}
		} elseif ($event instanceof EntityItemPickupEvent) {
			$player = $event->getEntity();
			$land = iLand::getInstance()->getProvider()->getLandByPosition($player->getPosition());
			if ($land instanceof Land && $player instanceof Player) {
				if ($land->isLeader($player)) {
					return true;
				} elseif ($land->isMember($player) && $land->getSettings()['allow_pickupitem']) {
					return true;
				}
				return false;
			}
		}
		return true;
	}

	public function testBlock(Event $event) : bool {
		if ($event instanceof BlockBreakEvent) {
			$block = $event->getBlock();
			$player = $event->getPlayer();
			$land = iLand::getInstance()->getProvider()->getLandByPosition($block->getPosition());
			if ($land instanceof Land) {
				if ($land->isLeader($player)) {
					return true;
				} elseif ($land->isMember($player) && $land->getSettings()['allow_destroy']) {
					return true;
				}
				return false;
			}
		} elseif ($event instanceof BlockPlaceEvent) {
			$block = $event->getBlock();
			$player = $event->getPlayer();
			$land = iLand::getInstance()->getProvider()->getLandByPosition($block->getPosition());
			if ($land instanceof Land) {
				if ($land->isLeader($player)) {
					return true;
				} elseif ($land->isMember($player) && $land->getSettings()['allow_place']) {
					return true;
				}
				return false;
			}
		}
		return true;
	}
}
