<?php

namespace davidglitch04\iLand\Command;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\BaseSubCommand;
use davidglitch04\iLand\Command\SubCommands\Buy;
use davidglitch04\iLand\Form\iLandForm;
use davidglitch04\iLand\iLand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class iLandCommand extends BaseCommand
{
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $subcommands = array_values(array_map(function (BaseSubCommand $subCommand): string {
            return $subCommand->getName();
        }, $this->getSubCommands()));
        if(!isset($args["args"]) and $sender instanceof Player){
            new iLandForm($sender);
        } elseif($args["args"] == "set" and $sender instanceof Player){
            if(!iLand::getInstance()->getSessionManager()->inSession($sender)){
                $sender->sendTip(iLand::getLanguage()->translateString("title.rangeselector.fail.outmode"));
                return;
            } else{
                if(iLand::getInstance()->getSessionManager()->getSession($sender)->isNull("A")){
                    iLand::getInstance()->getSessionManager()->getSession($sender)->setPositionA($sender->getPosition());
                    $sender->sendTip(iLand::getLanguage()->translateString("title.rangeselector.pointed", [
                        "A", 
                        $sender->getWorld()->getFolderName(), 
                        $sender->getLocation()->getX(), 
                        $sender->getLocation()->getY(), 
                        $sender->getLocation()->getZ()])
                    );
                } elseif(iLand::getInstance()->getSessionManager()->getSession($sender)->isNull("B")){
                    iLand::getInstance()->getSessionManager()->getSession($sender)->setPositionB($sender->getPosition());
                    $sender->sendTip(iLand::getLanguage()->translateString("title.rangeselector.pointed", [
                        "B", 
                        $sender->getWorld()->getFolderName(), 
                        $sender->getLocation()->getX(), 
                        $sender->getLocation()->getY(), 
                        $sender->getLocation()->getZ()])
                    );
                }
            }
        }
        $sender->sendMessage("Usage: /land <" . implode("|", $subcommands) . ">");
    }

    protected function prepare(): void{
        $this->registerArgument(0, new RawStringArgument("args", true));
        $this->registerArgument(1, new RawStringArgument("select", true));
        $this->registerSubCommand(new Buy("buy", iLand::getLanguage()->translateString("Buy the selected land")));
    }
}
