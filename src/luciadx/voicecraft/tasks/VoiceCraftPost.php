<?php

namespace luciadx\voicecraft\tasks;

use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Internet;
use luciadx\voicecraft\packets\VoiceCraftPacket;

class VoiceCraftPost extends AsyncTask{

    public ?string $Data;

    public function __construct(string $Ip,string $Port,VoiceCraftPacket $Packet, \Closure $Completion){
        $this->Data = igbinary_serialize([$Ip,$Port,json_encode($Packet)]);
        $this->storeLocal("CompletionCallback", $Completion);
    }

    public function onRun(): void{
        $Data = igbinary_unserialize($this->Data);
        $this->setResult(Internet::postURL("http://{$Data[0]}:{$Data[1]}",$Data[2],10, ["Content-Type: application/json"]));
    }

    public function onCompletion(): void{
        $Callback = $this->fetchLocal("CompletionCallback");
        $Result = $this->getResult();
        $Callback($Result);
    }

}