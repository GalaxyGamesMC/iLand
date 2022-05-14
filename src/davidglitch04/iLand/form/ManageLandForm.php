<?php

declare(strict_types=1);

namespace davidglitch04\iLand\form;

use davidglitch04\iLand\iLand;
use davidglitch04\iLand\libs\Vecnavium\FormsUI\CustomForm;
use davidglitch04\iLand\libs\Vecnavium\FormsUI\SimpleForm;
use pocketmine\player\Player;
use function abs;
use function array_search;
use function count;
use function in_array;
use function strcmp;
use function strtolower;

class ManageLandForm{

	public function __construct(Player $player)
	{
		$this->openForm($player);
	}

	private function openForm(Player $player)
	{
		$language = iLand::getLanguage();
		$form = new SimpleForm(function (Player $player, $data){
			if(!isset($data)){
				return false;
			}
			$this->Mgr($player, $data + 1);
		});
		$form->setTitle($language->translateString("gui.landmgr.title"));
		$form->setContent($language->translateString("gui.landmgr.select"));
		foreach (iLand::getInstance()->getProvider()->getAllLand() as $key => $data){
			if (strcmp($data["Owner"], $player->getName()) == 0){
				$form->addButton($data["Name"]);
			}
		}
		$player->sendForm($form);
		return $form;
	}

	private function Mgr(Player $player, int $key){
		$language = iLand::getLanguage();
		$dataland = iLand::getInstance()->getProvider()->getAllLand()[$key];
		$form = new SimpleForm(function (Player $player, $data) use ($key){
			if(!isset($data)){
				return false;
			}
			switch ($data){
				case 0:
					$this->LandInfo($player, $key);
					break;
				case 1:
					$this->Permission($player, $key);
					break;
				case 2:
					$this->LandTrust($player, $key);
					break;
			}
		});
		$form->setTitle($language->translateString("gui.fastlmgr.title"));
		$form->setContent($language->translateString("gui.fastlmgr.content", [$dataland["Name"]]));
		$form->addButton($language->translateString("gui.landmgr.options.landinfo"));
		$form->addButton($language->translateString("gui.landmgr.options.landperm"));
		$form->addButton($language->translateString("gui.landmgr.options.landtrust"));
		$form->addButton($language->translateString("gui.landmgr.options.landtag"));
		$form->addButton($language->translateString("gui.landmgr.options.landdescribe"));
		$form->addButton($language->translateString("gui.landmgr.options.landtransfer"));
		$form->addButton($language->translateString("gui.landmgr.options.delland"));
		$form->addButton($language->translateString("gui.general.close"));
		$player->sendForm($form);
	}

	private function LandInfo(Player $player, int $key) : void{
		$language = iLand::getLanguage();
		$dataland = iLand::getInstance()->getProvider()->getAllLand()[$key];
		$form = new SimpleForm(function (Player $player, $data){
			if(!isset($data)){
				return;
			}
		});
		$start = iLand::getInstance()->getProvider()->StringToPosition($dataland["Start"]);
		$end = iLand::getInstance()->getProvider()->StringToPosition($dataland["End"]);
		$form->setTitle($language->translateString("gui.landmgr.landinfo.title"));
		$length = abs((int) $start->getX() - (int) $end->getX());
		$width = abs((int) $start->getZ() - (int) $end->getZ());
		$params = [
			$dataland["Owner"],
			$dataland["Name"],
			$start->getWorld()->getFolderName(),
			$start->getX() . "/" . $start->getZ(),
			$end->getX() . "/" . $end->getZ(),
			$length . "/" . $width,
			$length * $width
		];
		$form->setContent($language->translateString("gui.landmgr.landinfo.content", $params));
		$form->addButton($language->translateString("gui.general.close"));
		$player->sendForm($form);
	}

