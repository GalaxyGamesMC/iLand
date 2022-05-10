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
            $this->Mgr($player, $data + 1);
        });
        $form->setTitle($language->translateString("gui.landmgr.title"));
        $form->setContent($language->translateString("gui.landmgr.select"));
        foreach (iLand::getInstance()->getProvider()->getAllLand() as $key => $data){
            if (strcmp($data["Owner"], $player->getName()) == 0){
                $form->addButton($data["Name"]);
            }
        }
        $player->sendForm($form);
        return $form;
    }

    private function Mgr(Player $player, int $key){
        $language = iLand::getLanguage();
        $dataland = iLand::getInstance()->getProvider()->getAllLand()[$key];
        $form = new SimpleForm(function (Player $player, $data) use ($dataland){
            if(!isset($data)){
                return false;
            }
            switch ($data){
                case 0:
                    $this->LandInfo($player, $dataland);
                    break;
            }
        });
        $form->setTitle($language->translateString("gui.fastlmgr.title"));
        $form->setContent($language->translateString("gui.fastlmgr.content", [$dataland["Name"]]));
        $form->addButton($language->translateString("gui.landmgr.options.landinfo"));
        $form->addButton($language->translateString("gui.landmgr.options.landcfg"));
        $form->addButton($language->translateString("gui.landmgr.options.landperm"));
        $form->addButton($language->translateString("gui.landmgr.options.landtrust"));
        $form->addButton($language->translateString("gui.landmgr.options.landtag"));
        $form->addButton($language->translateString("gui.landmgr.options.landdescribe"));
        $form->addButton($language->translateString("gui.landmgr.options.landtransfer"));
        $form->addButton($language->translateString("gui.landmgr.options.delland"));
        $form->addButton($language->translateString("gui.general.close"));
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
        $length = abs((int)$start->getX() - (int)$end->getX());
        $width = abs((int)$start->getZ() - (int)$end->getZ());
        $params = [
            $data["Owner"], 
            $data["Name"], 
            $start->getWorld()->getFolderName(), 
            $start->getX() . "/" . $start->getZ(),
            $end->getX() . "/" . $end->getZ(),
            $length . "/" . $width,
            $length * $width
        ];
        $form->setContent($language->translateString("gui.landmgr.landinfo.content", $params));
        $form->addButton($language->translateString("gui.general.close"));
        $player->sendForm($form);
        return $form;
    }


}