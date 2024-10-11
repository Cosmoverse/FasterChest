<?php

declare(strict_types=1);

namespace cosmoverse\fasterchest;

use pocketmine\item\Item;

interface FasterChestSerializer{

	/**
	 * @param array<int, Item> $contents
	 * @return string
	 */
	public function serialize(array $contents): string;

	/**
	 * @param string $data
	 * @return array<int, Item>
	 */
	public function deserialize(string $data) : array;
}