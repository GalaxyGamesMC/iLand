<?php

declare(strict_types=1);

namespace davidglitch04\iLand\session;

use davidglitch04\iLand\iLand;
use pocketmine\player\Player;
use function strtolower;

class SessionManager {
	public function __construct() {
		//NOTHING
	}

	public function addPlayer(Player $player) : void {
		$name = strtolower($player->getName());
		if (!isset(iLand::getInstance()->session[$name])) {
			iLand::getInstance()->session[$name] = new Session($name);
		}
	}

	public function inSession(Player $player) : bool {
		$name = strtolower($player->getName());
		if (isset(iLand::getInstance()->session[$name])) {
			return true;
		} else {
			return false;
		}
	}

	public function removePlayer(Player $player) : void {
		$name = strtolower($player->getName());
		if (isset(iLand::getInstance()->session[$name])) {
			unset(iLand::getInstance()->session[$name]);
		}
	}

	public function getSession(Player $player) : Session {
		$name = strtolower($player->getName());

		return iLand::getInstance()->session[$name];
	}
}
