<?php
require("session_check.php");
//-----------------------------------------------------------------

class GXML {
	
	private $XML, $FILE, $FOUND;
	//-----------------------------------------------------------------
	
	function __construct($file, $load=false) {
		$this->FOUND=0;
		$this->FileCheck($file);
		if ($load) {
			$this->XML = simplexml_load_file($this->FILE);
		}
	}
	//-----------------------------------------------------------------
	
	private function FileCheck($file) {
		if (!file_exists($file)) $this->FILE = "charts/default.xml";
		else $this->FILE = $file;
		
		return $this->FILE;
	}
	//-----------------------------------------------------------------
	
	function GetXml() {
		return $this->XML;
	}
	//-----------------------------------------------------------------
	
	function SaveXml($file) {
		if ($this->XML->asXML($file))		return $file; 
		else return "FAIL";
	}
	//-----------------------------------------------------------------
	 
	public function LoadXml($file="default.xml") {
		$this->XML = simplexml_load_file($this->FileCheck($file));
	}
	//-----------------------------------------------------------------
	
	private function AddElement($elem, $val=0, $attr=0, &$selem=0) 
	{
		$tmp = NULL;
		if ($selem) {
			if ($val) $tmp = $selem->addChild($elem, $val);
			else $tmp = $selem->addChild($elem);
		}
		else {
			if ($val) $tmp = $this->XML->addChild($elem, $val);
			else $tmp = $this->XML->addChild($elem);
		}
		
		if ($attr) {
			foreach ($attr as $key=>$value) {
				$tmp->addAttribute($key, $value);
			}
		}
		return $tmp;
	}
	//-----------------------------------------------------------------
	
	public function GetElem($elem) {
		foreach ($this->XML->children() as $selem) {
			if ($selem->getName() == $elem)
				return $selem;
		}
		return false;
	}
	//-----------------------------------------------------------------
	
	public function AddElem($elem, $val=0, $into=0, $attr=0) {
		if (!$into) return $this->AddElement($elem, $val, $attr);
		else
		foreach ($this->XML->children() as $selem) {
			if ($selem->getName() == $into)
				return $this->AddElement($elem, $val, $attr, $selem);
		}
		return false;
	}
	//-----------------------------------------------------------------
	
	public function AddAttr($elem, $attr, $val) {
		foreach ($this->XML->children() as $selem) {
			if ($selem->getName() == $elem) {
				$selem->addAttribute($attr, $val);
				return  true;
			}
		}
		return false;
	}
	//-----------------------------------------------------------------
	
	public function GetAttr($elem, $attr) {
		foreach ($this->XML->children() as $selem) {
			if ($selem->getName() == $elem)
				return $selem[$attr];
		}
		return false;
	}
	//-----------------------------------------------------------------
	
	public function AttrExists($elem, $attr, $val, $Dparent=0) {
		if ($this->FOUND) return true;
		foreach ($Dparent?$Dparent->children():$this->XML->children() as $child) {
			if ($child->getName() == $elem && $child[$attr] == $val)
				{ $this->FOUND=$child; return true; }
			else if ($child) $this->AttrExists($elem, $attr, $val,$child); 
		}
		return $this->FOUND;
	}
	//-----------------------------------------------------------------
	
	public function AttrReset()
	{
		$this->FOUND=0;
	}
	//-----------------------------------------------------------------
	
	public function SetAttr($elem, $attr, $val) {
		foreach ($this->XML->children() as $selem) {
			if ($selem->getName() == $elem) {
				$selem[$attr] = $val;
				return  true;
			}
		}
		return false;
	}
	//-----------------------------------------------------------------
	
	public function PrintAll() {
		print_r($this->XML);
	}
	//-----------------------------------------------------------------

}
?>