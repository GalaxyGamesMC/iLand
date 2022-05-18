<?php

declare(strict_types=1);

namespace davidglitch04\iLand\command\SubCommands;

use davidglitch04\iLand\form\NewLandForm;
use davidglitch04\iLand\iLand;
use davidglitch04\iLand\libs\CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class NewLand extends BaseSubCommand {

	protected function prepare() : void {
	}


	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		if ($sender instanceof Player) {
			$session = iLand::getInstance()->getSessionManager();
			$language = iLand::getLanguage();
			if ($session->inSession($sender)) {
				$sender->sendTip($language->translateString("talk.invalidaction"));
				return;
			} else {
				new NewLandForm($sender);
			}
		}
	}
}
