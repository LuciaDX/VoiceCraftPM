<?php

namespace luciadx\voicecraft\packets;

class VoiceCraftPacket implements \JsonSerializable {

    public function jsonSerialize(): mixed{
        return get_object_vars($this);
    }

}