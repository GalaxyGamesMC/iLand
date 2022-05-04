<?php

namespace davidglitch04\iLand\Economy;

use Closure;
use cooldogedev\BedrockEconomy\libs\cooldogedev\libSQL\context\ClosureContext;
use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;
use pocketmine\Server;

final class EconomyManager
{
    private const ECONOMYAPI = 'EcoAPI';

    private const BEDROCKECONOMYAPI = 'BedrockEco';

    private static function getEconomy(): array
    {
        $api = Server::getInstance()->getPluginManager()->getPlugin('EconomyAPI');
        if ($api !== null) {
            return [self::ECONOMYAPI, $api];
        } else {
            $api = Server::getInstance()->getPluginManager()->getPlugin('BedrockEconomy');
            if ($api !== null) {
                return [self::BEDROCKECONOMYAPI, $api];
            }
        }
    }

    public static function myMoney(string $player, Closure $callback): void
    {
        if (self::getEconomy()[0] === self::ECONOMYAPI) {
            $money = self::getEconomy()[1]->myMoney($player);
            assert(is_float($money));
            $callback($money);
        } elseif (self::getEconomy()[0] === self::BEDROCKECONOMYAPI) {
            self::getEconomy()[1]->getAPI()->getPlayerBalance($player, ClosureContext::create(static function (?int $money) use ($callback): void {
                $callback($money ?? 0);
            }));
        }
    }

    public static function addMoney(Player $player, int $amount): void
    {
        if (self::getEconomy()[0] === self::ECONOMYAPI) {
            self::getEconomy()[1]->addMoney($player, $amount);
        } elseif (self::getEconomy()[0] === self::BEDROCKECONOMYAPI) {
            self::getEconomy()[1]->getAPI()->addToPlayerBalance($player->getName(), (int) $amount);
        }
    }

    public static function reduceMoney(Player $player, int $amount, Closure $callback): void
    {
        if (self::getEconomy()[0] === self::ECONOMYAPI) {
            $callback(self::getEconomy()[1]->reduceMoney($player, $amount) === EconomyAPI::RET_SUCCESS);
        } elseif (self::getEconomy()[0] === self::BEDROCKECONOMYAPI) {
            self::getEconomy()[1]->getAPI()->subtractFromPlayerBalance($player->getName(), (int) ceil($amount), ClosureContext::create(static function (bool $success) use ($callback): void {
                $callback($success);
            }));
        }
    }
}
