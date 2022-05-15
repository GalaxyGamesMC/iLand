<?php

declare(strict_types=1);

namespace davidglitch04\iLand\economy;

use Closure;
use cooldogedev\BedrockEconomy\libs\cooldogedev\libSQL\context\ClosureContext;
use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;
use pocketmine\Server;
use function addToPlayerBalance;
use function assert;
use function ceil;
use function is_float;

final class EconomyManager {
	/** @var \pocketmine\plugin\Plugin|null $eco */
	private $eco;

	public function __construct() {
		$manager = Server::getInstance()->getPluginManager();
		$this->eco = $manager->getPlugin("EconomyAPI") ?? $manager->getPlugin("BedrockEconomy") ?? null;
		unset($manager);
	}
	/**
	 * @return int
	 */
	public function getMoney(Player $player, Closure $callback) : void {
		switch ($this->eco->getName()) {
			case "EconomyAPI":
				$money = $this->eco->myMoney($player);
				assert(is_float($money));
				$callback($money);
				break;
			case "BedrockEconomy":
				$this->eco->getAPI()->getPlayerBalance($player->getName(), ClosureContext::create(static function(?int $balance) use ($callback) : void {
					$callback($balance ?? 0);
				}));
				break;
			default:
				$this->eco->getAPI()->getPlayerBalance($player->getName(), ClosureContext::create(static function(?int $balance) use ($callback) : void {
					$callback($balance ?? 0);
				}));
		}
	}

	/**
	 * @return bool
	 */
	public function reduceMoney(Player $player, int $amount, Closure $callback) : void {
		if ($this->eco == null) {
			$this->plugin->getLogger()->warning("You not have Economy plugin");
			return true;
		}
		switch ($this->eco->getName()) {
			case "EconomyAPI":
				$callback($this->eco->reduceMoney($player, $amount) === EconomyAPI::RET_SUCCESS);
				break;
			case "BedrockEconomy":
				$this->eco->getAPI()->subtractFromPlayerBalance($player->getName(), (int) ceil($amount), ClosureContext::create(static function(bool $success) use ($callback) : void {
					$callback($success);
				}));
				break;
		}
	}

	public function addMoney(Player $player, float $amount) : void {
		if ($this->eco == null) {
			$this->plugin->getLogger()->warning("You not have Economy plugin");
			return true;
		}
		switch ($this->eco->getName()) {
			case "EconomyAPI":
				$this->eco->addMoney($player, $amount);
				break;
			case "BedrockEconomy":
				$this->eco->getAPI() - addToPlayerBalance($player->getName(), $amount);
				break;
		}
	}
}
