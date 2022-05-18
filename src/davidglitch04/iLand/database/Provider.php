<?php

declare(strict_types=1);

namespace davidglitch04\iLand\database;

use davidglitch04\iLand\iLand;
use pocketmine\player\Player;
use pocketmine\world\Position;

interface Provider {
	/** @param iLand $iland */
	public function __construct(iLand $iland);

	/** @return void */
	public function initConfig() : void;

	/**
	 * @return void
	 */
	public function getData(Player $name) : array;

	public function CountLand(Player $player) : int;

	public function isOverlap(Position $position) : bool ;

	public function addLand(Player $player, Position $positionA, Position $positionB) : void;

	public function delLand(Player $player, int $key) : void;

	public function getAllReceived() : array;

	public function save() : void;
}
