<?php

declare(strict_types=1);

namespace davidglitch04\iLand\task;

use davidglitch04\iLand\iLand;
use davidglitch04\iLand\item\ItemUtils;
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

	public function onRun() : void
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
		$this->player->sendTitle(iLand::getLanguage()->translateString("title.rangeselector.inmode"), iLand::getLanguage()->translateString("title.rangeselector.selectpoint", [ItemUtils::getItem()->getName(),$status]));
		if(!$statusA && !$statusB && iLand::getInstance()->getSessionManager()->inSession($this->player)){
			if(iLand::getDefaultConfig()->get("particel-selected", false)){
				$posA = iLand::getInstance()->getSessionManager()->getSession($this->player)->getPositionA();
				$posB = iLand::getInstance()->getSessionManager()->getSession($this->player)->getPositionB();
				for ($y = 1;$y <= 255;$y++){
					$this->addBorder($this->player, $posA->getX(), $y, $posA->getZ());
					$this->addBorder($this->player, $posB->getX(), $y, $posB->getZ());
					$this->addBorder($this->player, $posB->getX(), $y, $posA->getZ());
					$this->addBorder($this->player, $posA->getX(), $y, $posB->getZ());
				}
			}
			$this->player->sendTitle(iLand::getLanguage()->translateString("title.selectland.complete1"), iLand::getLanguage()->translateString("title.selectland.complete2", [ItemUtils::getItem()->getName()]));
		}
	}
	public function addBorder(
		Player $player,
		float $x,
		float $y,
		float $z) : void
	{
		$packet = new SpawnParticleEffectPacket();
		$packet->position = new Vector3($x, $y, $z);
		$packet->particleName = "minecraft:villager_happy";
		$packet->molangVariablesJson = '';
		$player->getNetworkSession()->sendDataPacket($packet);
	}
}
