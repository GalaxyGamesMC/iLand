<?php

namespace davidglitch04\iLand\Database;

use davidglitch04\iLand\iLand;
use pocketmine\player\Player;
use pocketmine\world\Position;

interface Provider
{
    public function __construct(iLand $iland);

    public function initConfig(): void;

    public function getDatabase(string $name): array;

    public function addOwner(string $name, Player $owner): void;

    public function getSafeSpawn(string $owner): Position;
}
