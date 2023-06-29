<?php

namespace voicecraft;

use pmmp\thread\ThreadSafeArray;
use pocketmine\thread\Thread;
use pocketmine\utils\Internet;

class UpdateThread extends Thread{

    public function __construct(public ThreadSafeArray $Updates){}

    protected function onRun() : void{
        while(!$this->isKilled){
            while(is_string(($Serialized = $this->Updates->shift()))){
                $Data = igbinary_unserialize($Serialized);
                Internet::postURL("http://{$Data[0]}:{$Data[1]}",$Data[2],10, ["Content-Type: application/json"]);
            }
            usleep(50000);
        }
    }
}