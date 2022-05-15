<?php

declare(strict_types=1);

namespace davidglitch04\iLand\form;

use davidglitch04\iLand\economy\EconomyManager;
use davidglitch04\iLand\iLand;
use davidglitch04\iLand\libs\Vecnavium\FormsUI\CustomForm;
use davidglitch04\iLand\libs\Vecnavium\FormsUI\SimpleForm;
use pocketmine\player\Player;
use pocketmine\Server;

use function abs;
use function count;
use function floatval;
use function in_array;
use function is_null;
use function strcmp;
use function strtolower;

class ManageLandForm {
	public function __construct(Player $player) {
		$this->openForm($player);
	}

	private function openForm(Player $player) : void {
		$language = iLand::getLanguage();
		$form = new SimpleForm(function (Player $player, int|null $data) {
			if (!isset($data)) {
				return;
			}
			$this->Mgr($player, $data + 1);
		});
		$form->setTitle($language->translateString("gui.landmgr.title"));
		$form->setContent($language->translateString("gui.landmgr.select"));
		foreach (iLand::getInstance()->getProvider()->getAllLand() as $key => $data) {
			if (strcmp($data["Owner"], $player->getName()) == 0) {
				$form->addButton($data["Name"], 0, "textures/iLand/selectLand");
			}
		}
		$player->sendForm($form);
	}

	private function Mgr(Player $player, int $key) {
		$language = iLand::getLanguage();
		$dataland = iLand::getInstance()->getProvider()->getAllLand()[$key];
		$form = new SimpleForm(function (Player $player, int|null $data) use ($key) {
			if (!isset($data)) {
				return false;
			}
			switch ($data) {
				case 0:
					$this->LandInfo($player, $key);
					break;
				case 1:
					$this->Permission($player, $key);
					break;
				case 2:
					$this->LandTrust($player, $key);
					break;
				case 3:
					$this->LandNickname($player, $key);
					break;
				case 4:
					$this->LandTransfer($player, $key);
					break;
				case 5:
					$this->DeleteLand($player, $key);
					break;
			}
		});
		$form->setTitle($language->translateString("gui.fastlmgr.title"));
		$form->setContent($language->translateString("gui.fastlmgr.content", [$dataland["Name"]]));
		$form->addButton($language->translateString("gui.landmgr.options.landinfo"));
		$form->addButton($language->translateString("gui.landmgr.options.landperm"));
		$form->addButton($language->translateString("gui.landmgr.options.landtrust"));
		$form->addButton($language->translateString("gui.landmgr.options.landtag"));
		$form->addButton($language->translateString("gui.landmgr.options.landtransfer"));
		$form->addButton($language->translateString("gui.landmgr.options.delland"));
		$form->addButton($language->translateString("gui.general.close"));
		$player->sendForm($form);
	}

