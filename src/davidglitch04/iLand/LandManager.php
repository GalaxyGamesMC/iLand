<?php

declare(strict_types=1);

namespace davidglitch04\iLand;

use pocketmine\block\Chest;
use pocketmine\block\Furnace;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerBucketEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Server;
use pocketmine\world\Position;
use function explode;
use function in_array;
use function intval;
use function max;
use function min;
use function strtolower;

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
				$results = $this->inLand($player->getPosition())['Results'];
				if ($results['Status']) {
					if ($results['Data']['Owner'] == $player->getName()) {
						return true;
					} elseif (in_array(strtolower($player->getName()), $results['Data']['Members'], true)
					&& !$results['Data']['Settings']['allow_open_chest']
					&& $block instanceof Chest) {
						return false;
					} elseif (in_array(strtolower($player->getName()), $results['Data']['Members'], true)
					&& !$results['Data']['Settings']['use_furnace']
					&& $block instanceof Furnace) {
						return false;
					}
					return true;
				}
			}
		} elseif ($event instanceof PlayerBucketEvent) {
			$player = $event->getPlayer();
			$results = $this->inLand($player->getPosition())['Results'];
			if ($results['Status']) {
				if ($results['Data']['Owner'] == $player->getName()) {
					return true;
				} elseif (in_array(strtolower($player->getName()), $results['Data']['Members'], true) && $results['Data']['Settings']['use_bucket']) {
					return true;
				}
				return false;
			}
		} elseif ($event instanceof PlayerDropItemEvent) {
			$player = $event->getPlayer();
			$results = $this->inLand($player->getPosition())['Results'];
			if ($results['Status']) {
				if ($results['Data']['Owner'] == $player->getName()) {
					return true;
				} elseif (in_array(strtolower($player->getName()), $results['Data']['Members'], true) && $results['Data']['Settings']['allow_dropitem']) {
					return true;
				}
				return false;
			}
		} elseif ($event instanceof EntityItemPickupEvent) {
			$player = $event->getEntity();
			$results = $this->inLand($player->getPosition())['Results'];
			if ($results['Status']) {
				if ($results['Data']['Owner'] == $player->getName()) {
					return true;
				} elseif (in_array(strtolower($player->getName()), $results['Data']['Members'], true) && $results['Data']['Settings']['allow_pickupitem']) {
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
			$results = $this->inLand($block->getPosition())['Results'];
			if ($results['Status']) {
				if ($results['Data']['Owner'] == $player->getName()) {
					return true;
				} elseif (in_array(strtolower($player->getName()), $results['Data']['Members'], true) && $results['Data']['Settings']['allow_destroy']) {
					return true;
				}
				return false;
			}
		} elseif ($event instanceof BlockPlaceEvent) {
			$block = $event->getBlock();
			$player = $event->getPlayer();
			$results = $this->inLand($block->getPosition())['Results'];
			if ($results['Status']) {
				if ($results['Data']['Owner'] == $player->getName()) {
					return true;
				} elseif (in_array(strtolower($player->getName()), $results['Data']['Members'], true) && $results['Data']['Settings']['allow_place']) {
					return true;
				}
				return false;
			}
		}
		return true;
	}

	public function inLand(Position $position) : array {
		foreach (iLand::getInstance()->getLands() as $land) {
			$start = $this->StringToPosition($land->getStart());
			$end = $this->StringToPosition($land->getEnd());
			$x = $position->getX();
			$z = $position->getZ();
			$worldname = $position->getWorld()->getFolderName();
			$x1 = min($start->getX(), $end->getX());
			$x2 = max($start->getX(), $end->getX());
			$z1 = min($start->getZ(), $end->getZ());
			$z2 = max($start->getZ(), $end->getZ());
			if (($worldname == $land->getWorldName())
			&& ($x >= $x1) && ($x <= $x2) && ($z >= $z1) && ($z < $z2)) {
				return [
					'Results' => [
						'Status' => true,
						'Data' => $land->getData()
					]
				];
			}
		}
		return [
			'Results' => [
				'Status' => false,
				'Data' => null
			]
		];
	}
}