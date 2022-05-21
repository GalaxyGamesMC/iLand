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
use davidglitch04\iLand\session\SessionManager;
use davidglitch04\iLand\updater\GetUpdateInfo;
use davidglitch04\iLand\utils\DataUtils;
use pocketmine\lang\Language;
use pocketmine\plugin\PluginBase;
use pocketmine\resourcepacks\ResourcePack;
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

	private static ?ResourcePack $pack = null;

	private libRegRsp $libRegRsp;

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
		$this->saveResource("config.yml");
		self::$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		$this->initLanguage(strval(self::getDefaultConfig()->get('language', 'eng')), $this->languages);
		$this->validateConfigs();
		$this->initPack();
		$this->checkUpdater();

		if (VersionInfo::IS_DEVELOPMENT_BUILD) {
			if (!self::$config->get("enable-dev-builds", false)) {
				$this->getLogger()->emergency("To use this build anyway, set enable-dev-builds to true in your config.yml");
				$this->getServer()->forceShutdown();
			}
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


	protected function onDisable() : void {
		$this->libRegRsp->unRegRsp(self::$pack);
	}


	private function initPack() : void {
		$pack = self::$pack = DataUtils::zipPack($this);
		$this->libRegRsp = new libRegRsp($this);
		$this->libRegRsp->regRsp($pack);
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
			$this->saveResource("config.yml");
			$this->initLanguage(strval(self::getDefaultConfig()->get('language', 'eng')), $this->languages);
		}
	}


	private function checkUpdater() : void {
		$this->getServer()->getAsyncPool()->submitTask(new GetUpdateInfo($this, "https://raw.githubusercontent.com/David-pm-pl/iLand/stable/poggit_news.json"));
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


	public function getFileHack() : string {
		return $this->getFile();
	}
}
