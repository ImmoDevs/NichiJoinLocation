<?php

namespace MadeAja\NichiJoinLocation;

use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Internet;
use function json_decode;

class LocationTask extends AsyncTask
{
    private string $playerAddress;
    private string $playerName;

    public function __construct(string $ip, string $name)
    {
        $this->playerAddress = $ip;
        $this->playerName = $name;
    }

    public function onRun(): void
    {
        $data = Internet::getURL("http://ip-api.com/json/$this->playerAddress")->getBody();
        $data = json_decode($data, true);
        if (isset($data["message"]) && $data["message"] === "private range") {
            $data["country"] = "server";
            $data["city"] = "server";
        }
        $list[$this->playerName] = [
            "region" => $data["country"] ?? "Unknown", 
            "city" => $data['city'] ?? "Unknown"
        ];
        $this->setResult($list);
    }

    public function onCompletion(): void
    {
        $main = Main::getInstance();
        $result = $this->getResult();
        if ($main !== null && isset($result[$this->playerName])) {
            $main->displayBroadcast($result[$this->playerName]['region'], $result[$this->playerName]['city'], $this->playerName);
        }
    }
}
