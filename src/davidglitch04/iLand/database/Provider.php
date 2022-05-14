<?php

declare(strict_types=1);

namespace davidglitch04\iLand\database;

use davidglitch04\iLand\iLand;
use pocketmine\event\Event;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\world\World;

interface Provider {
	/** @param iLand $iland */
	public function __construct(iLand $iland);

	/** @return void */
	public function initConfig() : void;

	/**
	 * @return void
	 */
	public function getData(string $name) : array;

	public function CountLand(Player $player) : int;

	public function isOverlap(float $startX, float $startZ, float $endX, float $endZ, World $world) : bool;

	public function addLand(Player $player, Position $positionA, Position $positionB) : void;

	public function PositionToString(Position $position) : string;

	public function StringToPosition(string $string) : Position;

	public function testPlayer(Event $event) : bool;

	public function testBlock(Event $event) : bool;

	public function inLand(Position $position) : array;

	public function delLand(int $key) : void;

	public function getAllLand() : array;

	public function save() : void;
}
