<?php

namespace luciadx\voicecraft\tasks;

use pocketmine\scheduler\Task;
use luciadx\voicecraft\VoiceCraft;

class UpdatePlayers extends Task {

    public function onRun(): void{
        VoiceCraft::getInstance()->UpdatePlayers();
    }

}