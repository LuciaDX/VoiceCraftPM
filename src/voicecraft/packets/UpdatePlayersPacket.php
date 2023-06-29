<?php

namespace voicecraft\packets;

class UpdatePlayersPacket extends VoiceCraftPacket{

    public int $Type = 2;

    public function __construct(public string $LoginKey,public array $Players){}

}