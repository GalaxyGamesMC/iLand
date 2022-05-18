<?php

declare(strict_types=1);

namespace davidglitch04\iLand\item;

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
