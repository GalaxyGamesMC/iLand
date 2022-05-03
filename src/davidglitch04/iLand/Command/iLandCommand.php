<?php

namespace davidglitch04\iLand\Command;

use davidglitch04\iLand\Form\iLandForm;
use davidglitch04\iLand\iLand;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

class iLandCommand extends Command implements PluginOwned
{
    /**@var iLand $iland */
    protected iLand $iland;

    /**
     * @param iLand $iland
     */
    public function __construct(iLand $iland)
    {
        $this->iland = $iland;
        parent::__construct('land');
        $this->setDescription('iLand control panel');
        $this->setAliases(['iland']);
    }

    /**
     * @return Plugin
     */
    public function getOwningPlugin(): Plugin
    {
        return $this->iland;
    }

    /**
     * @param  CommandSender $sender
     * @param  string        $commandLabel
     * @param  array         $args
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($sender instanceof Player) {
            new iLandForm($sender);
        } else {
            $sender->sendMessage(iLand::getLanguage()->translateString('use.ingame'));
        }
    }
}
