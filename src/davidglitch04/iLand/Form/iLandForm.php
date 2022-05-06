<?php

declare(strict_types = 1);

namespace davidglitch04\iLand\form;

use davidglitch04\iLand\iLand;
use davidglitch04\iLand\libs\Vecnavium\FormsUI\SimpleForm;
use pocketmine\player\Player;

class iLandForm
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
                return true;
            }
            switch ($data) {
                case 0:
                    new NewLandForm($player);
                break;
                case 1:
                   // new ManageLandForm($player);
                break;
                case 2:
                   // new TeleportLandForm($player);
                break;
            }
        });
        $language = iLand::getLanguage();
        $form->setTitle($language->translateString('gui.fastgde.title'));
        $form->setContent($language->translateString('gui.fastgde.content', [iLand::getInstance()->getProvider()->CountLand($player)]));
        $form->addButton($language->translateString('gui.fastgde.create'));
        $form->addButton($language->translateString('gui.fastgde.manage'));
        $form->addButton($language->translateString('gui.fastgde.landtp'));
        $form->addButton($language->translateString('gui.general.back'));
        $player->sendForm($form);
    }
}
