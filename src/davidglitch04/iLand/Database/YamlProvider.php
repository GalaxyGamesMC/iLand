<?php

namespace davidglitch04\iLand\Database;

use davidglitch04\iLand\iLand;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use pocketmine\world\World;

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

    public function getData(string $name): array
    {
        return $this->land->get($name, []);
    }

    public function addOwner(string $name, Player $owner): void
    {
        //TODO:
    }

    public function CountLand(Player $player): int
    {
        $name = $player->getName();
        $counts = 0;
        foreach ((array)$this->land->getAll() as $lands){
            if($lands["Owner"] == $name){
                $counts++;
            }
        }
        return $counts;
    }

    public function isOverlap(
        float $startX, 
        float $startZ, 
        float $endX, 
        float $endZ, 
        World $world
        ): bool
    {
    foreach ((array)$this->land->getAll() as $lands){
        $start = $this->StringToPosition($lands['Start']);
        $end = $this->StringToPosition($lands['End']);
        if($start->getWorld()->getFolderName() == $world->getFolderName()){
            if (($startX <= $end->getX() and $endX >= $start->getX()
                and $endZ >= $start->getZ() and $startZ <= $end->getZ())) {
                    return true;
                }
            }
        }
        return false;
    }

    public function addLand(
        Player $player, 
        Position $positionA, 
        Position $positionB
        ): void
    {
        $counts = 0;
        foreach ((array)$this->land->getAll() as $lands){
            $counts = $counts+1;
        }
        $landDb = [
            "Owner" => $player->getName(),
            "Name" => "Unnamed",
            "Spawn" => $this->PositionToString($positionA),
            "Start" => $this->PositionToString($positionA),
            "End" => $this->PositionToString($positionB),
            "Members" => [],
            "Settings" => []
        ];
        $this->land->set($counts+1, $landDb);
        $this->land->save();
    }

    public function PositionToString(Position $position): string{
        $x = (int)$position->getX();
        $y = (int)$position->getY();
        $z = (int)$position->getZ();
        $world = (string)$position->getWorld()->getDisplayName();
        $string = $x.",".$y.",".$z.",".$world;
        return $string;
    }

    public function StringToPosition(string $string): Position{
        $position = explode(",", $string);
        return new Position(
            intval($position[0]), 
            intval($position[1]), 
            intval($position[2]), 
            Server::getInstance()->getWorldManager()->getWorldByName($position[3])
        );
    }

    public function getAllLand(): array
    {
        return (array)$this->land->getAll();
    }

    public function save(): void
    {
        $this->land->save();
    }
}
