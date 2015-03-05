<?php
class Resource {
	
	const TYPE_JS = 'js';
	const TYPE_CSS  = 'css';
	
	protected $type  = 'js';
	protected $name;
	protected $external   = false;
	
	function __construct($type, $name, $external=false) {
		$this->type = $type;
		$this->name = $name;
		$this->external = $external;
	}
	
	static function js($name){
		return new Resource(self::TYPE_JS, $name, false);
	}
	
	static function css($name){
		return new Resource(self::TYPE_CSS, $name, false);
	}
	
	static function extJs($url){
		return new Resource(self::TYPE_JS, $url, true);
	}
	
	static function extCss($url){
		return new Resource(self::TYPE_CSS, $url, true);
	}
	
	public function render($basePath, $assetsMap=array()){
		$out = '';
		$links = array();
		
		if($this->external || !isset($assetsMap[$this->type][$this->name])){
			$links[]=$this->name;
		} else {
			$links = array();	
			foreach ($assetsMap[$this->type][$this->name] as $l){
				$links[] = $basePath.$l;
			}		
		}
		
		if($this->type==self::TYPE_JS){
			foreach($links as $l){
				$out .= '<script src="'.$l.'"></script>';
			}
		}
		
		if($this->type==self::TYPE_CSS){
			foreach($links as $l){
				$out .= ' <link href="'.$l.'" rel="stylesheet" />';
			}
		}
		
		return $out;
	}
	
	public function __toString() {
		return 'Resource::'.$this->type.'|'.$this->name.'|'.$this->external;
	}
	
}