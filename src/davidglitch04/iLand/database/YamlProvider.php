<?php

declare(strict_types=1);

namespace davidglitch04\iLand\database;

use davidglitch04\iLand\iLand;
use davidglitch04\iLand\object\Land;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use function strtolower;

class YamlProvider implements Provider {

	protected iLand $iland;

	protected Config $received;

	public function __construct(iLand $iland) {
		$this->iland = $iland;
	}

	public function initConfig() : void {
		$this->received = new Config($this->iland->getDataFolder() . 'received.yml', Config::YAML);
		if(!file_exists($this->iland->getDataFolder() . "players/")){
			@mkdir($this->iland->getDataFolder() . "players/");
		}
	}

	public function getData(Player $player) : array {
		$name = trim(strtolower($player->getName()));
		if($name === ""){
			return [];
		}
		$path = $this->iland->getDataFolder() . "players/" . $name[0] . "/$name.yml";
		if(!file_exists($path)){
			return [];
		} else{
			$config = new Config($path, Config::YAML);
			return (array)$config->getAll();
		}
	}

	public function setData(Player $player, $key, $landdb) : void {
		$name = trim(strtolower($player->getName()));
		$data = new Config($this->iland->getDataFolder() . "players/" . $name[0] . "/$name.yml", Config::YAML);
		$data->set($key, $landdb);
		$data->save();
	}

	public function CountLand(Player $player) : int {
		$data = $this->getData($player);
		if (empty($data)){
			return 0;
		} else{
			return count($data);
		}
	}

	public function isOverlap(Position $position) : bool {
		foreach (iLand::getInstance()->getLands() as $land){
			if ($land->contains($position)){
				return true;
			}
		}
		return false;
	}

	public function addLand(
		Player $player,
		Position $positionA,
		Position $positionB
		) : void {
		$counts = 0;
		foreach ((array) $this->received->getAll() as $lands) {
			$counts = $counts + 1;
		}
		$name = trim(strtolower($player->getName()));
		$landDb = [
			"Owner" => $player->getName(),
			"Name" => iLand::getLanguage()->translateString("gui.landmgr.unnamed"),
			"Spawn" => iLand::getInstance()->getLandManager()->PositionToString($positionA),
			"Start" => iLand::getInstance()->getLandManager()->PositionToString($positionA),
			"End" => iLand::getInstance()->getLandManager()->PositionToString($positionB),
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
		@mkdir($this->iland->getDataFolder() . "players/" . $name[0] . "/");
		$data = new Config($this->iland->getDataFolder() . "players/" . $name[0] . "/$name.yml", Config::YAML);
		$data->set($this->CountLand($player) + 1, $landDb);
		$data->save();
		$this->received->set($counts + 1, json_encode([
			"Name" => $player->getName(),
			"Start" => iLand::getInstance()->getLandManager()->PositionToString($positionA),
			"End" => iLand::getInstance()->getLandManager()->PositionToString($positionB)
		]));
		$this->received->save();
		iLand::getInstance()->lands[] = new Land($this->received->get($counts+1));
	}

	

	public function delLand(Player $player, int $key) : void {
		$name = trim(strtolower($player->getName()));
		foreach (iLand::getInstance()->getLands() as $keyland => $data){
			if ($data->equals($this->getData($player)[$key]["Start"], $this->getData($player)[$key]["End"])){
				$this->received->remove($keyland+1);
				$this->received->save();
				unset(iLand::getInstance()->lands[$keyland]);
			}
		}
		$data = new Config($this->iland->getDataFolder() . "players/" . $name[0] . "/$name.yml", Config::YAML);
		$data->remove($key);
		$data->save();
	}

	public function getAllReceived() : array {
		return (array) $this->received->getAll();
	}

	public function save() : void {
		$this->received->save();
	}
}
