<?php

declare(strict_types=1);

namespace NoobMCBG\MosaicGems;

use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
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
}