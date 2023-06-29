<?php

namespace voicecraft\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\plugin\PluginOwned;
use voicecraft\ServerSettings;
use voicecraft\VoiceCraft;
use voicecraft\VoiceCraftPlugin;

class Settings extends Command implements PluginOwned {

    public function __construct(public VoiceCraftPlugin $plugin){
        parent::__construct("vcsettings", "Change VoiceCraft server settings", "/vcsettings [ProximityDistance: int] [ProximityToggle: 0/1]");
        $this->setPermission("voicecraft.settings");
    }

    public function execute(CommandSender $Sender, string $CommandLabel, array $Args): bool{
        if(count($Args) < 2){
            throw new InvalidCommandSyntaxException();
        }
        VoiceCraft::getInstance()->Settings = new ServerSettings(intval($Args[0],boolval($Args[1])));
        VoiceCraft::getInstance()->SendSettings($Sender);
        return true;
    }

    public function getOwningPlugin(): VoiceCraftPlugin{
        return $this->plugin;
    }
}