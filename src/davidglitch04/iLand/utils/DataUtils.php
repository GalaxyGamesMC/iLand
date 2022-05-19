<?php

declare(strict_types=1);

namespace davidglitch04\iLand\utils;

use function json_decode;
use function json_encode;
use function utf8_encode;

class DataUtils {
	public static function encode(array $data) : mixed {
		$encode = utf8_encode(json_encode($data));
		return $encode;
	}

	public static function decode(string $encrypt) : mixed {
		$decode = json_decode($encrypt);
		return $decode;
	}
}