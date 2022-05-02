<?php

namespace davidglitch04\iLand\Form;

use davidglitch04\iLand\iLand;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class iLandForm
{
    protected iLand $iland;

    public function __construct(iLand $iland)
    {
        $this->iland = $iland;
    }

    public function mainForm(Player $player)
    {
        $form = new SimpleForm(function (Player $player, $data) {
            if (!isset($data)) {
                return true;
            }
            switch ($data) {
                case 0:
                    new CreateLandForm($player);
                break;
                case 1:
                    new ManageLandForm($player);
                break;
                case 2:
                    new TeleportLandForm($player);
                break;
            }
        });
        $form->setTitle('iLand');
        $form->addButton('New Land');
        $form->addButton('Manage Land');
        $form->addButton('Teleport To Land');
        $form->addButton('Exit');
        $player->sendForm($form);
    }
}
