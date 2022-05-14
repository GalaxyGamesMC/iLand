<?php

declare(strict_types=1);

namespace davidglitch04\iLand\form;

use davidglitch04\iLand\iLand;
use davidglitch04\iLand\libs\Vecnavium\FormsUI\SimpleForm;
use davidglitch04\iLand\task\TitleTask;
use pocketmine\player\Player;

class NewLandForm
{

	public function __construct(Player $player)
	{
		$this->sendForm($player);
	}
	/**
	 * @return mixed
	 */
	private function sendForm(Player $player)
	{
		$form = new SimpleForm(function (Player $player, $data) {
			if (!isset($data)) {
				return new iLandForm($player);
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

		return $form;
	}
}
