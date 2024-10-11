<?php

declare(strict_types=1);

namespace cosmoverse\fasterchest;

use pocketmine\block\inventory\ChestInventory;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryListener;
use pocketmine\item\Item;
use function assert;

final class FasterChestInventoryListener implements InventoryListener{

	public static function instance() : self{
		static $instance = null;
		return $instance ??= new self();
	}

	private function __construct(){
	}

	public function onSlotChange(Inventory $inventory, int $slot, Item $oldItem) : void{
		$this->onAnyChange($inventory);
	}

	public function onContentChange(Inventory $inventory, array $oldContents) : void{
		$this->onAnyChange($inventory);
	}

	private function onAnyChange(Inventory $inventory) : void{
		assert($inventory instanceof ChestInventory);
		$position = $inventory->getHolder();
		$tile = $position->world->getTileAt($position->x, $position->y, $position->z);
		if($tile instanceof FasterChest){
			$tile->setUnsavedChanges();
		}
	}
}