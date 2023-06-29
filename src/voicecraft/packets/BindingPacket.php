<?php

namespace voicecraft\packets;

class BindingPacket extends VoiceCraftPacket{

    public int $Type = 1;

    public function __construct(public string $LoginKey,public string $PlayerId,public string $PlayerKey,public string $Gamertag){}

}