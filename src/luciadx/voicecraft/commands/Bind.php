<?php

namespace luciadx\voicecraft\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\plugin\PluginOwned;
use luciadx\voicecraft\VoiceCraft;
use luciadx\voicecraft\VoiceCraftPlugin;

class Bind extends Command implements PluginOwned {

    public function __construct(public VoiceCraftPlugin $plugin){
        parent::__construct("vcbind", "Connect to VoiceCraft server", "/vcbind [Code: string]");
        $this->setPermission("voicecraft.bind");
    }

    public function execute(CommandSender $Sender, string $CommandLabel, array $Args): bool{
        if (!$Sender instanceof Player) {
            $Sender->sendMessage("You must run this command in-game.");
            return false;
        }
        if(empty($Args)){
            throw new InvalidCommandSyntaxException();
        }
        VoiceCraft::getInstance()->Bind($Args[0],$Sender);
        return true;
    }

    public function getOwningPlugin(): VoiceCraftPlugin{
        return $this->plugin;
    }
}