	private function Permission(Player $player, int $key) : void{
		$language = iLand::getLanguage();
		$alltoggle = [
			0 => "allow_open_chest",
			1 => "use_bucket",
			2 => "use_furnace",
			3 => "allow_place",
			4 => "allow_dropitem",
			5 => "allow_pickupitem",
			6 => "allow_destroy"
		];
		$dataland = iLand::getInstance()->getProvider()->getAllLand()[$key];
		$form = new CustomForm(function (Player $player, $data) use ($key, $alltoggle){
			if(!isset($data)){
				return;
			}
			$landdb = iLand::getInstance()->getProvider()->getData($key);
			for ($i = 0;$i < count($alltoggle);$i++){
				if($data != 0){
					$landdb["Settings"][$alltoggle[$i]] = $data[$i + 1];
				}
			}
			iLand::getInstance()->getProvider()->setData($key, $landdb);
		});
		$form->setTitle($language->translateString("gui.landmgr.landperm.title"));
		$form->addLabel($language->translateString("gui.landmgr.landperm.options.title"));
		foreach($alltoggle as $toggle){
			$form->addToggle($language->translateString("gui.landmgr.landperm." . $toggle), $dataland["Settings"][$toggle]);
		}
		$form->addLabel($language->translateString("gui.landmgr.landperm.editevent"));
		$player->sendForm($form);
	}

	private function LandTrust(Player $player, int $key) : void{
		$language = iLand::getLanguage();
		$dataland = iLand::getInstance()->getProvider()->getAllLand()[$key];
		$form = new SimpleForm(function (Player $player, $data) use ($key){
			if (!isset($data)){
				return;
			}
			if ($data === 0){
				$this->addTrust($player, $key);
			} elseif ($data === 1){
				$this->rmTrust($player, $key);
			}
		});
		$content = $language->translateString("gui.landtrust.tip");
		$form->setTitle($language->translateString("gui.landtrust.title"));
		$form->addButton($language->translateString("gui.landtrust.addtrust"));
		if (count($dataland['Members']) >= 1){
			$form->addButton($language->translateString("gui.landtrust.rmtrust"));
			$content .= "\n" . $language->translateString("gui.landtrust.trusted") . " ";
			foreach ($dataland['Members'] as $trust){
				$content .= "," . $trust;
			}
		}
		$form->setContent($content);
		$player->sendForm($form);
	}

	private function addTrust(Player $player, int $key) : void{
		$language = iLand::getLanguage();
		$form = new CustomForm(function (Player $player, $data) use ($key, $language){
			if (!isset($data)){
				return;
			}
			if (isset($data[1])){
				$landdb = iLand::getInstance()->getProvider()->getData($key);
				if (in_array($data[1], $landdb['Members'], true)){
					$player->sendMessage($language->translateString("gui.landtrust.fail.alreadyexists"));
					return;
				}
				if (strtolower($data[1]) == strtolower($player->getName())){
					$player->sendMessage($language->translateString("gui.landtrust.fail.cantaddown"));
					return;
				}
				$landdb['Members'][] = strtolower($data[1]);
				iLand::getInstance()->getProvider()->setData($key, $landdb);
				$player->sendMessage($language->translateString("gui.landtrust.addsuccess"));
			} else{
				$this->addTrust($player, $key);
				return;
			}
		});
		$form->setTitle($language->translateString("gui.itemselector.trust.title"));
		$form->addLabel("§lInstructions " . $language->translateString("gui.itemselector.trust.tip_usage"));
		$form->addInput($language->translateString("gui.itemselector.trust.search"), "Player Username");
		$player->sendForm($form);
	}

	private function rmTrust(Player $player, int $key) : void{
		$language = iLand::getLanguage();
		$dataland = iLand::getInstance()->getProvider()->getAllLand()[$key];
		$form = new CustomForm(function (Player $player, $data) use ($key, $language){
			if (!isset($data)){
				return;
			}
			if (isset($data[1])){
				$landdb = iLand::getInstance()->getProvider()->getData($key);
				$name = $landdb['Members'][$data[1]];
				unset($landdb['Members'][array_search($name, $landdb['Members'], true)]);
				iLand::getInstance()->getProvider()->setData($key, $landdb);
				$player->sendMessage($language->translateString("gui.landtrust.rmsuccess"));
			} else{
				$this->rmTrust($player, $key);
				return;
			}
		});
		$form->setTitle($language->translateString("gui.itemselector.trust.title"));
		$form->addLabel("§lInstructions " . $language->translateString("gui.itemselector.trust.tip_usage"));
		$form->addDropdown($language->translateString("gui.itemselector.trust.select"), $dataland['Members']);
		$player->sendForm($form);
	}
}
