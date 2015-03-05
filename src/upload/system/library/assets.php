<?php
class Assets {
	
	protected $assetsMap = array();
	
	protected $header = array();
	
	protected $footer = array();
	
	protected $registry;
	
	function __construct($registry) {
		$this->registry = $registry;
	}
	
	function toHeader($res){
		$this->header[]=$res;
	}
	
	function toFooter($res){
		$this->footer[]=$res;
	}
	
	public function render($output){
		$basePath = DIR_TEMPLATE . $this->config->get('config_template').'/';
		$baseUrl = 'catalog/view/theme/'.$this->config->get('config_template').'/';
		
		$assetsMap = $basePath.'assets.json';
		$versionsMap = $basePath.'assets/versions.json';
		
		if(!file_exists($assetsMap) || !file_exists($versionsMap)){
			throw new \RuntimeException('Assets recompilation needed');
		}
		
		$this->assetsMap = json_decode(file_get_contents($assetsMap), true);
		
		if(defined('COOL_THEME_ENV') && COOL_THEME_ENV=='prod'){
			$versionsMap = json_decode(file_get_contents($versionsMap), true);			
			foreach ($versionsMap as $v){
				preg_match('/.*\/([a-zA-Z0-9]+).min.(js|css)$/', $v['originalPath'], $matches);
				if(isset($matches[1]) && isset($this->assetsMap[$matches[2]][$matches[1]]) ){
					$this->assetsMap[$matches[2]][$matches[1]] = array( $v['versionedPath'] );
				}
			}
		}
		
		$out = "";
		
		foreach (array_unique($this->header) as $l){
			$out .= $l->render($baseUrl, $this->assetsMap);
		}
		$output = str_replace('<!-- end-header -->', $out, $output);
		
		foreach (array_unique($this->footer) as $l){
			$out .= $l->render($baseUrl, $this->assetsMap);
		}
		$output = str_replace('<!-- end-body -->', $out, $output);
		
				
		return $output;
	}
	
	public function __get($key) {
		return $this->registry->get($key);
	}
	
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
	
}