<?php

namespace luciadx\voicecraft;

use luciadx\voicecraft\packets\VoiceCraftPacket;

class ServerSettings extends VoiceCraftPacket {

    public function __construct(public int $ProximityDistance = 30, public bool $ProximityToggle = true){}

}