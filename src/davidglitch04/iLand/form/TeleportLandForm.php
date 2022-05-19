<?php

declare(strict_types=1);

namespace davidglitch04\iLand\form;

use davidglitch04\iLand\iLand;
use davidglitch04\iLand\libs\Vecnavium\FormsUI\SimpleForm;
use davidglitch04\iLand\utils\DataUtils;
use pocketmine\player\Player;

class TeleportLandForm {
	public function __construct(Player $player) {
		$this->openForm($player);
	}


	private function openForm(Player $player) : void {
		$language = iLand::getLanguage();
		$form = new SimpleForm(function (Player $player, int|null $data) use ($language) {
			if (!isset($data)) {
				return;
			}
			$dataland = DataUtils::decode(iLand::getInstance()->getProvider()->getData($player)[$data + 1]);
			$position = iLand::getInstance()->getLandManager()->StringToPosition($dataland["Spawn"]);
			$position->getWorld()->loadChunk($position->getX(), $position->getZ());
			$player->sendTip($language->translateString("api.safetp.tping.talk"));
			$player->sendTitle($language->translateString("api.safetp.talk.pleasewait"), $language->translateString("api.safetp.tping.foundfoothold"));
			$player->teleport($position);
		});
		$form->setTitle($language->translateString("gui.landtp.title"));
		$form->setContent($language->translateString("gui.landtp.tip"));
		foreach (iLand::getInstance()->getProvider()->getData($player) as $key => $data) {
			$form->addButton($data["Name"], 0, "textures/iLand/selectLand");
		}
		$player->sendForm($form);
	}
}