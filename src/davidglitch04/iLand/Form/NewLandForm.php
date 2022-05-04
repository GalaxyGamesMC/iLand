<?php

namespace davidglitch04\iLand\Form;

use davidglitch04\iLand\iLand;
use davidglitch04\iLand\Task\TitleTask;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class NewLandForm
{
    /**
     * @param Player $player
     */
    public function __construct(Player $player)
    {
        $this->sendForm($player);
    }
    /**
     * @param  Player $player
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
                iLand::getInstance()->getScheduler()->scheduleRepeatingTask(new TitleTask($player), 1);
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
