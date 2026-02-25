<?php

namespace ZyberCrates;

use pocketmine\item\ItemFactory;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;

class CrateManager {

    /** @var Crate[] */
    private array $crates = [];
    private PluginBase $plugin;

    public function __construct(PluginBase $plugin){
        $this->plugin = $plugin;
        $this->loadCrates();
    }

    public function loadCrates(): void {
        $this->crates = [];
        $cfg = $this->plugin->getConfig()->getAll();
        foreach($cfg as $key => $data){
            $id = $data["keyItem"]["id"];
            $meta = $data["keyItem"]["meta"] ?? 0;
            $name = $data["keyItem"]["customName"] ?? $key." Key";
            $keyItem = ItemFactory::get($id, $meta, 1);
            $keyItem->setCustomName($name);
            $rewards = $data["rewards"] ?? [];
            $this->crates[$key] = new Crate($key, $keyItem, $rewards);
        }
    }

    public function getCrates(): array { return $this->crates; }
    public function getCrate(string $name): ?Crate { return $this->crates[$name] ?? null; }
    public function reload(): void { $this->loadCrates(); }
}
