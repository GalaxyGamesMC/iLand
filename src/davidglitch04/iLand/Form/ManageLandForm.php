<?php

namespace davidglitch04\iLand\Form;

use davidglitch04\iLand\iLand;
use davidglitch04\iLand\Libs\Vecnavium\FormsUI\SimpleForm;
use pocketmine\player\Player;

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
            $this->Mgr($player, $data);
        });
        $form->setTitle($language->translateString("gui.landmgr.title"));
        foreach (iLand::getInstance()->getProvider()->getAllLand() as $lands){
            if (strcmp($lands["Owner"], $player->getName()) == 0){
                $form->addButton($lands["Name"]);
            }
        }
        $player->sendForm($form);
        return $form;
    }

    private function Mgr(Player $player, int $key){
        //TODO
    }
}