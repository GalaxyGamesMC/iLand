<?php

namespace davidglitch04\iLand;

use davidglitch04\iLand\Command\iLandCommand;
use davidglitch04\iLand\Database\YamlProvider;
use davidglitch04\iLand\Libs\CortexPE\Commando\PacketHooker;
use davidglitch04\iLand\Listeners\BlockListener;
use davidglitch04\iLand\Listeners\PlayerListener;
use davidglitch04\iLand\Session\SessionManager;
use pocketmine\lang\Language;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class iLand extends PluginBase
{
    use SingletonTrait;

    /**@var Language $language */
    private static Language $language;
	
	public array $session = [];

    protected YamlProvider $provider;

    private static Config $config;

    /** @var array $languages */
    private array $languages = [
        'eng',
        'vie',
        'zho',
    ];

    /**
     * @return Language
     */
    public static function getLanguage(): Language
    {
        return self::$language;
    }

    public static function getDefaultConfig(): Config
    {
        return self::$config;
    }

    /**
     * @return void
     */
    public function onLoad(): void
    {
        $this->setInstance($this);
        $this->provider = new YamlProvider($this);
    }

    /**
     * @return void
     */
    public function onEnable(): void
    {
        $this->provider->initConfig();
        $this->initCommand();
        $this->saveResource("config.json");
        self::$config = new Config($this->getDataFolder() . "config.json", Config::JSON);
        $this->initLanguage(strval(self::getDefaultConfig()->get('language', 'eng')), $this->languages);
        if (VersionInfo::IS_DEVELOPMENT_BUILD) {
            $this->getLogger()->warning(self::getLanguage()->translateString('is.development.build'));
        }
        if (!PacketHooker::isRegistered()) PacketHooker::register($this);
        foreach ([
            new PlayerListener($this), 
            new BlockListener($this)] as $event
        ) {
            $this->getServer()->getPluginManager()->registerEvents($event, $this);
        }
    }

    public function initCommand() : void
    {
        DefaultPermissions::registerPermission(new Permission("iland.allow.command", "Allow player to use iland"));
        $this->getServer()->getCommandMap()->register('land', new iLandCommand($this, "land", "Land control panel", ["iland"]));
    }
    
    protected function onDisable(): void
    {
        $this->getProvider()->save();
    }
    /**
     * @param  string $lang
     * @param  array  $languageFiles
     * @return void
     */
    public function initLanguage(string $lang, array $languageFiles): void
    {
        $path = $this->getDataFolder().'languages/';
        if (!is_dir($path)) {
            @mkdir($path);
        }
        foreach ($languageFiles as $file) {
            if (!is_file($path.$file.'.ini')) {
                $this->saveResource('languages/'.$file.'.ini');
            }
        }
        self::$language = new Language($lang, $path);
    }

    /**
     * @return YamlProvider
     */
    public function getProvider(): YamlProvider
    {
        return $this->provider;
    }
    
    /**
     * @return SessionManager
     */
    public function getSessionManager(): SessionManager
    {
        return new SessionManager();
    }
}
