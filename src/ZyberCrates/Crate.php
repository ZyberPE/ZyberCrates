<?php

namespace ZyberCrates;

use pocketmine\player\Player;
use pocketmine\item\ItemFactory;
use pocketmine\item\Item;

class Crate {

    private string $name;
    private array $rewards;
    private Item $keyItem;

    public function __construct(string $name, Item $keyItem, array $rewards){
        $this->name = $name;
        $this->keyItem = $keyItem;
        $this->rewards = $rewards;
    }

    public function getName(): string { return $this->name; }
    public function getKeyItem(): Item { return $this->keyItem; }

    public function openCrate(Player $player): void {
        // Random reward
        $reward = $this->rewards[array_rand($this->rewards)];
        [$id, $count] = explode(" ", $reward);
        $item = ItemFactory::get((int) $id, 0, (int) $count);
        $player->getInventory()->addItem($item);
        $player->sendMessage("§aYou won: ".$item->getName()." x".$item->getCount());
    }
}
