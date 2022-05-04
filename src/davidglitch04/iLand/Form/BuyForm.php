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
        EconomyManager::myMoney($player, static function(float $money) use ($player) : void {
            $form = new SimpleForm(function (Player $player, $data){
                if(!isset($data)){
                    return false;
                }
            });
            $money = $money;
            $language = iLand::getLanguage();
            $form->setTitle($language->translateString("gui.buyland.title"));
            $startpos = iLand::getInstance()->getSessionManager()->getSession($player)->getPositionA();
            $endpos = iLand::getInstance()->getSessionManager()->getSession($player)->getPositionB();
            $length = abs((int)$startpos->getX() - (int)$endpos->getX());
            $width = abs((int)$startpos->getZ() - (int)$endpos->getZ());
            $priceperblock = iLand::getInstance()->getConfig()->get("Price/Area");
            $blocks = $length * $width;
            $price = $priceperblock * $blocks;
            $form->setContent($language->translateString("gui.buyland.content", [$length, $width, $blocks, $price, $money]));
            $form->addButton($language->translateString("gui.buyland.button.confirm"));
            $form->addButton($language->translateString("gui.buyland.button.cancel"));
            $player->sendForm($form);
        });
    }
}