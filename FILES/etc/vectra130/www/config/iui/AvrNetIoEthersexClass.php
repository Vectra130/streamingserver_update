 <?php

/*
 * AvrNetIo PHP Class von Sascha Kimmel steht unter einer Creative Commons
 * Namensnennung-Weitergabe unter gleichen Bedingungen 3.0 Deutschland Lizenz.
 * http://creativecommons.org/licenses/by-sa/3.0/de/
 *
 * Anleitung und Infos:
 * http://www.sascha-kimmel.de/2010/02/avr-net-io-mit-php-ansteuern/
 *
 * Class abgeändert zum Betrieb der AvrNetIo Karte mit Ethersex von Vectra130
 */

class AvrNetIo
{
    protected $ip;
    protected $conn;
    protected $timeout = 5;

//    protected $lcdInitialized;

    const STATUS_RAW          = 1;
    const STATUS_ARRAY_BOOL   = 2;
    const STATUS_ARRAY_STRING = 3;

    public function __construct($ip)
    {
        $this->ip = $ip;
    }

    public function connect()
    {
    	$port = 50000 + (int)substr($this->ip, -3);
        $this->conn = fsockopen($this->ip, $port, $errno, $errstr, $this->timeout);
        return (bool)$this->conn;
    }

    public function disconnect()
    {
        return fclose($this->conn);
    }

    protected function read($cmd, $lines)
    {
        fputs($this->conn, trim($cmd)."\r\n");
        $results = array();
        for ($i=0; $i<$lines; $i++) {
            $results[] = trim(fgets($this->conn, 65535));
        }
        return $results;
    }

    public function getVersion()
    {
        $info = $this->read("VERSION", 3);
        $data = array();
        foreach ($info as $l) {
            list($n, $v) = explode(":", $l);
            $v = trim($v);
            $data[strtolower($n)] = $v;
            
        }
        return $data;
    }

    public function getStatus($returnType = self::STATUS_RAW)
    {
        $r    = $this->read("io get pin 2", 1);
        $hex  = substr($r[0], -2);
        $r[0] = "S".sprintf("%'08d",decbin(hexdec($hex)));
        $data = $r[0];

        if ($returnType == self::STATUS_RAW) {
            return $r[0];
          } else {
            $array = array();
            if ($returnType == self::STATUS_ARRAY_BOOL) {
                for ($i=1; $i<strlen($data); $i++) {
                    $char = substr($data, $i, 1);
                    $array[] = (bool)$char;
                }
            } else if ($returnType == self::STATUS_ARRAY_STRING) {
                for ($i=1; $i<strlen($data); $i++) {
                    $char = substr($data, $i, 1);
                    $array[] = (int)$char;
                }                
            }
        }
        return $array;
    }
   
    public function getPin($ddr, $number)
    {
        $r    = $this->read("io get pin ".$ddr, 1);
        $hex  = substr($r[0], -2);
        $data = sprintf("%'08d",decbin(hexdec($hex)));
        $r[0] = substr($data, (8-$number), 1);
        return (int)$r[0];
    }

    public function getPort($number)
    {
        $r    = $this->read("io get port 0", 1);
        $hex  = substr($r[0], -2);
        $data = sprintf("%'08d",decbin(hexdec($hex)));
        $r[0] = substr($data, (8-$number), 1);
        return (int)$r[0];
    }

    public function getDDR($number)
    {
        $r    = $this->read("io get ddr 0", 1);
        $hex  = substr($r[0], -2);
        $data = sprintf("%'08d",decbin(hexdec($hex)));
        $r[0] = substr($data, (8-$number), 1);
        return (int)$r[0];
    }

    public function setPort($i, $number, $value)
    {
        $hex  = dechex(pow(2,((int)$number - 1)));
        if ($value) {
            $value = sprintf("%'02d",$hex);
        } else {
            $value = "00";
        }
        $r = $this->read("io set port ".$i." ".$value." ".$hex, 1);
        return $this->resultToBool($r[0]);
    }

    public function setPortHex($i, $number, $value)
    {
        if ($value) {
            $value = $number;
        } else {
            $value = "00";
        }
        $r = $this->read("io set port ".$i." ".$value." ".$number, 1);
        return $this->resultToBool($r[0]);
    }

    public function setDDR($number, $value)
    {
        $hex  = dechex(pow(2,((int)$number - 1)));
        if ($value) {
            $value = sprintf("%'02d",$hex);
        } else {
            $value = "00";
        }
        $r = $this->read("io set ddr 0 ".$value." ".$hex, 1);
        return $this->resultToBool($r[0]);
    }

    public function getAdc($number)
    {
        $r = $this->read("adc get ".((int)$number - 1), 1);
        $r[0] = hexdec($r[0]);
        return (int)$r[0];
    }

    public function getOneWire($id)
    {
        $list = $this->read("1w list");
        for($i=0; $i<(count($list)-1); $i++) {
        	if (substr($list[$i], 0, 16) == $id) {
  		      $rx = $this->read("1w convert ".$id);
    		  $r  = $this->read("1w get ".$id);
    		}
    	}
		if("$r[0]" == "") {
  		  	$r[0] = "--";
  		}    		  
        return $r[0];
    }

    protected function resultToBool($result)
    {
        return ($result == 'OK');
    }
}
?>