	private function LandInfo(Player $player, int $key) : void {
		$language = iLand::getLanguage();
		$dataland = iLand::getInstance()->getProvider()->getAllLand()[$key];
		$form = new SimpleForm(function (Player $player, int|null $data) {
			if (!isset($data)) {
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

	private function Permission(Player $player, int $key) : void {
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
		$form = new CustomForm(function (Player $player, array|null $data) use ($key, $alltoggle) {
			if (!isset($data)) {
				return;
			}
			$landdb = iLand::getInstance()->getProvider()->getData($key);
			for ($i = 0;$i < count($alltoggle);$i++) {
				if ($data != 0) {
					$landdb["Settings"][$alltoggle[$i]] = $data[$i + 1];
				}
			}
			iLand::getInstance()->getProvider()->setData($key, $landdb);
		});
		$form->setTitle($language->translateString("gui.landmgr.landperm.title"));
		$form->addLabel($language->translateString("gui.landmgr.landperm.options.title"));
		foreach ($alltoggle as $toggle) {
			$form->addToggle($language->translateString("gui.landmgr.landperm." . $toggle), $dataland["Settings"][$toggle]);
		}
		$form->addLabel($language->translateString("gui.landmgr.landperm.editevent"));
		$player->sendForm($form);
	}

	private function LandTrust(Player $player, int $key) : void {
		$language = iLand::getLanguage();
		$dataland = iLand::getInstance()->getProvider()->getAllLand()[$key];
		$form = new SimpleForm(function (Player $player, int|null $data) use ($key) {
			if (!isset($data)) {
				return;
			}
			if ($data === 0) {
				$this->addTrust($player, $key);
			} elseif ($data === 1) {
				$this->rmTrust($player, $key);
			}
		});
		$content = $language->translateString("gui.landtrust.tip");
		$form->setTitle($language->translateString("gui.landtrust.title"));
		$form->addButton($language->translateString("gui.landtrust.addtrust"));
		if (count($dataland['Members']) >= 1) {
			$form->addButton($language->translateString("gui.landtrust.rmtrust"));
			$content .= "\n" . $language->translateString("gui.landtrust.trusted") . " ";
			foreach ($dataland['Members'] as $trust) {
				$content .= "," . $trust;
			}
		}
		$form->setContent($content);
		$player->sendForm($form);
	}

	private function addTrust(Player $player, int $key) : void {
		$language = iLand::getLanguage();
		$form = new CustomForm(function (Player $player, array|null $data) use ($key, $language) {
			if (!isset($data)) {
				return;
			}
			if (isset($data[1])) {
				$landdb = iLand::getInstance()->getProvider()->getData($key);
				if (in_array($data[1], $landdb['Members'], true)) {
					$player->sendMessage($language->translateString("gui.landtrust.fail.alreadyexists"));
					return;
				}
				if (strtolower($data[1]) == strtolower($player->getName())) {
					$player->sendMessage($language->translateString("gui.landtrust.fail.cantaddown"));
					return;
				}
				$landdb['Members'][] = strtolower($data[1]);
				iLand::getInstance()->getProvider()->setData($key, $landdb);
				$player->sendMessage($language->translateString("gui.landtrust.addsuccess"));
			} else {
				$this->addTrust($player, $key);
				return;
			}
		});
		$form->setTitle($language->translateString("gui.itemselector.trust.title"));
		$form->addLabel("§lInstructions " . $language->translateString("gui.itemselector.trust.tip_usage"));
		$form->addInput($language->translateString("gui.itemselector.trust.search"), "Player Username");
		$player->sendForm($form);
	}

	private function rmTrust(Player $player, int $key) : void {
		$language = iLand::getLanguage();
		$dataland = iLand::getInstance()->getProvider()->getAllLand()[$key];
		$form = new CustomForm(function (Player $player, array|null $data) use ($key, $language) {
			if (!isset($data)) {
				return;
			}
			if (isset($data[1])) {
				$landdb = iLand::getInstance()->getProvider()->getData($key);
				unset($landdb['Members'][$data[1]]);
				iLand::getInstance()->getProvider()->setData($key, $landdb);
				$player->sendMessage($language->translateString("gui.landtrust.rmsuccess"));
			} else {
				$this->rmTrust($player, $key);
				return;
			}
		});
		$form->setTitle($language->translateString("gui.itemselector.trust.title"));
		$form->addLabel("§lInstructions " . $language->translateString("gui.itemselector.trust.tip_usage"));
		$form->addDropdown($language->translateString("gui.itemselector.trust.select"), $dataland['Members']);
		$player->sendForm($form);
	}

	private function LandNickname(Player $player, int $key) : void {
		$language = iLand::getLanguage();
		$dataland = iLand::getInstance()->getProvider()->getAllLand()[$key];
		$form = new CustomForm(function (Player $player, array|null $data) use ($key) {
			if (!isset($data)) {
				return;
			}
			if (isset($data[1])) {
				$landdb = iLand::getInstance()->getProvider()->getData($key);
				$landdb["Name"] = $data[1];
				iLand::getInstance()->getProvider()->setData($key, $landdb);
				$this->CompleteForm($player);
			}
		});
		$form->setTitle($language->translateString("gui.landtag.title"));
		$form->addLabel($language->translateString("gui.landtag.tip"));
		$form->addInput("", $dataland["Name"]);
		$player->sendForm($form);
	}

	private function LandTransfer(Player $player, int $key) : void {
		$language = iLand::getLanguage();
		$form = new SimpleForm(function (Player $player, int|null $data) use ($key, $language) {
			if (!isset($data)) {
				return;
			}
			if ($data === 0) {
				$form = new CustomForm(function (Player $player, array|null $data) use ($key, $language) {
					if (!isset($data)) {
						return;
					}
					if (isset($data[1])) {
						if (!is_null(Server::getInstance()->getPlayerByPrefix($data[1]))) {
							$landdb = iLand::getInstance()->getProvider()->getData($key);
							$landdb["Owner"] = Server::getInstance()->getPlayerByPrefix($data[1])->getName();
							iLand::getInstance()->getProvider()->setData($key, $landdb);
							$this->CompleteForm($player);
						} else {
							$player->sendMessage($language->translateString("gui.general.player"));
							return;
						}
					}
				});
				$form->setTitle($language->translateString("gui.itemselector.transfer.title"));
				$form->addLabel("§lInstructions " . $language->translateString("gui.itemselector.transfer.tip_usage"));
				$form->addInput($language->translateString("gui.itemselector.transfer.search"), "Player Username");
				$player->sendForm($form);
			}
		});
		$form->setTitle($language->translateString("gui.landmgr.options.landtransfer"));
		$form->setContent($language->translateString("gui.landtransfer.tip"));
		$form->addButton($language->translateString("gui.general.yes"));
		$form->addButton($language->translateString("gui.general.close"));
		$player->sendForm($form);
	}

	private function DeleteLand(Player $player, int $key) : void {
		$language = iLand::getLanguage();
		$dataland = iLand::getInstance()->getProvider()->getAllLand()[$key];
		$startpos = iLand::getInstance()->getProvider()->StringToPosition($dataland["Start"]);
		$endpos = iLand::getInstance()->getProvider()->StringToPosition($dataland["End"]);
		$length = abs((int) $startpos->getX() - (int) $endpos->getX());
		$width = abs((int) $startpos->getZ() - (int) $endpos->getZ());
		$priceperblock = iLand::getDefaultConfig()->get("recycle/area");
		$blocks = $length * $width;
		$price = $priceperblock * $blocks;
		$form = new SimpleForm(function (Player $player, int|null $data) use ($key, $language, $price) {
			if (!isset($data)) {
				return;
			}
			if ($data === 0) {
				$ecomgr = new EconomyManager();
				$ecomgr->addMoney($player, floatval($price));
				iLand::getInstance()->getProvider()->delLand($key);
				$this->CompleteForm($player);
			}
		});
		$form->setTitle($language->translateString("gui.delland.title"));
		$form->setContent($language->translateString("gui.delland.content", [$price]));
		$form->addButton($language->translateString("gui.general.yes"));
		$form->addButton($language->translateString("gui.general.close"));
		$player->sendForm($form);
	}

	private function CompleteForm(Player $player) : void {
		$language = iLand::getLanguage();
		$form = new SimpleForm(function (Player $player, int|null $data) {
			if (!isset($data)) {
				return;
			}
		});
		$form->setTitle($language->translateString("gui.general.complete"));
		$form->setContent($language->translateString("gui.general.complete.content"));
		$form->addButton($language->translateString("gui.general.close"));
		$player->sendForm($form);
	}
}
