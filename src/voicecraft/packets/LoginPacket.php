<?php

namespace voicecraft\packets;

class LoginPacket extends VoiceCraftPacket{

    public int $Type = 0;

    public function __construct(public string $LoginKey){}

}