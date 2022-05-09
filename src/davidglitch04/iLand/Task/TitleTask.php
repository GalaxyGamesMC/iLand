<?php

namespace davidglitch04\iLand\Task;

use davidglitch04\iLand\iLand;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\player\Player;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\Task;

class TitleTask extends Task{

    protected Player $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function onRun(): void
    {
        if(!$this->player->isConnected()){
            throw new CancelTaskException();
        }
        if(!iLand::getInstance()->getSessionManager()->inSession($this->player)){
            throw new CancelTaskException();
        }
        $status = '';
        $statusA = iLand::getInstance()->getSessionManager()->getSession($this->player)->isNull("A");
        $statusB = iLand::getInstance()->getSessionManager()->getSession($this->player)->isNull("B");
        if($statusA){
            $status = "A";
        } elseif($statusB){
            $status = "B";
        }
        $this->player->sendTitle(iLand::getLanguage()->translateString("title.rangeselector.inmode"), iLand::getLanguage()->translateString("title.rangeselector.selectpoint", [iLand::getInstance()->getTool()->getName(),$status]));
        if(!$statusA and !$statusB and iLand::getInstance()->getSessionManager()->inSession($this->player)){
            if(iLand::getDefaultConfig()->get("particel-selected", false)){
                $posA = iLand::getInstance()->getSessionManager()->getSession($this->player)->getPositionA();
                $posB = iLand::getInstance()->getSessionManager()->getSession($this->player)->getPositionB();
                for ($x=$posA->getX();($posA->getX() < $posB->getX()) ? $x<=$posB->getX() : $x>=$posB->getX();($posA->getX() < $posB->getX()) ? $x++ : $x--){
                    $this->addBorder($this->player, $x, $this->player->getPosition()->getY()+3, $posA->getZ());
                    $this->addBorder($this->player, $x, $this->player->getPosition()->getY()+3, $posB->getZ());
                }
                for ($z=$posA->getZ();($posA->getZ() < $posB->getZ()) ? $z<=$posB->getZ() : $z>=$posB->getZ();($posA->getZ() < $posB->getZ()) ? $z++ : $z--){
                    $this->addBorder($this->player, $posA->getX(), $this->player->getPosition()->getY()+3, $z);
                    $this->addBorder($this->player, $posB->getX(), $this->player->getPosition()->getY()+3, $z);
                }
            }
            $this->player->sendTitle(iLand::getLanguage()->translateString("title.selectland.complete1"), iLand::getLanguage()->translateString("title.selectland.complete2", [iLand::getInstance()->getTool()->getName()]));
        }
    }
    public function addBorder(
        Player $player, 
        float $x, 
        float $y, 
        float $z): void 
    {
		$packet = new SpawnParticleEffectPacket();
		$packet->position = new Vector3($x, $y, $z);
		$packet->particleName = "minecraft:villager_happy";
		$packet->molangVariablesJson = '';
		$player->getNetworkSession()->sendDataPacket($packet);
	}
}