<?php

namespace luciadx\voicecraft\packets;

use luciadx\voicecraft\ServerSettings;

class UpdateSettingsPacket extends VoiceCraftPacket{

    public int $Type = 3;

    public function __construct(public string $LoginKey,public ServerSettings $Settings){}

}