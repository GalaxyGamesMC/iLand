<?php

namespace davidglitch04\iLand\Form;

use davidglitch04\iLand\Economy\EconomyManager;
use davidglitch04\iLand\iLand;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class BuyForm{

    public function __construct(Player $player)
    {
        $this->sendForm($player);
    }

    private function sendForm(Player $player){
        $form = new SimpleForm(function (Player $player, $data){
            if(!isset($data)){
                return false;
            }
        });
        $language = iLand::getLanguage();
        $form->setTitle($language->translateString("gui.buyland.title"));
        $startpos = iLand::getInstance()->getSessionManager()->getSession($player)->getPositionA();
        $endpos = iLand::getInstance()->getSessionManager()->getSession($player)->getPositionB();
        $length = $startpos->getX() - $endpos->getX();
        $width = $startpos->getY() - $endpos->getY();
        $priceperblock = iLand::getInstance()->getConfig()->get("Price/Area");
        $blocks = $length * $width;
        $price = $priceperblock * $blocks;
        $form->setContent($language->translateString("gui.buyland.content", [$length, $width, $blocks, $price]));
        $form->addButton($language->translateString("gui.buyland.button.confirm"));
        $form->addButton($language->translateString("gui.buyland.button.cancel"));
        $player->sendForm($form);
    }
}