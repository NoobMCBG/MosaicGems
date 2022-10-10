<?php

declare(strict_types=1);

namespace NoobMCBG\MosaicGems;

use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use muqsit\invmenu\InvMenuHandler;
use NoobMCBG\MosaicGems\commands\MosaicGemsCommands;

class MosaicGems extends PluginBase implements Listener {

	public static $instance;

	public static function getInstance() : self {
		return self::$instance;
	}

	public function onEnable() : void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
		$this->getServer()->getCommandMap()->register("/khamngoc", new MosaicGemsCommands($this));
        if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}
		//var_dump(\pocketmine\item\ItemIds::HEART_OF_THE_SEA);
		self::$instance = $this;
    }

    public function sendSound(Player $player, string $soundName, float $volume = 0, float $pitch = 0) : void {
        $packet = new PlaySoundPacket();
        $packet->soundName = $soundName;
        $packet->x = $player->getPosition()->getX();
        $packet->y = $player->getPosition()->getY();
        $packet->z = $player->getPosition()->getZ();
        $packet->volume = $volume;
        $packet->pitch = $pitch;
        $player->getNetworkSession()->sendDataPacket($packet);
    }
}
