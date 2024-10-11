<?php

declare(strict_types=1);

namespace cosmoverse\fasterchest;

use GlobalLogger;
use pocketmine\block\tile\Container;
use pocketmine\data\bedrock\item\SavedItemStackData;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\item\Item;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\TreeRoot;
use function assert;

final class DefaultFasterChestSerializer implements FasterChestSerializer{

	public static function instance() : self{
		static $instance = null;
		return $instance ??= new self();
	}

	readonly private BigEndianNbtSerializer $serializer;

	private function __construct(){
		$this->serializer = new BigEndianNbtSerializer();
	}

	public function serialize(array $contents): string{
		$tag = new ListTag([], NBT::TAG_Compound);
		foreach($contents as $slot => $item){
			$tag->push($item->nbtSerialize($slot));
		}
		$nbt = CompoundTag::create()->setTag(Container::TAG_ITEMS, $tag);
		return $this->serializer->write(new TreeRoot($nbt));
	}

	public function deserialize(string $data) : array{
		$tag = $this->serializer->read($data)->mustGetCompoundTag()->getListTag(Container::TAG_ITEMS);
		if($tag === null){
			return [];
		}
		$contents = [];
		foreach($tag as $item){
			assert($item instanceof CompoundTag);
			try{
				$contents[$item->getByte(SavedItemStackData::TAG_SLOT)] = Item::nbtDeserialize($item);
			}catch(SavedDataLoadingException $e){
				GlobalLogger::get()->logException($e);
				continue;
			}
		}
		return $contents;
	}
}