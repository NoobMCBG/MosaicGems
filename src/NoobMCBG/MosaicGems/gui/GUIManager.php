<?php

declare(strict_types=1);

namespace NoobMCBG\MosaicGems\gui;

use Closure;
use pocketmine\player\Player;
use pocketmine\inventory\Inventory;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\data\bedrock\EnchantmentIdMap;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\math\Vector3;
use NoobMCBG\MosaicGems\MosaicGems;

class GUIManager {

	public const BORDER_OUTSIDE = [
		0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 18, 27, 36, 45, 17, 26, 35, 44, 46, 47, 48, 49, 50, 51, 52
	];

	public const BORDER_INSIDE = [
        10, 11, 12, 13, 14, 15, 16, 19, 21, 22, 23, 25, 28, 29, 30, 31, 32, 33, 34, 37, 38, 39, 40, 41, 42, 43
	];

	public function menuKhamNgoc(Player $player){
		$menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
		$menu->setListener(Closure::fromCallable([$this, "menuKhamNgocListener"]));
        $menu->setInventoryCloseListener(Closure::fromCallable([$this, "menuKhamNgocCloseListener"]));
        $menu->setName("§l§3•§2 Menu Khảm Ngọc §3•");
        $inv = $menu->getInventory();
        $itemoutside = self::getItem(160, 14, 1);
        $itemoutside->setCustomName("§l§cBORDER");
        foreach(self::BORDER_OUTSIDE as $outside){
            $inv->setItem($outside, $itemoutside);
        }
        $iteminside = self::getItem(160, 4, 1);
		$iteminside->setCustomName("§l§cBORDER");
        foreach(self::BORDER_INSIDE as $inside){
            $inv->setItem($inside, $iteminside);

        }
        $inv->setItem(53, self::getItem(160, 5, 1)->setCustomName("§l§c•§e Khảm Ngọc §c•"));
        $inv->setItem(45, self::getItem(160, 3, 1)->setCustomName("§l§c•§e Xem Vật Phẩm Nhận Được §c•"));
        $menu->send($player);
	}

