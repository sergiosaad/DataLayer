<?php

namespace SergioSaad\DataLayer;

class Domain
{
	private $domains;
	private $default;
	public $value;

	public function __construct($domains) {
		$this->domains = [];
		foreach ($domains as $domain) {
			$key = (String) $domain[0];
			$label =  (String) $domain[1];
			$this->domains[$key] = $label;
			if($domain[2]) {
				$this->default = $key;
				$this->value = $key;
			}
		}
	}

	public function list() {
		$domains = [];
		foreach ($this->domains as $key => $label) {
			$selected = $key == $this->value ? "Y" : "";
			$default = $key == $this->default ? "Y" : "";
			$obj = new \stdClass();
			$obj->key = $key;
			$obj->label = $label;
			$obj->isSelected = $selected;
			$obj->isDefault = $default;
			$domains[$key] = $obj;
		}
		return $domains;
	}

    public function __get($name)
    {
        if($name == "label") {
			$name = $this->value;
		}
		return ($this->domains[$name] ?? null);
    }


}