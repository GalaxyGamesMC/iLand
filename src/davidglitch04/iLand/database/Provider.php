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

namespace davidglitch04\iLand\database;

use davidglitch04\iLand\iLand;
use davidglitch04\iLand\object\Land;
use pocketmine\player\Player;
use pocketmine\world\Position;

interface Provider {
	/** @param iLand $iland */
	public function __construct(iLand $iland);

	/** @return void */
	public function initConfig() : void;

	public function getData(Player $name) : array;

	public function CountLand(Player $player) : int;

	public function isOverlap(Position $position) : bool ;

	public function addLand(Player $player, Position $positionA, Position $positionB) : void;

	public function delLand(Player $player, int $key) : void;

	public function getLandByPosition(Position $position) : Land|null;
}
