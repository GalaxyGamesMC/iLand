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
use Error;
use pocketmine\scheduler\AsyncTask;
use function curl_close;
use function curl_errno;
use function curl_error;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt;
use function fclose;
use function fopen;

class DownloadFile extends AsyncTask {
	protected string $url;
	protected string $path;

	public function __construct(iLand $plugin, string $url, string $path) {
		$this->url = $url;
		$this->path = $path;
		$this->storeLocal("idk", $plugin); //4.0 compatible.
	}
	public function onRun() : void {
		$file = fopen($this->path, 'w+');
		if ($file === false) {
			throw new Error('Could not open: ' . $this->path);
		}
		$ch = curl_init($this->url);
		curl_setopt($ch, CURLOPT_FILE, $file);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60); //give it 1 minute.
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_exec($ch);
		if (curl_errno($ch)) {
			throw new Error(curl_error($ch));
		}
		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		fclose($file);
		$this->setResult($statusCode);
	}
	public function onCompletion() : void {
		$plugin = $this->fetchLocal("idk");
		Utils::handleDownload($this->path, $this->getResult());
	}
}
