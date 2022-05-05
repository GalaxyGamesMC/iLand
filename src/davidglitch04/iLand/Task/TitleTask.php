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
            if(iLand::getInstance()->getConfig()->get("particel-selected", false)){
                $posA = iLand::getInstance()->getSessionManager()->getSession($this->player)->getPositionA();
                $posB = iLand::getInstance()->getSessionManager()->getSession($this->player)->getPositionB();
                for ($x=$posA->getX();($posA->getX() < $posB->getX()) ? $x<=$posB->getX() : $x>=$posB->getX();($posA->getX() < $posB->getX()) ? $x++ : $x--){
                    $this->spawnParticleEffect($this->player, new Vector3($x, $this->player->getPosition()->getY()+3, $posA->getZ()));
                    $this->spawnParticleEffect($this->player, new Vector3($x, $this->player->getPosition()->getY()+3, $posB->getZ()));
                }
                for ($z=$posA->getZ();($posA->getZ() < $posB->getZ()) ? $z<=$posB->getZ() : $z>=$posB->getZ();($posA->getZ() < $posB->getZ()) ? $z++ : $z--){
                    $this->spawnParticleEffect($this->player, new Vector3($posA->getX(), $this->player->getPosition()->getY()+3, $z));
                    $this->spawnParticleEffect($this->player, new Vector3($posB->getX(), $this->player->getPosition()->getY()+3, $z));
                }
            }
            $this->player->sendTitle(iLand::getLanguage()->translateString("title.selectland.complete1"), iLand::getLanguage()->translateString("title.selectland.complete2", [iLand::getInstance()->getTool()->getName()]));
        }
        if(!iLand::getInstance()->getSessionManager()->inSession($this->player)){
            throw new CancelTaskException();
        }
    }
    public function spawnParticleEffect(Player $player, Vector3 $position): void {
		$packet = new SpawnParticleEffectPacket();
		$packet->position = $position;
		$packet->particleName = "minecraft:villager_happy";
		$packet->molangVariablesJson = '';
		$player->getNetworkSession()->sendDataPacket($packet);
	}
}