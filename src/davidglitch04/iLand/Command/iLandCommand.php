<?php

namespace davidglitch04\iLand\Command;

use davidglitch04\iLand\Command\SubCommands\Buy;
use davidglitch04\iLand\Form\BuyForm;
use davidglitch04\iLand\Form\iLandForm;
use davidglitch04\iLand\iLand;
use davidglitch04\iLand\Libs\CortexPE\Commando\args\RawStringArgument;
use davidglitch04\iLand\Libs\CortexPE\Commando\BaseCommand;
use davidglitch04\iLand\Libs\CortexPE\Commando\BaseSubCommand;
use davidglitch04\iLand\Libs\Vecnavium\FormsUI\SimpleForm;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class iLandCommand extends BaseCommand
{
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $subcommands = array_values(array_map(function (BaseSubCommand $subCommand): string {
            return $subCommand->getName();
        }, $this->getSubCommands()));
        if (!isset($args['args']) and $sender instanceof Player) {
            new iLandForm($sender);
        } elseif ($args['args'] == 'set' and $sender instanceof Player) {
            if (!iLand::getInstance()->getSessionManager()->inSession($sender)) {
                $sender->sendTip(iLand::getLanguage()->translateString('title.rangeselector.fail.outmode'));

                return;
            } else {
                $x = $sender->getPosition()->getX();
                $z = $sender->getPosition()->getZ();
                $statusA = iLand::getInstance()->getSessionManager()->getSession($sender)->isNull("A");
                $statusB = iLand::getInstance()->getSessionManager()->getSession($sender)->isNull("B");
                if (iLand::getInstance()->getProvider()->isOverlap($x, $z, $x, $z, $sender->getWorld())) {
                    $form = new SimpleForm(function (Player $sender, $data){
                        if(!isset($data)){
                            return false;
                        }
                    });
                    $form->setTitle(iLand::getLanguage()->translateString("gui.overlap.title"));
                    $form->setContent(iLand::getLanguage()->translateString("gui.overlap.content"));
                    $form->addButton(iLand::getLanguage()->translateString("gui.general.close"));
                    $sender->sendForm($form);
                    return;
                }
                if(!$statusA and !$statusB){
                    new BuyForm($sender);
                    return;
                }
                $sender->sendTip(iLand::getLanguage()->translateString('title.rangeselector.pointed', [
                    iLand::getInstance()->getSessionManager()->getSession($sender)->setNextPosition($sender->getPosition()),
                    $sender->getWorld()->getFolderName(),
                    $sender->getLocation()->getX(),
                    $sender->getLocation()->getY(),
                    $sender->getLocation()->getZ(), ])
                );
            }
        } else{
            $sender->sendMessage('Usage: /land <'.implode('|', $subcommands).'>');
        }
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument('args', true));
        $this->registerSubCommand(new Buy('buy', iLand::getLanguage()->translateString('Buy the selected land')));
    }
}
