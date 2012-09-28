<?php
class BitSwitch implements ArrayAccess {
	
	private $labels = array();
	private $val = 0;

	public function __construct($n = 0, $labels = array()) {
		if(!empty($n) && is_array($n)) {
			
			// Loop through the array and set the switches
			$i=0; foreach($n as $label => $tf) {
				$this->labels[$i] = $label;
				$this->setBit($i, $tf);
				$i++;
			}
			
			return;
		}
		
		// Set the Labels and Val
		$this->labels($labels);
		$this->val = $n;
	}
	
	public function setBit($bit, $val) {
		$val = $val > 0;
		
		if(is_string($bit)) {
			$labels = array_flip($this->labels);				$bit = $labels[$bit];
		}
		
		if(empty($bit) && $bit !== 0) return false;
		
		if($val) $this->val |= pow(2, $bit);
		elseif(!$val && $this->getBit($bit))
			$this->val &= ~pow(2, $bit);
		
		return $this;
	}
	
	public function getBit($bit) {
		
		if(is_string($bit)) {
			$labels = array_flip($this->labels);				$bit = $labels[$bit];
		}
		
		if(empty($bit) && $bit !== 0) return false;
		
		return (pow(2, $bit) & $this->val) > 0;
	}
	
	public function setInt($n) {
		if(!is_numeric($n)) return false;
		$this->val = (int) $n;
		
		return $this;
	}
	
	public function labels($labels = array(), $reset = false) {
		if(!$reset) $this->labels += $labels;
		else $this->labels = $labels;
		
		return $this;
	}
	
	public function  getInt() {
		return $this->val;
	}
	
	public function __toString() {
		if(empty($this->labels))
			return "BitSwitch: ".$this->val;
		
		return json_encode($this->getArray());
	}
	
	public function getArray() {
		if(empty($this->labels))
			throw new Exception("BitSwitch labels are empty. Cannot getArray().");
		
		$serialized = array();
		foreach($this->labels as $bit => $label) {
			$serialized[$label] = $this->getBit($bit);
		}
		
		return $serialized;
	}
	
	/**
	 * Object Access
	 */
	public function __get($bit) {
		return $this->getBit($bit);
	}
	public function __set($bit, $val) {
		return $this->setBit($bit, $val);
	}
	
	/**
	 * Array Access
	 */
	public function offsetGet($bit) {
		return $this->getBit($bit);
	}
	public function offsetSet($bit, $val) {
		return $this->setBit($bit, $val);
	}
	public function offsetExists($bit) {
		return $this->getBit($bit);
	}
	public function offsetUnset($bit) {
		return $this->setBit($bit, 0);
	}
}