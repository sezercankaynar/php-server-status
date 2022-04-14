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
        ' ' => ' ',
    ];
    function __construct()
    {
        $this->client = \Config\Services::curlrequest(); //Set curl to class property
    }
    public function Cpu()
    {

        helper('morse'); // Call morse helper for translations
        $code = morseToString($this->request->getPost('command'), $this->morseDictionary); //Translate morse code to string
        if ($code == 'cpu') {
            /*
                BEGIN:Server Status
            */
            $timeout = "1";
            $services = array();
            $services[] = array("port" => stringToMorse('80', $this->morseDictionary),    "service" => stringToMorse("Web server", $this->morseDictionary),          "ip" => stringToMorse("localhost", $this->morseDictionary), "status" => "");
            $services[] = array("port" => stringToMorse('21', $this->morseDictionary),    "service" => stringToMorse("FTP", $this->morseDictionary),                 "ip" => stringToMorse("localhost", $this->morseDictionary), "status" => "");
            $services[] = array("port" => stringToMorse('3306', $this->morseDictionary),  "service" => stringToMorse("MYSQL", $this->morseDictionary),               "ip" => stringToMorse("localhost", $this->morseDictionary), "status" => "");
            $services[] = array("port" => stringToMorse('22', $this->morseDictionary),    "service" => stringToMorse("Open SSH", $this->morseDictionary),            "ip" => stringToMorse("localhost", $this->morseDictionary), "status" => "");
            $services[] = array("port" => stringToMorse('58846', $this->morseDictionary), "service" => stringToMorse("Deluge", $this->morseDictionary),              "ip" => stringToMorse("localhost", $this->morseDictionary), "status" => "");
            $services[] = array("port" => stringToMorse('8112', $this->morseDictionary),  "service" => stringToMorse("Deluge Web", $this->morseDictionary),          "ip" => stringToMorse("localhost", $this->morseDictionary), "status" => "");
            $services[] = array("port" => stringToMorse('8083', $this->morseDictionary),  "service" => stringToMorse("Vesta panel", $this->morseDictionary),         "ip" => stringToMorse("localhost", $this->morseDictionary), "status" => "");
            $services[] = array("port" => stringToMorse('80', $this->morseDictionary),    "service" => stringToMorse("Internet Connection", $this->morseDictionary), "ip" => stringToMorse("google.com", $this->morseDictionary), "status" => "");

            foreach ($services  as &$service) {
                $fp = @fsockopen(morseToString(trim($service['ip']), $this->morseDictionary), morseToString($service['port'], $this->morseDictionary), $errno, $errstr, $timeout); //Test connection
                if (!$fp) {
                    $service['status'] = stringToMorse("Offline", $this->morseDictionary); //If connection is not successfull set the status offline
                } else {
                    $service['status'] .= stringToMorse("Online", $this->morseDictionary); //If connection is successfull set the status online
                    fclose($fp);
                }
            }
            /*
                END:Server Information
            */
            /*
                BEGIN:Server Information
            */
            $totalDiskSpace = stringToMorse(number_format(disk_total_space(getcwd()) / 1024 / 1024), $this->morseDictionary); //MB
            $freeDiskSpace = stringToMorse(number_format(disk_free_space(getcwd()) / 1024 / 1024), $this->morseDictionary); //MB
            $ramUsage = stringToMorse(number_format(memory_get_usage() / 1024), $this->morseDictionary); //MB
            $totalRam = trim(explode(':', exec('systeminfo | findstr /C:"Total Physical Memory'))[1]);
            $totalRam = stringToMorse(number_format(intval(explode(' ', $totalRam)[0]) * 1024), $this->morseDictionary); //MB
            /*
                END:Server Information
            */

            $response = [
                "services" => $services,
                "totalDiskSpace" => $totalDiskSpace,
                "freeDiskSpace" => $freeDiskSpace,
                "totalRam" => $totalRam,
                "ramUsage" => $ramUsage
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
}
