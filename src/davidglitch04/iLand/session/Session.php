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

namespace davidglitch04\iLand\session;

use pocketmine\world\Position;
use function is_null;

class Session {
	private array $data = [];

	public function __construct(string $username) {
		$this->data['Username'] = $username;
		$this->data['A'] = null;
		$this->data['B'] = null;
	}

	public function setNextPosition(Position $position) : string {
		if ($this->isNull('A')) {
			$this->data['A'] = $position;
			$string = "A";
		} elseif ($this->isNull('B')) {
			$this->data['B'] = $position;
			$string = "B";
		}
		return $string;
	}

	public function getPositionA() : Position {
		return $this->data['A'];
	}

	public function getPositionB() : Position {
		return $this->data['B'];
	}

	public function isNull(string $location) : bool {
		if (is_null($this->data[$location])) {
			return true;
		} else {
			return false;
		}
	}
}
