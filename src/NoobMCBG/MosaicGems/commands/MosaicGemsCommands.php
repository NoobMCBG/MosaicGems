<?php

declare(strict_types=1);

namespace NoobMCBG\MosaicGems\commands;

use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use NoobMCBG\MosaicGems\MosaicGems;
use NoobMCBG\MosaicGems\gui\GUIManager;

class MosaicGemsCommands extends Command implements PluginOwned {

	private MosaicGems $plugin;

	public function __construct(MosaicGems $plugin){
		$this->plugin = $plugin;
		parent::__construct("mosaicgem", "Lệnh Để Mở Menu Khảm Khảm", null, ["mosaicgems", "khamngoc"]);
	}

	public function execute(CommandSender $sender, string $label, array $args){
		if(!$sender instanceof Player){
			return true;
		}
		$gui = new GUIManager();
		$gui->menuKhamNgoc($sender);
		MosaicGems::sendSound($sender, "random.click");
	}

	public function getOwningPlugin() : MosaicGems {
		return $this->plugin;
	}
}