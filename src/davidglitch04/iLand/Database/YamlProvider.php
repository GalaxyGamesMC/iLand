<?php

namespace davidglitch04\iLand\Database;

use davidglitch04\iLand\iLand;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\world\Position;

class YamlProvider implements Provider
{
    /**@var iLand $iland */
    protected iLand $iland;

    /**@var Config $land */
    protected Config $land;

    /**
     * @param iLand $iland
     */
    public function __construct(iLand $iland)
    {
        $this->iland = $iland;
    }

    /**
     * @return void
     */
    public function initConfig(): void
    {
        $this->land = new Config($this->iland->getDataFolder().'land.yml', Config::YAML);
    }

    /**
     * @param  string $name
     * @return array
     */
    public function getDatabase(string $name): array
    {
        return $this->land->get($name, []);
    }

    /**
     * @param string $name
     * @param Player $owner
     * @return void
     */
    public function addOwner(string $name, Player $owner): void
    {
        //TODO:
    }

    /**
     * @param  string $owner
     * @return Position
     */
    public function getSafeSpawn(string $owner): Position
    {
        $land = $this->land->get($owner);
        return new Position($land["Spawn"]["X"], $land["Spawn"]["Y"], $land["Spawn"]["Z"], $land["World"]);
    }
}
