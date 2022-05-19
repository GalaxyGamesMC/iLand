<?php

declare(strict_types=1);

namespace davidglitch04\iLand;

use davidglitch04\iLand\command\iLandCommand;
use davidglitch04\iLand\database\YamlProvider;
use davidglitch04\iLand\libs\CortexPE\Commando\PacketHooker;
use davidglitch04\iLand\libs\JackMD\ConfigUpdater\ConfigUpdater;
use davidglitch04\iLand\libs\NhanAZ\libRegRsp\libRegRsp;
use davidglitch04\iLand\listeners\BlockListener;
use davidglitch04\iLand\listeners\PlayerListener;
use davidglitch04\iLand\object\Land;
use davidglitch04\iLand\session\SessionManager;
use davidglitch04\iLand\updater\GetUpdateInfo;
use davidglitch04\PluginUpdater\PluginUpdater;
use pocketmine\lang\Language;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use function is_dir;
use function is_file;
use function mkdir;
use function rename;
use function strval;

class iLand extends PluginBase {
	use SingletonTrait;

	/**@var Language $language */
	private static Language $language;

	public array $session = [];

	public array $lands = [];

	protected YamlProvider $provider;

	private static Config $config;
	/** @var array|string[] $languages */
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

	/**
	 * @throws libs\CortexPE\Commando\exception\HookAlreadyRegistered
	 */
	public function onEnable() : void {
		$this->provider->initConfig();
		$this->saveResource("config.json");
		self::$config = new Config($this->getDataFolder() . "config.json", Config::JSON);
		$this->initLanguage(strval(self::getDefaultConfig()->get('language', 'eng')), $this->languages);
		$this->validateConfigs();
		$this->initPack();
		$this->checkUpdater();
		$this->addLands();
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
		$this->getServer()->getCommandMap()->register('iland', new iLandCommand($this, "iland", self::getLanguage()->translateString("command.land"), ["land"]));
	}


	public function addLands() : void {
		foreach ($this->getProvider()->getAllReceived() as $json) {
			$this->lands[] = new Land($json);
		}
	}


	private function initPack() : void {
		$libRegRsp = new libRegRsp($this);
		$libRegRsp->regRsp("iLandPack.mcpack");
	}


	private function validateConfigs() : void {
		$updated = false;

		if (ConfigUpdater::checkUpdate($this, self::$config, "version", VersionInfo::CONFIG_VERSION)) {
			$updated = true;
			$this->reloadConfig();
		}

		if ($updated) {
			$path = $this->getDataFolder() . 'languages/';
			foreach ($this->languages as $file) {
				rename($path . $file . '.ini', $path . $file . '_old.ini');
			}
			$this->saveResource("config.json");
			$this->initLanguage(strval(self::getDefaultConfig()->get('language', 'eng')), $this->languages);
		}
	}


    private function checkUpdater() : void {
        $this->getServer()->getAsyncPool()->submitTask(new GetUpdateInfo($this, "https://raw.githubusercontent.com/David-pm-pl/iLand/stable/poggit_news.json"));
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


	public function getLandManager() : LandManager {
		return new LandManager();
	}


	public function getLands() : array {
		return $this->lands;
	}


	public function getFileHack() : string {
		return $this->getFile();
	}
}
