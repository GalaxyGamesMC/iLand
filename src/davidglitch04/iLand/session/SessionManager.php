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
