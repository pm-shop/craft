<?php
namespace CraftCommand;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase{
    public $config;
    public $cooldown;
    public $db;
    public static $instance;

    public function onEnable()
    {
        $this->getLogger()->info("CraftCommand activé");
        self::$instance = $this;
        @mkdir($this->getDataFolder());
        if(!file_exists($this->getDataFolder(). "config.yml")){
            $this->saveResource('config.yml');
        }
        $this->config = new Config($this->getDataFolder(). 'config.yml', Config::YAML);
        $this->cooldown = $this->config->get("cooldown");

        @mkdir($this->getDataFolder());
        if(!file_exists($this->getDataFolder(). "craftdb.yml")){
            $this->saveResource('craftdb.yml');
        }
        if(empty($this->cooldown)){
            $this->config->set("cooldown", 10);
            $this->config->save();
        }
        $this->db = new Config($this->getDataFolder(). 'craftdb.yml', Config::YAML);
        $this->getServer()->getCommandMap()->registerAll("Commands", [
            new Craft('craft', 'acceder au menu de craft', 'craft', ['craft'])
        ]);
    }

    public static function getInstance(){
        return self::$instance;
    }

    public function onDisable()
    {
        $this->getLogger()->info("CraftCommand disactivé");
    }
}