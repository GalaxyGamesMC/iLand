<?php

/*
 *
 *   _____ _                     _
 *  |_   _| |                   | |
 *    | | | |     __ _ _ __   __| |
 *    | | | |    / _` | '_ \ / _` |
 *   _| |_| |___| (_| | | | | (_| |
 *  |_____|______\__,_|_| |_|\__,_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author DavidGlitch04
 * @link https://github.com/David-pm-pl/iLand
 *
 *
*/

declare(strict_types=1);

namespace davidglitch04\iLand\form;

use davidglitch04\iLand\iLand;
use davidglitch04\iLand\libs\Vecnavium\FormsUI\SimpleForm;
use pocketmine\player\Player;

class iLandForm {
	public function __construct(Player $player) {
		$this->sendForm($player);
	}

	private function sendForm(Player $player) : void {
		$form = new SimpleForm(function (Player $player, $data) {
			if (!isset($data)) {
				return;
			}
			switch ($data) {
				case 0:
					new NewLandForm($player);
				break;
				case 1:
					new ManageLandForm($player);
				break;
				case 2:
					new TeleportLandForm($player);
				break;
			}
		});
		$language = iLand::getLanguage();
		$form->setTitle($language->translateString('gui.fastgde.title'));
		$form->setContent($language->translateString('gui.fastgde.content', [iLand::getInstance()->getProvider()->CountLand($player)]));
		$form->addButton($language->translateString('gui.fastgde.create'), 0, "textures/iLand/newLand");
		$form->addButton($language->translateString('gui.fastgde.manage'), 0, "textures/iLand/mgrLand");
		$form->addButton($language->translateString('gui.fastgde.landtp'), 0, "textures/iLand/tpLand");
		$form->addButton($language->translateString('gui.general.back'));
		$player->sendForm($form);
	}
}