	public static function menuKhamNgocListener(InvMenuTransaction $transaction) : InvMenuTransactionResult {
		$action = $transaction->getAction();
        $player = $transaction->getPlayer();
		$ngoc = $action->getInventory()->getItem(24);
        $doghep = $action->getInventory()->getItem(20);
        if($action->getSlot() == 53){
        	if($ngoc->isNull() == false){
                if($doghep->isNull() == false){
                    if($doghep instanceof \pocketmine\item\Tool){
                        if($ngoc->getId() == ItemIds::HEART_OF_THE_SEA){
                            if($ngoc->hasEnchantments()){
                                foreach($ngoc->getEnchantments() as $enchantment){
                                    $doghep->addEnchantment($enchantment);
                                }
                                if($player->getInventory()->canAddItem($doghep)){
                                    $player->getInventory()->addItem($doghep);
                                }else{
                                    $player->getPosition()->getWorld()->dropItem(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), $doghep);
                                }
                                if($ngoc->getCount() > 1){
                                    $count = $ngoc->getCount() - 1;
                                    $ngoc->setCount($count);
                                    if($player->getInventory()->canAddItem($ngoc)){
                                        $player->getInventory()->addItem($ngoc);
                                    }else{
                                        $player->getPosition()->getWorld()->dropItem(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), $ngoc);
                                    }
                                }
                                $action->getInventory()->setItem(20, ItemFactory::getInstance()->get(0));
                                $action->getInventory()->setItem(24, ItemFactory::getInstance()->get(0));
                                $player->removeCurrentWindow();
                                $player->sendTitle("§l§c•§a KHẢM NGỌC THÀNH CÔNG §c•");
                                $player->sendMessage("§l§c•§e Bạn Đã Khảm Ngọc Thành Công !");
                                MosaicGems::sendSound($player, "random.totem");
                                MosaicGems::sendSound($player, "random.levelup");
                            }else{
                                $action->getInventory()->setItem(20, ItemFactory::getInstance()->get(0));
                                $action->getInventory()->setItem(24, ItemFactory::getInstance()->get(0));
                                $player->sendMessage("§l§c•§e Ngọc Bạn Để Vào Không Có Enchant !");
                                $player->getInventory()->addItem($ngoc);
                                $player->getInventory()->addItem($doghep);
                                $player->removeCurrentWindow();
                                MosaicGems::sendSound($player, "random.explode");
                            }
                        }else{
                            $action->getInventory()->setItem(20, ItemFactory::getInstance()->get(0));
                            $action->getInventory()->setItem(24, ItemFactory::getInstance()->get(0));
                            $player->sendMessage("§l§c•§e Bạn Phải Để Ngọc Vào Ô Thứ 2, Chứ Không Phải Các Vật Phẩm Khác !");
                            $player->getInventory()->addItem($ngoc);
                            $player->getInventory()->addItem($doghep);
                            $player->removeCurrentWindow();
                            MosaicGems::sendSound($player, "random.explode");
                        }
                    }else{
                        $action->getInventory()->setItem(20, ItemFactory::getInstance()->get(0));
                        $action->getInventory()->setItem(24, ItemFactory::getInstance()->get(0));
                        $player->sendMessage("§l§c•§e Bạn Chỉ Có Thể Khảm Cho Các Vật Phẩm Như:§a Cúp, Kiếm, Rìu, Xẻng !");
                        $player->getInventory()->addItem($ngoc);
                        $player->getInventory()->addItem($doghep);
                        $player->removeCurrentWindow();
                        MosaicGems::sendSound($player, "random.explode");
                    }
                }else{
                    $action->getInventory()->setItem(20, ItemFactory::getInstance()->get(0));
                    $action->getInventory()->setItem(24, ItemFactory::getInstance()->get(0));
                    $player->sendMessage("§l§c•§e Xin Hãy Để Ngọc Khảm Vào Ô Thứ 2 !");
                    $player->getInventory()->addItem($ngoc);
                    $player->getInventory()->addItem($doghep);
                    $player->removeCurrentWindow();
                    MosaicGems::sendSound($player, "random.explode");
                }
            }else{
                $action->getInventory()->setItem(20, ItemFactory::getInstance()->get(0));
                $action->getInventory()->setItem(24, ItemFactory::getInstance()->get(0));
                $player->sendMessage("§l§c•§e Xin Hãy Để Vật Phẩm Muốn Khảm Vào Ô Thứ 1 !");
                $player->getInventory()->addItem($ngoc);
                $player->getInventory()->addItem($doghep);
                $player->removeCurrentWindow();
                MosaicGems::sendSound($player, "random.explode");
            }
            return $transaction->discard();
        }
        if($action->getSlot() == 45){
            if($doghep->isNull() == false){
                if($ngoc->isNull() == false){
                    if($ngoc->hasEnchantments()){
                        foreach($ngoc->getEnchantments() as $enchantment){
                            $doghep->addEnchantment($enchantment);
                        }
                        $action->getInventory()->setItem(40, $doghep);
                    }else{
                        $action->getInventory()->setItem(40, $doghep);
                    }
                }else{
                    $action->getInventory()->setItem(40, self::getItem(160, 6, 1)->setCustomName("§l§c•§e Vui Lòng Để Ngọc Vào Ô Thứ 2 Để Xem Đồ Nhận Được !"));
                }
            }else{
                $action->getInventory()->setItem(40, self::getItem(160, 6, 1)->setCustomName("§l§c•§e Vui Lòng Để Đồ Khảm Vào Ô Thứ 1 Để Xem Đồ Nhận Được !"));
            }
            return $transaction->discard();
        }
        if($action->getSlot() == 20 or $action->getSlot() == 24){
            return $transaction->continue();
        }
        return $transaction->discard();

        if(in_array($action->getSlot(), [self::BORDER_INSIDE])){
            return $transaction->discard();
        }
        if(in_array($action->getSlot(), [self::BORDER_OUTSIDE])){
            return $transaction->discard();
        }
	}

    public function menuKhamNgocCloseListener(Player $player, Inventory $inventory) : void {
        if($inventory->getItem(20)->isNull() == false){
            if($player->getInventory()->canAddItem($inventory->getItem(20))){
                $player->getInventory()->addItem($inventory->getItem(20));
            }else{
                $player->getPosition()->getWorld()->dropItem(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), $inventory->getItem(20));
            }
        }
        if($inventory->getItem(24)->isNull() == false){
            if($player->getInventory()->canAddItem($inventory->getItem(24))){
                $player->getInventory()->addItem($inventory->getItem(24));
            }else{
                $player->getPosition()->getWorld()->dropItem(new Vector3($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ()), $inventory->getItem(24));
            }
        }
    }

	public static function getItem(int $id, int $meta, int $count){
		$item = ItemFactory::getInstance()->get((int)$id, (int)$meta, (int)$count);
        return $item;
	}
}