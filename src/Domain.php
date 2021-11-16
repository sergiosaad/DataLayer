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
			array_push($domains, ["key"=>$key,"label"=>$label,"selected"=>$selected,"default"=>$default]);
		}
		return $domains;
	}

    public function __get($name)
    {
        return ($this->domains[$name] ?? null);
    }


}