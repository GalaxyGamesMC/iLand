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
use davidglitch04\iLand\VersionInfo;
use pocketmine\Server;
use function array_key_exists;
use function date;
use function end;
use function explode;
use function intval;
use function is_dir;
use function mkdir;
use function rename;
use function rmdir;
use function scandir;
use function sizeof;
use function str_replace;
use function strtolower;
use function substr;
use function unlink;

class Utils {
	/**
	 * handleUpdateInfo function
	 */
	public static function handleUpdateInfo(Array $data) : void {
		$plugin = iLand::getInstance();
		Server::getInstance()->getLogger()->debug("Handling latest update data.");
		if ($data["Error"] !== '') {
			Server::getInstance()->getLogger()->warning("Failed to get latest update data, Error: " . $data["Error"] . " Code: " . $data["httpCode"]);
			return;
		}
		if (array_key_exists("version", $data["Response"]) && array_key_exists("time", $data["Response"]) && array_key_exists("link", $data["Response"])) {
			$update = Utils::compareVersions(strtolower(VersionInfo::PLUGIN_VERSION), strtolower($data["Response"]["version"]));
			if ($update == 0) {
				Server::getInstance()->getLogger()->debug("Plugin up-to-date !");
				return;
			}
			if ($update > 0) {
				$lines = explode("\n", $data["Response"]["patch_notes"]);
				Server::getInstance()->getLogger()->warning("--- UPDATE AVAILABLE ---");
				Server::getInstance()->getLogger()->warning("§cVersion     :: " . $data["Response"]["version"]);
				Server::getInstance()->getLogger()->warning("§bReleased on :: " . date("d-m-Y", intval($data["Response"]["time"])));
				Server::getInstance()->getLogger()->warning("§aPatch Notes :: " . $lines[0]);
				for ($i = 1; $i < sizeof($lines); $i++) {
					Server::getInstance()->getLogger()->warning("                §c" . $lines[$i]);
				}
				Server::getInstance()->getLogger()->warning("§dUpdate Link :: " . $data["Response"]["link"]);
				if ($plugin->getConfig()->get("enableUpdateAutoUpdater") !== true) {
					$plugin->getLogger()->warning("§cEnable the download_updates option in config.yml to automatically download and install updates.");
				}
			} else {
				if ($update < 0) {
					Server::getInstance()->getLogger()->debug("Running a build not yet released, this can cause un intended side effects (including possible data loss)");
				}
				return;
			}
			if ($plugin->getConfig()->get("enableUpdateAutoUpdater")) {
				Server::getInstance()->getLogger()->warning("§cDownloading & Installing Update, please do not abruptly stop server/plugin.");
				Server::getInstance()->getLogger()->debug("Begin download of new update from '" . $data["Response"]["download_link"] . "'.");
				Utils::downloadUpdate($data["Response"]["download_link"]);
			}
		} else {
			Server::getInstance()->getLogger()->warning("Failed to verify update data/incorrect format provided.");
			return;
		}
	}

	/**
	 * downloadUpdate function
	 */
	protected static function downloadUpdate(string $url) : void {
		$plugin = iLand::getInstance();
		@mkdir($plugin->getDataFolder() . "tmp/");
		$path = $plugin->getDataFolder() . "tmp/iLand.phar";
		Server::getInstance()->getAsyncPool()->submitTask(new DownloadFile($plugin, $url, $path));
	}
	/**
	 * compareVersions function
	 *
	 * @return integer
	 */
	public static function compareVersions(string $base, string $new) : int {
		$baseParts = explode(".",$base);
		$baseParts[2] = explode("-beta",$baseParts[2])[0];
		if (sizeof(explode("-beta",explode(".",$base)[2])) > 1) {
			$baseParts[3] = explode("-beta",explode(".",$base)[2])[1];
		}
		$newParts = explode(".",$new);
		$newParts[2] = explode("-beta",$newParts[2])[0];
		if (sizeof(explode("-beta",explode(".",$new)[2])) > 1) {
			$newParts[3] = explode("-beta",explode(".",$new)[2])[1];
		}
		if (intval($newParts[0]) > intval($baseParts[0])) {
			return 1;
		}
		if (intval($newParts[0]) < intval($baseParts[0])) {
			return -1;
		}
		if (intval($newParts[1]) > intval($baseParts[1])) {
			return 1;
		}
		if (intval($newParts[1]) < intval($baseParts[1])) {
			return -1;
		}
		if (intval($newParts[2]) > intval($baseParts[2])) {
			return 1;
		}
		if (intval($newParts[2]) < intval($baseParts[2])) {
			return -1;
		}
		if (isset($baseParts[3])) {
			if (isset($newParts[3])) {
				if (intval($baseParts[3]) > intval($newParts[3])) {
					return -1;
				}
				if (intval($baseParts[3]) < intval($newParts[3])) {
					return 1;
				}
			} else {
				return 1;
			}
		}
		return 0;
	}
	/**
	 * handleDownload function
	 *
	 * @param integer $status
	 */
	public static function handleDownload(string $path, int $status) : void {
		$plugin = iLand::getInstance();
		Server::getInstance()->getLogger()->debug("Update download complete, at '" . $path . "' with status '" . $status . "'");
		if ($status !== 200) {
			Server::getInstance()->getLogger()->warning("Received status code '" . $status . "' when downloading update, update cancelled.");
			Utils::rmalldir($plugin->getDataFolder() . "/tmp");
			return;
		}
		@rename($path, Server::getInstance()->getPluginPath() . "/iLand.phar");
		if (Utils::getFileName() === null) {
			Server::getInstance()->getLogger()->debug("Deleting previous iLand version...");
			Utils::rmalldir($plugin->getFileHack());
			Server::getInstance()->getLogger()->warning("Update complete, restart your server to load the new updated version.");
			return;
		}
		@rename(Server::getInstance()->getPluginPath() . "/" . Utils::getFileName(), Server::getInstance()->getPluginPath() . "/iLand.phar.old"); //failsafe i guess.
		Server::getInstance()->getLogger()->warning("Update complete, restart your server to load the new updated version.");
		return;
	}
	/**
	 * rmalldir function
	 */
	public static function rmalldir(string $dir) : void {
		if ($dir == "" || $dir == "/" || $dir == "C:/") {
			return;
		} //tiny safeguard.
		$tmp = scandir($dir);
		foreach ($tmp as $item) {
			if ($item === '.' || $item === '..') {
				continue;
			}
			$path = $dir . '/' . $item;
			if (is_dir($path)) {
				Utils::rmalldir($path);
			} else {
				@unlink($path);
			}
		}
		@rmdir($dir);
	}
	/**
	 * getFileName function
	 *
	 * @return string|null
	 */
	private static function getFileName() {
		$plugin = iLand::getInstance();
		$path = $plugin->getFileHack();
		if (substr($path, 0, 7) !== "phar://") {
			return null;
		}
		$tmp = explode("\\", $path);
		$tmp = end($tmp); //requires reference, so cant do all in one
		return str_replace("/","",$tmp);
	}
}
