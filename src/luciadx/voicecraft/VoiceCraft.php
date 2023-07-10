<?php

namespace luciadx\voicecraft;

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\DimensionNameIds;
use pocketmine\player\Player;
use pmmp\thread\ThreadSafeArray;
use pocketmine\Server;
use pocketmine\utils\InternetRequestResult;
use luciadx\voicecraft\packets\BindingPacket;
use luciadx\voicecraft\packets\LoginPacket;
use luciadx\voicecraft\packets\UpdatePlayersPacket;
use luciadx\voicecraft\packets\UpdateSettingsPacket;
use luciadx\voicecraft\tasks\VoiceCraftPost;

class VoiceCraft{

    public static self $instance;
    public bool $Connected = false;
    public UpdateThread $Thread;
    public ThreadSafeArray $Updates;

    public function __construct(public string $Ip,public string $Port,public string $LoginKey,public ServerSettings $Settings){
        self::$instance = $this;
        $this->Updates = new ThreadSafeArray();
        $this->Thread = new UpdateThread($this->Updates);
        $this->Thread->start();
    }

    public static function getInstance(): self{
        return self::$instance;
    }

    public function ConnectResponse(bool $Status): void{
        $this->Connected = $Status;
        switch($Status){
            case true:
                $this->SendSettings();
                Server::getInstance()->getLogger()->info("Connected to $this->Ip:$this->Port successfully!");
                break;
            case false:
                $this->ConnectError();
        }
    }

    public function ConnectError(): void{
        $this->Connected = false;
        Server::getInstance()->getLogger()->error("Failed to connect to $this->Ip:$this->Port");
    }

    public function Connect(): void{
        $VC = $this;
        Server::getInstance()->getAsyncPool()->submitTask(new VoiceCraftPost(
            $this->Ip,
            $this->Port,
            new LoginPacket($this->LoginKey),
            function($Result) use ($VC){
                if($Result instanceof InternetRequestResult) {
                    switch ($Result->getCode()) {
                        case 200:
                            $VC->ConnectResponse(true);
                            break;
                        case 403:
                            $VC->ConnectResponse(false);
                    }
                } else {
                    $VC->ConnectResponse(false);
                }
            }
        ));
    }

    public function SendSettings(?CommandSender $Sender = null): void{
        if(!$this->Connected){
            return;
        }
        $VC = $this;
        Server::getInstance()->getAsyncPool()->submitTask(new VoiceCraftPost(
            $this->Ip,
            $this->Port,
            new UpdateSettingsPacket($this->LoginKey,$this->Settings),
            function($Result) use ($VC, $Sender){
                if($Result instanceof InternetRequestResult) {
                    if($Result->getCode() == 200){
                        Server::getInstance()->getLogger()->info("Updated settings successfully!");
                        if($Sender){
                            $Sender->sendMessage("Updated settings successfully!");
                        }
                    }
                } else {
                    $VC->ConnectError();
                    if($Sender){
                        $Sender->sendMessage("Failed to connect to $VC->Ip:$VC->Port");
                    }
                }
            }
        ));
    }

    public function Bind(string $Code, ?Player $Player): void{
        if(!$this->Connected){
            return;
        }
        $VC = $this;
        Server::getInstance()->getAsyncPool()->submitTask(new VoiceCraftPost(
            $this->Ip,
            $this->Port,
            new BindingPacket($this->LoginKey,$Player->getUniqueId()->toString(), $Code, $Player->getName()),
            function($Result) use ($VC, $Player){
                if($Result instanceof InternetRequestResult) {
                    if($Result->getCode() == 202){
                        if(is_null($Player)){
                            return;
                        }
                        Server::getInstance()->getLogger()->info("Player {$Player->getName()} successfully connected to VoiceCraft!");
                        $Player->sendMessage("Successfully connected to VoiceCraft!");
                    } else {
                        if(is_null($Player)){
                            return;
                        }
                        Server::getInstance()->getLogger()->info("Player {$Player->getName()} could not connect to VoiceCraft");
                        $Player->sendMessage("Could not find key, it has already been binded to a participant or you are already connected!");
                    }
                } else {
                    $VC->ConnectError();
                    if(!is_null($Player)){
                        $Player->sendMessage("Failed to connect to $VC->Ip:$VC->Port");
                    }
                }
            }
        ));
    }

    public function UpdatePlayers(): void{
        if(!$this->Connected){
            return;
        }
        $PlayerList = Server::getInstance()->getOnlinePlayers();
        if(empty($PlayerList)){
            return;
        }
        $Players = [];
        foreach($PlayerList as $N => $Player){
            $Players[] = [
                "PlayerId" => $Player->getUniqueId()->toString(),
                "DimensionId" => DimensionNameIds::OVERWORLD,
                "Location" => [
                    "x" => $Player->getEyePos()->getX(),
                    "y" => $Player->getEyePos()->getY(),
                    "z" => $Player->getEyePos()->getZ()
                ],
                "Rotation" => $Player->getLocation()->getYaw()
            ];
        }
        $this->Updates[]= igbinary_serialize([$this->Ip,$this->Port,json_encode((new UpdatePlayersPacket($this->LoginKey,$Players)))]);
    }

}