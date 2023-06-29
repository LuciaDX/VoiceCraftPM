<?php

namespace voicecraft;

use pocketmine\plugin\PluginBase;
use voicecraft\commands\Bind;
use voicecraft\commands\Settings;
use voicecraft\tasks\UpdatePlayers;

class VoiceCraftPlugin extends PluginBase{

    protected function onEnable(): void{
        $this->saveDefaultConfig();
        $Config = $this->getConfig()->getAll();
        $VC = new VoiceCraft($Config["Ip"],$Config["Port"],$Config["LoginKey"],new ServerSettings($Config["Settings"]["ProximityDistance"],$Config["Settings"]["ProximityToggle"]));
        $VC->Connect();
        $VC->SendSettings();
        $this->getScheduler()->scheduleRepeatingTask(new UpdatePlayers(),1);
        $this->getServer()->getCommandMap()->registerAll("voicecraft", [
            new Bind($this),
            new Settings($this)
        ]);
    }

    protected function onDisable(): void{
        VoiceCraft::getInstance()->Thread->quit();
    }

}