<?php

namespace voicecraft\tasks;

use pocketmine\scheduler\Task;
use voicecraft\VoiceCraft;

class UpdatePlayers extends Task {

    public function onRun(): void{
        VoiceCraft::getInstance()->UpdatePlayers();
    }

}