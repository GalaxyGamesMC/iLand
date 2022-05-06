<?php

declare(strict_types = 1);

namespace davidglitch04\iLand\database;

use davidglitch04\iLand\iLand;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\world\World;

interface Provider
{
    /** @param iLand $iland */
    public function __construct(iLand $iland);

    /** @return void */
    public function initConfig(): void;

    /**
     * @param  string $name
     * @return void
     */
    public function getData(string $name): array;

    /**
     * @param string $name
     * @param Player $owner
     * @return void
     */
    public function addOwner(string $name, Player $owner): void;

    public function CountLand(Player $player): int;

    public function isOverlap(float $startX, float $startZ, float $endX, float $endZ, World $world): bool;

    public function addLand(Player $player, Position $positionA, Position $positionB): void;

    public function PositionToString(Position $position): string;

    public function StringToPosition(string $string): Position;

    public function save() : void;
}
