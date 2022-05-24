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
use davidglitch04\iLand\task\TitleTask;
use pocketmine\player\Player;

class NewLandForm {
	public function __construct(Player $player) {
		$this->sendForm($player);
	}

	private function sendForm(Player $player) : void {
		$form = new SimpleForm(function (Player $player, int|null $data) {
			if (!isset($data)) {
				return;
			}
			if ($data === 0) {
				iLand::getInstance()->getSessionManager()->addPlayer($player);
				iLand::getInstance()->getScheduler()->scheduleRepeatingTask(new TitleTask($player), 20);
			} else {
				new iLandForm($player);
			}
		});
		$language = iLand::getLanguage();
		$form->setTitle($language->translateString('gui.buyland.title'));
		$form->addButton($language->translateString('gui.buyland.start'));
		$form->addButton($language->translateString('gui.general.back'));
		$player->sendForm($form);
	}
}
