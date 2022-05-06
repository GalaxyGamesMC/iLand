<?php

namespace davidglitch04\iLand\Command\SubCommands;

use CortexPE\Commando\BaseSubCommand;
use davidglitch04\iLand\Form\BuyForm;
use davidglitch04\iLand\iLand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class Buy extends BaseSubCommand{

    protected function prepare(): void
    {
        
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if($sender instanceof Player){
            $session = iLand::getInstance()->getSessionManager();
            $language = iLand::getLanguage();
            if(!$session->inSession($sender)){
                $sender->sendTip($language->translateString("talk.invalidaction"));
                return;
            } else{
                $statusA = $session->getSession($sender)->isNull("A");
                $statusB = $session->getSession($sender)->isNull("B");
                if($statusA and $statusB){
                    $sender->sendTip($language->translateString("talk.invalidaction"));
                    return;
                } else{
                    new BuyForm($sender);
                }
            }
        }
    }
}