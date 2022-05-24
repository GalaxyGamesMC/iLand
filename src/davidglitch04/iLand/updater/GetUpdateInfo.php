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

namespace davidglitch04\iLand\updater;

use davidglitch04\iLand\iLand;
use pocketmine\scheduler\AsyncTask;
use function curl_error;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt;
use function json_decode;

class GetUpdateInfo extends AsyncTask {
	protected string $url;

	public function __construct(iLand $plugin, string $url) {
		$this->url = $url;
		$this->storeLocal("key", $plugin); //4.0 compatible.
	}
	public function onRun() : void {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($curl);
		$curlerror = curl_error($curl);
		$responseJson = json_decode($response, true);
		$error = '';
		if ($curlerror != "") {
			$error = "Unknown error occurred, code:" . curl_getinfo($curl, CURLINFO_HTTP_CODE);
		} elseif (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200) {
			$error = $responseJson['message'];
		}
		$result = ["Response" => $responseJson, "Error" => $error, "httpCode" => curl_getinfo($curl, CURLINFO_HTTP_CODE)];
		$this->setResult($result);
	}
	public function onCompletion() : void {
		$plugin = $this->fetchLocal("key");
		Utils::handleUpdateInfo($this->getResult());
	}
}
