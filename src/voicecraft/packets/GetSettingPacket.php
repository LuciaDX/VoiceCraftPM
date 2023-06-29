<?php

namespace voicecraft\packets;

class GetSettingPacket extends VoiceCraftPacket{

    public int $Type = 4;

    public function __construct(public string $LoginKey){}

}