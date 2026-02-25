<?php

namespace ZyberCrates;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\item\ItemFactory;

class Main extends PluginBase {

    public CrateManager $crateManager;

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->saveResource("crates.yml");
        $this->crateManager = new CrateManager($this);
        $this->getLogger()->info("ZyberCrates enabled!");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if(!$sender instanceof Player) return false;
        if(!isset($args[0])){
            $sender->sendMessage("Usage: /crate <type|give|list|reload>");
            return true;
        }

        $sub = strtolower($args[0]);
        switch($sub){
            case "list":
                $sender->sendMessage("§aAvailable crates:");
                foreach($this->crateManager->getCrates() as $crate){
                    $sender->sendMessage("§e".$crate->getName());
                }
                break;

            case "reload":
                $this->crateManager->reload();
                $sender->sendMessage("§aCrates reloaded!");
                break;

            case "give":
                if(!$sender->hasPermission("zybercrates.admin")){
                    $sender->sendMessage("§cYou do not have permission!");
                    return true;
                }
                if(!isset($args[1]) || !isset($args[2])){
                    $sender->sendMessage("Usage: /crate give <player> <crate>");
                    return true;
                }
                $player = $this->getServer()->getPlayerByPrefix($args[1]);
                if($player === null){
                    $sender->sendMessage("§cPlayer not found!");
                    return true;
                }
                $crate = $this->crateManager->getCrate($args[2]);
                if($crate === null){
                    $sender->sendMessage("§cCrate not found!");
                    return true;
                }
                $player->getInventory()->addItem($crate->getKeyItem());
                $sender->sendMessage("§aGave ".$player->getName()." a key for ".$crate->getName());
                break;

            default:
                // Treat as crate type to open
                $crate = $this->crateManager->getCrate($args[0]);
                if($crate === null){
                    $sender->sendMessage("§cCrate not found!");
                    return true;
                }

                $item = $crate->getKeyItem();
                if(!$sender->getInventory()->contains($item)){
                    $sender->sendMessage("§cYou do not have the key!");
                    return true;
                }

                $sender->getInventory()->removeItem($item);
                $crate->openCrate($sender);
                break;
        }

        return true;
    }
}
