<?php

namespace davidglitch04\iLand\Form;

use davidglitch04\iLand\iLand;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class CreateLandForm
{
    public function __construct(Player $player)
    {
        $this->sendForm($player);
    }

    private function sendForm(Player $player)
    {
        $form = new SimpleForm(function (Player $player, $data) {
            if (!isset($data)) {
                return false;
            }
            if ($data === 0) {
                iLand::getInstance()->getSessionManager()->addPlayer($player);
            }
        });
        $form->setTitle('Create Land');
        $form->addButton('Start Create');
        $player->sendForm($form);

        return $form;
    }
}
