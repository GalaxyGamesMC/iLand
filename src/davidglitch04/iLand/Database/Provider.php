<?php

namespace davidglitch04\iLand\Database;

use davidglitch04\iLand\iLand;
use pocketmine\player\Player;
use pocketmine\world\Position;

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

    /**
     * @param  string $owner
     * @return Position
     */
    public function getSafeSpawn(string $owner): Position;
}
