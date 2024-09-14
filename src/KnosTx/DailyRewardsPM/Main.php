<?php

namespace KnosTx\DailyRewardsPM;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase {

    private Config $dataConfig;
    private Config $rewardsConfig;

    public function onEnable() : void {
        @mkdir($this->getDataFolder());
        $this->dataConfig = new Config($this->getDataFolder() . "rewardsData.yml", Config::YAML);
        $this->saveDefaultConfig();
        $this->rewardsConfig = $this->getConfig();
    }

    public function onDisable() : void {
        $this->dataConfig->save();
    }

    public function claimReward(Player $player, string $type): bool {
        $name = $player->getName();
        $currentTime = time();
        $intervals = [
            "daily" => 86400,
            "weekly" => 604800,
            "monthly" => 2592000
        ];

        $lastClaimed = $this->dataConfig->get($name, []);

        if (!isset($lastClaimed[$type])) {
            $lastClaimed[$type] = 0;
        }

        if (($currentTime - $lastClaimed[$type]) >= $intervals[$type]) {
            $lastClaimed[$type] = $currentTime;
            $this->dataConfig->set($name, $lastClaimed);
            $this->dataConfig->save();
            $this->giveConfigItem($player, $type);
            return true;
        }
        return false;
    }

    private function giveConfigItem(Player $player, string $type): void {
        $rewards = $this->rewardsConfig->get($type, []);
        foreach ($rewards as $itemConfig) {
            $item = Item::get($itemConfig['id'], 0, mt_rand($itemConfig['min'], $itemConfig['max']));
            $player->getInventory()->addItem($item);
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if (!$sender instanceof Player) return false;
        if (empty($args[0])) return false;

        $type = strtolower($args[0]);
        if (!in_array($type, ["daily", "weekly", "monthly"])) return false;

        return $this->claimReward($sender, $type);
    }
}
