<?php

namespace davidglitch04\iLand;

use CortexPE\Commando\PacketHooker;
use davidglitch04\iLand\Command\iLandCommand;
use davidglitch04\iLand\Database\YamlProvider;
use davidglitch04\iLand\Listeners\BlockListener;
use davidglitch04\iLand\Listeners\PlayerListener;
use davidglitch04\iLand\Session\SessionManager;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\lang\Language;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class iLand extends PluginBase
{
    use SingletonTrait;

    /**@var Language $language */
    private static Language $language;
	
	public array $session = [];

    protected YamlProvider $provider;

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
        $this->saveDefaultConfig();
        $this->initLanguage(strval($this->getConfig()->get('language', 'eng')), $this->languages);
        if (VersionInfo::IS_DEVELOPMENT_BUILD) {
            $this->getLogger()->warning(self::getLanguage()->translateString('is.development.build'));
        }
        if (!PacketHooker::isRegistered()) PacketHooker::register($this);
        $this->getServer()->getCommandMap()->register('land', new iLandCommand($this, "land", "Land control panel", ["iland"]));
        foreach ([
            new PlayerListener($this), 
            new BlockListener($this)] as $event
        ) {
            $this->getServer()->getPluginManager()->registerEvents($event, $this);
        }
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

    public function getTool(): Item
    {
        $item = StringToItemParser::getInstance()->parse($this->getConfig()->get("tool_name", "Wooden_Axe"));
        if($item !== null){
            return $item;
        } else{
            return StringToItemParser::getInstance()->parse("Wooden_Axe");
        }
    }
}
