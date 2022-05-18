<?php

declare(strict_types=1);

namespace davidglitch04\iLand\command;

use davidglitch04\iLand\command\SubCommands\Buy;
use davidglitch04\iLand\command\SubCommands\Mgr;
use davidglitch04\iLand\command\SubCommands\NewLand;
use davidglitch04\iLand\command\SubCommands\Tp;
use davidglitch04\iLand\form\BuyForm;
use davidglitch04\iLand\form\iLandForm;
use davidglitch04\iLand\iLand;
use davidglitch04\iLand\libs\CortexPE\Commando\args\RawStringArgument;
use davidglitch04\iLand\libs\CortexPE\Commando\BaseCommand;
use davidglitch04\iLand\libs\CortexPE\Commando\BaseSubCommand;
use davidglitch04\iLand\libs\Vecnavium\FormsUI\SimpleForm;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use function array_map;
use function array_values;
use function implode;

class iLandCommand extends BaseCommand {

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		$subcommands = array_values(array_map(function (BaseSubCommand $subCommand) : string {
			return $subCommand->getName();
		}, $this->getSubCommands()));
		if (!isset($args['args']) && $sender instanceof Player) {
			new iLandForm($sender);
		} elseif ($args['args'] == 'set' && $sender instanceof Player) {
			if (!iLand::getInstance()->getSessionManager()->inSession($sender)) {
				$sender->sendTip(iLand::getLanguage()->translateString('title.rangeselector.fail.outmode'));

				return;
			} else {
				$x = $sender->getPosition()->getX();
				$z = $sender->getPosition()->getZ();
				$statusA = iLand::getInstance()->getSessionManager()->getSession($sender)->isNull("A");
				$statusB = iLand::getInstance()->getSessionManager()->getSession($sender)->isNull("B");
				if (iLand::getInstance()->getProvider()->isOverlap($sender->getPosition())) {
					$form = new SimpleForm(function (Player $sender, int|null $data) {
						if (!isset($data)) {
							return false;
						}
					});
					$form->setTitle(iLand::getLanguage()->translateString("gui.overlap.title"));
					$form->setContent(iLand::getLanguage()->translateString("gui.overlap.content"));
					$form->addButton(iLand::getLanguage()->translateString("gui.general.close"));
					$sender->sendForm($form);
					return;
				}
				if (!$statusA && !$statusB) {
					new BuyForm($sender);
					return;
				}
				$sender->sendTip(iLand::getLanguage()->translateString('title.rangeselector.pointed', [
					iLand::getInstance()->getSessionManager()->getSession($sender)->setNextPosition($sender->getPosition()),
					$sender->getWorld()->getFolderName(),
					$sender->getLocation()->getX(),
					$sender->getLocation()->getY(),
					$sender->getLocation()->getZ()])
				);
			}
		} else {
			$sender->sendMessage('Usage: /land <' . implode('|', $subcommands) . '>');
		}
	}

	/**
	 * @throws \davidglitch04\iLand\libs\CortexPE\Commando\exception\ArgumentOrderException
	 */
	protected function prepare() : void {
		$this->registerArgument(0, new RawStringArgument('args', true));
		$this->setPermission("iland.allow.command");
		$this->registerSubCommand(new Buy('buy', iLand::getLanguage()->translateString("command.land_buy")));
		$this->registerSubCommand(new NewLand('new', iLand::getLanguage()->translateString("command.land_new")));
		$this->registerSubCommand(new Tp('tp', iLand::getLanguage()->translateString("command.land_tp")));
		$this->registerSubCommand(new Mgr('mgr', iLand::getLanguage()->translateString("command.land_mgr")));
	}
}
