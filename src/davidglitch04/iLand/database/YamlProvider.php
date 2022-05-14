<?php

declare(strict_types=1);

namespace davidglitch04\iLand\database;

use davidglitch04\iLand\iLand;
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
use pocketmine\utils\Config;
use pocketmine\world\Position;
use pocketmine\world\World;
use function explode;
use function in_array;
use function intval;
use function max;
use function min;
use function strtolower;

class YamlProvider implements Provider
{
	protected iLand $iland;

	protected Config $land;

	public function __construct(iLand $iland)
	{
		$this->iland = $iland;
	}

	public function initConfig() : void
	{
		$this->land = new Config($this->iland->getDataFolder() . 'land.json', Config::JSON);
	}

	public function getData($name) : array
	{
		return $this->land->get($name, []);
	}

	public function setData($name, $data) : void
	{
		$this->land->set($name, $data);
		$this->land->save();
	}

	public function CountLand(Player $player) : int
	{
		$name = $player->getName();
		$counts = 0;
		foreach ((array) $this->land->getAll() as $lands){
			if($lands["Owner"] == $name){
				$counts++;
			}
		}
		return $counts;
	}

	public function isOverlap(
		float $startX,
		float $startZ,
		float $endX,
		float $endZ,
		World $world
		) : bool
	{
	foreach ((array) $this->land->getAll() as $lands){
		$start = $this->StringToPosition($lands['Start']);
		$end = $this->StringToPosition($lands['End']);
		if($start->getWorld()->getFolderName() == $world->getFolderName()){
			if (($startX <= $end->getX() && $endX >= $start->getX()
				&& $endZ >= $start->getZ() && $startZ <= $end->getZ())) {
					return true;
				}
			}
		}
		return false;
	}

	public function addLand(
		Player $player,
		Position $positionA,
		Position $positionB
		) : void
	{
		$counts = 0;
		foreach ((array) $this->land->getAll() as $lands){
			$counts = $counts + 1;
		}
		$landDb = [
			"Owner" => $player->getName(),
			"Name" => iLand::getLanguage()->translateString("gui.landmgr.unnamed"),
			"Spawn" => $this->PositionToString($positionA),
			"Start" => $this->PositionToString($positionA),
			"End" => $this->PositionToString($positionB),
			"Members" => [],
			"Settings" => [
				"allow_open_chest" => false,
				"use_bucket" => false,
				"use_furnace" => false,
				"allow_place" => false,
				"allow_dropitem" => false,
				"allow_pickupitem" => false,
				"allow_destroy" => false
			]
		];
		$this->land->set($counts + 1, $landDb);
		$this->land->save();
	}

	public function PositionToString(Position $position) : string
	{
		$x = (int) $position->getX();
		$y = (int) $position->getY();
		$z = (int) $position->getZ();
		$world = (string) $position->getWorld()->getFolderName();
		$string = $x . "," . $y . "," . $z . "," . $world;
		return $string;
	}

	public function StringToPosition(string $string) : Position
	{
		$position = explode(",", $string);
		return new Position(
			intval($position[0]),
			intval($position[1]),
			intval($position[2]),
			Server::getInstance()->getWorldManager()->getWorldByName($position[3])
		);
	}

	public function testPlayer(Event $event) : bool
	{
	   if ($event instanceof PlayerInteractEvent){
		   $player = $event->getPlayer();
		   $block = $event->getBlock();
			if ($event->getAction() == PlayerInteractEvent::RIGHT_CLICK_BLOCK){
				$results = $this->inLand($player->getPosition())['Results'];
				if($results['Status']){
					if($results['Data']['Owner'] == $player->getName()){
						return true;
					} elseif (in_array(strtolower($player->getName()), $results['Data']['Members'], true)
					&& !$results['Data']['Settings']['allow_open_chest']
					&& $block instanceof Chest){
						return false;
					} elseif (in_array(strtolower($player->getName()), $results['Data']['Members'], true)
					&& !$results['Data']['Settings']['use_furnace']
					&& $block instanceof Furnace){
						return false;
					}
					return true;
				}
		   }
		} elseif ($event instanceof PlayerBucketEvent){
			$player = $event->getPlayer();
			$results = $this->inLand($player->getPosition())['Results'];
			if($results['Status']){
				if($results['Data']['Owner'] == $player->getName()){
					return true;
				} elseif (in_array(strtolower($player->getName()), $results['Data']['Members'], true) && $results['Data']['Settings']['use_bucket']){
					return true;
				}
				return false;
			}
		} elseif ($event instanceof PlayerDropItemEvent){
			$player = $event->getPlayer();
			$results = $this->inLand($player->getPosition())['Results'];
			if($results['Status']){
				if($results['Data']['Owner'] == $player->getName()){
					return true;
				} elseif (in_array(strtolower($player->getName()), $results['Data']['Members'], true) && $results['Data']['Settings']['allow_dropitem']){
					return true;
				}
				return false;
			}
		} elseif ($event instanceof EntityItemPickupEvent){
			$player = $event->getEntity();
			$results = $this->inLand($player->getPosition())['Results'];
			if($results['Status']){
				if($results['Data']['Owner'] == $player->getName()){
					return true;
				} elseif (in_array(strtolower($player->getName()), $results['Data']['Members'], true) && $results['Data']['Settings']['allow_pickupitem']){
					return true;
				}
				return false;
			}
		}
	   return true;
	}

	public function testBlock(Event $event) : bool
	{
		if ($event instanceof BlockBreakEvent){
			$block = $event->getBlock();
			$player = $event->getPlayer();
			$results = $this->inLand($block->getPosition())['Results'];
			if($results['Status']){
				if($results['Data']['Owner'] == $player->getName()){
					return true;
				} elseif (in_array(strtolower($player->getName()), $results['Data']['Members'], true) && $results['Data']['Settings']['allow_destroy']){
					return true;
				}
				return false;
			}
		} elseif ($event instanceof BlockPlaceEvent){
			$block = $event->getBlock();
			$player = $event->getPlayer();
			$results = $this->inLand($block->getPosition())['Results'];
			if($results['Status']){
				if($results['Data']['Owner'] == $player->getName()){
					return true;
				} elseif (in_array(strtolower($player->getName()), $results['Data']['Members'], true) && $results['Data']['Settings']['allow_place']){
					return true;
				}
				return false;
			}
		}
		return true;
	}

	public function inLand(Position $position) : array
	{
		foreach ((array) $this->land->getAll() as $lands){
			$start = $this->StringToPosition($lands['Start']);
			$end = $this->StringToPosition($lands['End']);
			$x = $position->getX();
			$z = $position->getZ();
			$worldname = $position->getWorld()->getFolderName();
			$x1 = min($start->getX(), $end->getX());
			$x2 = max($start->getX(), $end->getX());
			$z1 = min($start->getZ(), $end->getZ());
			$z2 = max($start->getZ(), $end->getZ());
			if(($worldname == $start->getWorld()->getFolderName())
			&& ($x >= $x1) && ($x <= $x2) && ($z >= $z1) && ($z < $z2)){
			   return [
			   	'Results' => [
			   		'Status' => true,
			   		'Data' => $lands
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

	public function getAllLand() : array
	{
		return (array) $this->land->getAll();
	}

	public function save() : void
	{
		$this->land->save();
	}
}
