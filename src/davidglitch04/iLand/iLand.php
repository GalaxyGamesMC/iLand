<?php

namespace davidglitch04\iLand;

use davidglitch04\iLand\Command\iLandCommand;
use davidglitch04\iLand\Database\YamlProvider;
use davidglitch04\iLand\Session\SessionManager;
use pocketmine\lang\Language;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class iLand extends PluginBase
{
    use SingletonTrait;

    protected const IS_DEVELOPMENT_BUILD = true;

    protected const CONFIG = 'Yaml';

    private static Language $language;

    private array $languages = [
        'eng',
        'vie',
        'china',
    ];

    public static function getLanguage(): Language
    {
        return self::$language;
    }

    public function onLoad(): void
    {
        $this->setInstance($this);
    }

    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->initDataBase();
        $this->initLanguage(strval($this->getConfig()->get('language', 'eng')), $this->languages);
        if (self::IS_DEVELOPMENT_BUILD) {
            $this->getLogger()->warning('You are on development, unexpected errors will occur DavidGlitch04 will not fix this!');
        }
        $this->getServer()->getCommandMap()->register('land', new iLandCommand($this));
    }

    public function initDataBase(): void
    {
        switch (self::CONFIG) {
            case 'Yaml':
                $database = new YamlProvider($this);
                break;
            default:
            $database = new YamlProvider($this);
                break;
        }
        $database->initConfig();
    }

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

    public function getDataBase()
    {
        switch (self::CONFIG) {
            case 'Yaml':
                $database = new YamlProvider($this);
                break;
            default:
            $database = new YamlProvider($this);
                break;
        }

        return $database;
    }

    public function getSessionManager(): SessionManager
    {
        return new SessionManager();
    }
}
