<?php

class CSocket {
	private $SOCKET, $OPENED, $HOST, $PORT;
	public $Timeout;
  
	// private constructor function 
	// to prevent external instantiation 
	function __construct() {
		$this->OPENED = false;
		if (!$this->Timeout) $this->Timeout = 10;
	} 
	function __destruct() {
		if ($this->isActive()) $this->close();
  	}
  
	/* ************************************************** */
	/* ************************************************** */
	
	public function open($host, $port, $persist=false) {
		$this->HOST = $host;
		$this->PORT = $port;
		if ($persist) $this->SOCKET = @pfsockopen($host, $port, $errno, $errstr, $this->Timeout);
		else $this->SOCKET = @fsockopen($host, $port, $errno, $errstr, $this->Timeout);
		if (!$this->SOCKET) {
			return "$errstr ($errno)";
		} else { $this->OPENED=true; return "OK"; }
	}
	/* ************************************************** */
	
	public function close() {
		if ($this->OPENED) { fclose($this->SOCKET); $this->OPENED=false; }
		else return "Socket not opened!";
	}
	/* ************************************************** */
	
	public function write($buf) {
		if ($this->OPENED) fwrite($this->SOCKET, $buf);
		else return "Socket not opened!";
	}
	/* ************************************************** */
	
	public function writeln($buf) {
		if ($this->OPENED) fwrite($this->SOCKET, $buf."\r\n");
		else return "Socket not opened!";
	}
	/* ************************************************** */
	
	public function read(&$buf) {
		if ($this->OPENED)  return $buf = fgets($this->SOCKET);
		else return "Socket not opened!";
	}
	/* ************************************************** */
	
	public function getHost() {
		if ($this->OPENED)  return $this->HOST;
		else return "Socket not opened!";
	}
	/* ************************************************** */
	
	public function getPort() {
		if ($this->OPENED)  return $this->PORT;
		else return "Socket not opened!";
	}
	/* ************************************************** */
	
	public function isActive() {
		return $this->OPENED;
	}
	/* ************************************************** */
	
	public function readLoop(&$buf, $max=0) {
		if ($this->OPENED)  {
			$loops=0;
			while (!feof($this->SOCKET)) {
				$buf .= fgets($this->SOCKET);
				if ($max && $max == $loops) {
					return $buf; 
				}
				$loops++;
			}
		}
		else return "Socket not opened!";
	}
	/* ************************************************** */
}

?>