<?php

namespace davidglitch04\iLand\Command;

use davidglitch04\iLand\iLand;
use davidglitch04\iLand\Form\iLandForm;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat as TF;

class iLandCommand extends Command implements PluginOwned
{

    protected iland $iland;

    public function __construct(iLand $iland)
    {
        $this->iland = $iland;
        parent::__construct("land");
        $this->setDescription("iLand control panel");
        $this->setAliases(["iland"]);
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->iland;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($sender instanceof Player){
            $form = new iLandForm($this->getOwningPlugin());
            $form->mainForm($sender);
        }else{
            $sender->sendMessage(TF::RED . "Please use command in-game !");
        }
    }
}
