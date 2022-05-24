<?php

/*
 *
 *   _____ _                     _
 *  |_   _| |                   | |
 *    | | | |     __ _ _ __   __| |
 *    | | | |    / _` | '_ \ / _` |
 *   _| |_| |___| (_| | | | | (_| |
 *  |_____|______\__,_|_| |_|\__,_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author DavidGlitch04
 * @link https://github.com/David-pm-pl/iLand
 *
 *
*/

declare(strict_types=1);

namespace davidglitch04\iLand\utils;

use davidglitch04\iLand\iLand;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;

final class ItemUtils {
	public static function getItem() : Item {
		$item = StringToItemParser::getInstance()->parse(iLand::getDefaultConfig()->get("tool_name", "Wooden_Axe"));
		if ($item !== null) {
			return $item;
		} else {
			return StringToItemParser::getInstance()->parse("Wooden_Axe");
		}
	}
}
