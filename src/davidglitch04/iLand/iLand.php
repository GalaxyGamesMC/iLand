<?php

declare(strict_types=1);

namespace davidglitch04\iLand;

use davidglitch04\iLand\command\iLandCommand;
use davidglitch04\iLand\database\YamlProvider;
use davidglitch04\iLand\libs\CortexPE\Commando\PacketHooker;
use davidglitch04\iLand\listeners\BlockListener;
use davidglitch04\iLand\listeners\PlayerListener;
use davidglitch04\iLand\session\SessionManager;
use pocketmine\lang\Language;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use function is_dir;
use function is_file;
use function mkdir;
use function strval;

class iLand extends PluginBase {
	use SingletonTrait;

	/**@var Language $language */
	private static Language $language;

	public array $session = [];

	protected YamlProvider $provider;

	private static Config $config;

	private array $languages = [
		'eng',
		'vie',
		'zho',
	];

	public static function getLanguage() : Language {
		return self::$language;
	}

	public static function getDefaultConfig() : Config {
		return self::$config;
	}

	public function onLoad() : void {
		$this->setInstance($this);
		$this->provider = new YamlProvider($this);
	}

	public function onEnable() : void {
		$this->provider->initConfig();
		$this->saveResource("config.json");
		self::$config = new Config($this->getDataFolder() . "config.json", Config::JSON);
		$this->initLanguage(strval(self::getDefaultConfig()->get('language', 'eng')), $this->languages);
		if (VersionInfo::IS_DEVELOPMENT_BUILD) {
			$this->getLogger()->warning(self::getLanguage()->translateString('is.development.build'));
		}
		if (!PacketHooker::isRegistered()) {
			PacketHooker::register($this);
		}
		foreach ([
			new PlayerListener($this),
			new BlockListener($this)] as $event
		) {
			$this->getServer()->getPluginManager()->registerEvents($event, $this);
		}
		$this->getServer()->getCommandMap()->register('land', new iLandCommand($this, "land", "Land control panel", ["iland"]));
	}

	protected function onDisable() : void {
		$this->getProvider()->save();
	}

	public function initLanguage(string $lang, array $languageFiles) : void {
		$path = $this->getDataFolder() . 'languages/';
		if (!is_dir($path)) {
			@mkdir($path);
		}
		foreach ($languageFiles as $file) {
			if (!is_file($path . $file . '.ini')) {
				$this->saveResource('languages/' . $file . '.ini');
			}
		}
		self::$language = new Language($lang, $path);
	}

	public function getProvider() : YamlProvider {
		return $this->provider;
	}

	public function getSessionManager() : SessionManager {
		return new SessionManager();
	}
}
