<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use Config\Service;

class MorseController extends BaseController
{
    //Response Trait for Apı Response
    use ResponseTrait;
    //curl Client For Apı Request
    public $client;
    //Morse Alphabet
    public $morseDictionary = [
        'a' => '.-',
        'b' => '-...',
        'c' => '-.-.',
        'd' => '-..',
        'e' => '.',
        'f' => '..-.',
        'g' => '--.',
        'h' => '....',
        'i' => '..',
        'j' => '.---',
        'k' => '-.-',
        'l' => '.-..',
        'm' => '--',
        'n' => '-.',
        'o' => '---',
        'p' => '.--.',
        'q' => '--.-',
        'r' => '.-.',
        's' => '...',
        't' => '-',
        'u' => '..-',
        'v' => '...-',
        'w' => '.--',
        'x' => '-..-',
        'y' => '-.--',
        'z' => '--..',
        '0' => '-----',
        '1' => '.----',
        '2' => '..---',
        '3' => '...--',
        '4' => '....-',
        '5' => '.....',
        '6' => '-....',
        '7' => '--...',
        '8' => '---..',
        '9' => '----.',
        '.' => '.-.-.-',
        ',' => '--..--',
        '?' => '..--..',
        '/' => '-..-.',
        ' ' => '.......',
    ];
    function __construct()
    {
        $this->client = \Config\Services::curlrequest(); //Set curl to class property
    }
    public function Cpu()
    {
        $code = $this->morseToString($this->request->getPost('command')); //Translate morse code to string
        if ($code == 'cpu') {
            /*
                BEGIN:Server Status
            */
            $timeout = "1";
            $services = array();
            $services[] = array("port" => '80',    "service" => "Web server",          "ip" => "localhost");
            $services[] = array("port" => '21',    "service" => "FTP",                 "ip" => "localhost");
            $services[] = array("port" => '3306',  "service" => "MYSQL",               "ip" => "localhost");
            $services[] = array("port" => '22',    "service" => "Open SSH",            "ip" => "localhost");
            $services[] = array("port" => '58846', "service" => "Deluge",              "ip" => "localhost");
            $services[] = array("port" => '8112',  "service" => "Deluge Web",          "ip" => "localhost");
            $services[] = array("port" => '8083',  "service" => "Vesta panel",         "ip" => "localhost");
            $services[] = array("port" => '80',    "service" => "Internet Connection", "ip" => "google.com");

            foreach ($services  as $serviceKey => $service) {
                $fp = @fsockopen($service['ip'], $service['port'], $errno, $errstr, $timeout); //Test connection
                if (!$fp) {
                    $services[$serviceKey]["status"] = "Offline"; //If connection is not successfull set the status offline
                } else {
                    $services[$serviceKey]["status"] = "Online"; //If connection is successfull set the status online
                    fclose($fp);
                }
            }
            foreach($services as $serviceKey => $service) {
                foreach($service as $key => $val){
                    $service[$key] = $this->stringToMorse($val);
                }
                $services[$serviceKey] = $service;
            }
            /*
                END:Server Information
            */
            /*
                BEGIN:Server Information
            */
            $totalDiskSpace = $this->stringToMorse(number_format(disk_total_space(getcwd()) / 1024 / 1024)); //MB
            $freeDiskSpace = $this->stringToMorse(number_format(disk_free_space(getcwd()) / 1024 / 1024)); //MB
            $ramUsage = $this->stringToMorse(number_format(memory_get_usage() / 1024)); //MB
            $totalRam = trim(explode(':', exec('systeminfo | findstr /C:"Total Physical Memory'))[1]);
            $totalRam = $this->stringToMorse(number_format(intval(explode(' ', $totalRam)[0]) * 1024)); //MB
            $load = null;
            @exec("wmic cpu get loadpercentage /all", $output);
            if ($output)
            {
                foreach ($output as $line)
                {
                    if ($line && preg_match("/^[0-9]+\$/", $line))
                    {
                        $load = $line;
                        break;
                    }
                }
            }
            $cpuUsage = $this->stringToMorse("%".$load);
            /*
                END:Server Information
            */

            $response = [
                "services" => $services,
                "totalDiskSpace" => $totalDiskSpace,
                "freeDiskSpace" => $freeDiskSpace,
                "totalRam" => $totalRam,
                "ramUsage" => $ramUsage,
                "cpuUsage" => $cpuUsage,
            ];
            return $this->respond($response);
        }
    }
    public function ServerInfo()
    {
        $data = $this->client->request("POST", 'localhost/php-server-info/public/morse-api', [
            "form_params" => [
                "command" => "-.-. .--. ..-",
            ],
        ]);
        $data = $data->getBody();
        $data = json_decode($data, true);
        $data['morseDictionary'] = $this->morseDictionary;
        return view("server-info", $data);
    }
    private function morseToString($morseCode)
    {
        $morseLetters = explode(" ", $morseCode);
        $code = '';
        foreach ($morseLetters as $morseLetter) {
            foreach ($this->morseDictionary as $key => $dictionaryLetter) {
                if ($dictionaryLetter == $morseLetter) {
                    $code .= $key;
                }
            }
        }
        return $code;
    }

    private function stringToMorse($string)
    {
        $stringParts = str_split(strtolower($string));
        $morse = '';
        foreach ($stringParts as $stringPart) {
            if (array_key_exists($stringPart, $this->morseDictionary)) {
                $morse .= $this->morseDictionary[$stringPart] . " ";
            }
        }
        return $morse;
    }
}
