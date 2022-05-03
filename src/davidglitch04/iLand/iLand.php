<?php

namespace davidglitch04\iLand;

use CortexPE\Commando\PacketHooker;
use davidglitch04\iLand\Command\iLandCommand;
use davidglitch04\iLand\Database\YamlProvider;
use davidglitch04\iLand\Session\SessionManager;
use pocketmine\lang\Language;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class iLand extends PluginBase
{
    use SingletonTrait;

    /**@var Language $language */
    private static Language $language;

    /** @var array $languages */
    private array $languages = [
        'eng',
        'vie',
        'china',
    ];

    /**
     * @return Language
     */
    public static function getLanguage(): Language
    {
        return self::$language;
    }

    /**
     * @return void
     */
    public function onLoad(): void
    {
        $this->setInstance($this);
    }

    /**
     * @return void
     */
    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->initDataBase();
        $this->initLanguage(strval($this->getConfig()->get('language', 'eng')), $this->languages);
        if (VersionInfo::IS_DEVELOPMENT_BUILD) {
            $this->getLogger()->warning(self::getLanguage()->translateString('is.development.build'));
        }
        if (!PacketHooker::isRegistered()) PacketHooker::register($this);
        $this->getServer()->getCommandMap()->register('land', new iLandCommand($this, "land", "Land control panel", ["iland"]));
    }

    /**
     * @return void
     */
    public function initDataBase(): void
    {
        $database = new YamlProvider($this);
        $database->initConfig();
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
    public function getDataBase(): YamlProvider
    {
        $database = new YamlProvider($this);
        return $database;
    }
    
    /**
     * @return SessionManager
     */
    public function getSessionManager(): SessionManager
    {
        return new SessionManager();
    }
}
