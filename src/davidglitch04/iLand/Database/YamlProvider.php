<?php

namespace davidglitch04\iLand\Database;

use davidglitch04\iLand\iLand;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\world\Position;

class YamlProvider implements Provider
{
    protected iLand $iland;

    protected Config $land;

    public function __construct(iLand $iland)
    {
        $this->iland = $iland;
    }

    public function initConfig(): void
    {
        $this->land = new Config($this->iland->getDataFolder().'land.yml', Config::YAML);
    }

    public function getDatabase(string $name): array
    {
        return $this->land->get($name, []);
    }

    public function addOwner(string $name, Player $owner): void
    {
        //TODO:
    }

    public function getSafeSpawn(string $owner): Position
    {
        $land = $this->land->get($owner);
        return new Position($land["Spawn"]["X"], $land["Spawn"]["Y"], $land["Spawn"]["Z"], $land["World"]);
    }
}
