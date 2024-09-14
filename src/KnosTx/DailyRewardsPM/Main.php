<?php

namespace KnosTx\DailyRewardsPM;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase {

  private $rewards;

  public function __construct() {
    //NOOP
  }

  public function onEnable() : void {
    //NOOP
  }

  public function getRewards() : bool {
    $this->rewards;
    return false;
  }
}
