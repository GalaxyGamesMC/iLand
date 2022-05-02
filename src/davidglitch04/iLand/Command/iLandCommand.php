<?php

namespace davidglitch04\iLand\Command;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;

class iLandCommand extends BaseCommand
{
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        //TODO:
        //If form is disable player must use command else sendForm to Player
    }

    public function prepare(): void
    {
        //TODO:
        //Register args
    }
}
