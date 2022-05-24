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

namespace davidglitch04\iLand\object;

use davidglitch04\iLand\iLand;
use davidglitch04\iLand\utils\DataUtils;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use function explode;
use function in_array;
use function intval;
use function strtolower;
use function trim;

class Land {
	private string $leader = "";

	private array $members = [];

	private string $startpos = "";

	private string $endpos = "";

	private array $settings = [];

	public function __construct(array $landData) {
		$this->leader = $landData["Leader"];
		$this->members = $landData["Members"];
		$this->startpos = $landData["Start"];
		$this->endpos = $landData["End"];
		$this->settings = $landData["Settings"];
	}

	public function getLeader() : string {
		return $this->leader;
	}

	public function isLeader(Player $player) : bool {
		return $this->getLeader() == $player->getName();
	}

	public function getMembers() : array {
		return $this->members;
	}

	public function isMember(Player $player) : bool {
		return in_array(strtolower($player->getName()), $this->getMembers(), true);
	}

	public function getSettings() : array {
		return $this->settings;
	}

	public function getWorldName() : string {
		return $this->getStart()->getWorld()->getFolderName();
	}

	public function getConfigFile() : Config {
		$name = trim(strtolower($this->getLeader()));
		$path = iLand::getInstance()->getDataFolder() . "players/" . "/$name.yml";
		return new Config($path, Config::YAML);
	}

	public function getData() : array {
		foreach ($this->getConfigFile()->getAll() as $lands) {
			$lands = DataUtils::decode($lands);
			if ($lands["Start"] == $this->startpos && $lands["End"] == $this->endpos) {
				return $lands;
			}
		}
		return [];
	}

	public function getStart() : Position {
		$position = explode(",", $this->startpos);
		return new Position(
			intval($position[0]),
			intval($position[1]),
			intval($position[2]),
			Server::getInstance()->getWorldManager()->getWorldByName($position[3])
		);
	}

	public function getEnd() : Position {
		$position = explode(",", $this->endpos);
		return new Position(
			intval($position[0]),
			intval($position[1]),
			intval($position[2]),
			Server::getInstance()->getWorldManager()->getWorldByName($position[3])
		);
	}

	public function contains(Position $position) : bool {
		$start = $this->getStart();
		$end = $this->getEnd();
		if ($start->getWorld()->getFolderName() == $position->getWorld()->getFolderName()) {
			if (($position->getX() <= $end->getX() && $position->getX() >= $start->getX()
			&& $position->getZ() >= $start->getZ() && $position->getZ() <= $end->getZ())) {
				return true;
			}
		}
		return false;
	}

	public function equals(string $startpos, string $endpos) : bool {
		$worldname = iLand::getInstance()->getLandManager()->StringToPosition($startpos)->getWorld()->getFolderName();
		if ($this->getWorldName() == $worldname && $startpos == $this->startpos && $endpos == $this->endpos) {
			return true;
		} else {
			return false;
		}
	}
}
