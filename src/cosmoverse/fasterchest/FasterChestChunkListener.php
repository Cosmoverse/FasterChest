<?php

declare(strict_types=1);

namespace cosmoverse\fasterchest;

use Closure;
use pocketmine\block\tile\Chest as VanillaChestTile;
use pocketmine\math\Vector3;
use pocketmine\world\ChunkListener;
use pocketmine\world\format\Chunk;
use pocketmine\world\World;

final class FasterChestChunkListener implements ChunkListener{

	/** @var array<int, true> */
	private array $exclude_list = [];

	public function __construct(
		readonly private Loader $loader,
		readonly private World $world
	){}

	public function onChunkChanged(int $chunkX, int $chunkZ, Chunk $chunk) : void{
	}

	public function onChunkLoaded(int $chunkX, int $chunkZ, Chunk $chunk) : void{
	}

	public function onChunkUnloaded(int $chunkX, int $chunkZ, Chunk $chunk) : void{
	}

	public function onChunkPopulated(int $chunkX, int $chunkZ, Chunk $chunk) : void{
	}

	public function onBlockChanged(Vector3 $block) : void{
		$tile = $this->world->getTileAt($block->x, $block->y, $block->z);
		if($tile === null || $tile::class !== VanillaChestTile::class || isset($this->exclude_list[World::blockHash($block->x, $block->y, $block->z)])){
			return;
		}
		$this->loader->convertTile($tile);
	}

	/**
	 * @param int $x
	 * @param int $y
	 * @param int $z
	 * @param Closure() : void $callback
	 */
	public function excluding(int $x, int $y, int $z, Closure $callback) : void{
		$hash = World::blockHash($x, $y, $z);
		$this->exclude_list[$hash] = true;
		try{
			$callback();
		}finally{
			unset($this->exclude_list[$hash]);
		}
	}
}