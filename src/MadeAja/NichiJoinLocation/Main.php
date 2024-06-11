<?php

namespace MadeAja\NichiJoinLocation;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase implements Listener
{
    use SingletonTrait;

    public array $config;

    /** onEnable */
    protected function onEnable() : void
    {
        self::setInstance($this);
        $this->saveDefaultConfig();
        $this->config = $this->getConfig()->getAll();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /** Event onJoin */
    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $this->getServer()->getAsyncPool()->submitTask(new LocationTask($player->getNetworkSession()->getIp(), strtolower($player->getName())));
        $event->setJoinMessage("");
    }

    /** displayBroadcast */
    public function displayBroadcast($region, $city, $name)
    {
        $message = str_replace(["{player}", "{region}", "{city}", "&"], [$name, $region, $city, "§"], $this->config["prefix"]);
        $this->getServer()->broadcastMessage($message);
    }
}
