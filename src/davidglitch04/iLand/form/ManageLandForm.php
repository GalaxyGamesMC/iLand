<?php

namespace davidglitch04\iLand\form;

use davidglitch04\iLand\iLand;
use davidglitch04\iLand\libs\Vecnavium\FormsUI\SimpleForm;
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
        $language = iLand::getLanguage();
        $data = iLand::getInstance()->getProvider()->getAllLand()[$key];
        $form = new SimpleForm(function (Player $player, $data){
            if(!isset($data)){
                return false;
            }
            switch ($data){
                case 0:
                    $this->LandInfo($player, $data);
                    break;
            }
        });
        $form->setTitle($language->translateString("gui.fastlmgr.title"));
        $form->setContent($language->translateString("gui.fastlmgr.content", [$data["Name"]]));
        $form->addButton("gui.landmgr.options.landinfo");
        $form->addButton("gui.landmgr.options.landcfg");
        $form->addButton("gui.landmgr.options.landperm");
        $form->addButton("gui.landmgr.options.landtrust");
        $form->addButton("gui.landmgr.options.landtag");
        $form->addButton("gui.landmgr.options.landdescribe");
        $form->addButton("gui.landmgr.options.landtransfer");
        $form->addButton("gui.landmgr.options.delland");
        $form->addButton("gui.general.close");
        $player->sendForm($form);
    }

    private function LandInfo(Player $player, array $data){
        $language = iLand::getLanguage();
        $form = new SimpleForm(function (Player $player, $data){
            if(!isset($data)){
                return false;
            }
        });
        $start = iLand::getInstance()->getProvider()->StringToPosition($data["Start"]);
        $end = iLand::getInstance()->getProvider()->StringToPosition($data["End"]);
        $form->setTitle($language->translateString("gui.landmgr.landinfo.title"));
        $form->setContent($language->translateString("gui.landmgr.landinfo.content", [$data["Owner"], $data["Name"], $start->getWorld()->getFolderName(), $start->getX()."/".$start->getZ()]));
    }
}