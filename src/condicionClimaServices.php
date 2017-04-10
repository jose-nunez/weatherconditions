<?php 

class bcwc_condicionClimaServices{

	static $checkingCalls = true;

	function initServices(){
		add_action('wp_ajax_nopriv_bcwc_cc_getState', array($this,'getStateService'));
		add_action('wp_ajax_bcwc_cc_getState', array($this,'getStateService'));
	}

	function addOptions(){
		add_option('bcwc_cc_counter',array('value'=>0,'time'=>DateTime::getTimestamp()));
	}
	
	function cliente(){
		return '
			<li>
				Temperatura 
				<span class="bcwc_cc_temp"></span>
				<img class="bcwc_icon bcwc_cc_icono" src="'. BCWC_URL .'img/loading.gif" />
				<span class="bcwc_cc_descripcion"></span><br/>
				<span class="bcwc_cc_tmax"></span><span class="bcwc_cc_tmin"></span><span class="bcwc_cc_humedad"></span>
			</li>
		';
	}

	function getStateService(){
		$latlng = $_POST['latlng'];
		die(json_encode($this->getState($latlng)));
	}
	function getState($latlng){
		// $url = "http://api.openweathermap.org/data/2.5/find?&APPID=7529c949ef6d31fb040411e2d9302ca8&lat=-33.4575106034059&lon=-70.58046340942381&lang=es&units=metric";
		$url =  "http://api.openweathermap.org/data/2.5/weather?&APPID=7529c949ef6d31fb040411e2d9302ca8&lat=".$latlng['lat']."&lon=".$latlng['lng']."&lang=es&units=metric";
		$state = json_decode(file_get_contents($url),true);
		$url =  "http://api.openweathermap.org/data/2.5/forecast/daily?&APPID=7529c949ef6d31fb040411e2d9302ca8&lat=".$latlng['lat']."&lon=".$latlng['lng']."&lang=es&units=metric";
		$plus = json_decode(file_get_contents($url),true);
		
		if($state && $plus){
			// return array($state);
			return array(
				'comuna'		=>$state['name']
				,'humedad'		=>$state['main']['humidity']
				,'temp'			=>round($state['main']['temp'],1)
				,'descripcion'	=>$state['weather'][0]['description']
				,'icono'		=>$state['weather'][0]['icon']
				,'tmax'			=>round($plus['list'][0]['temp']['max'],1)
				,'tmin'			=>round($plus['list'][0]['temp']['min'],1)
			);
		}
		else return 'error';
	}

	function checkCalls(){
		
		self::$checkingCalls = true;

		$counter = $this->getCounter();
		$calls = $counter['value'];
		$last = $counter['time'];
		$now = DateTime::getTimestamp();
		// ((($now-$last)/60000)>1) // Ha pasado +1 minuto desde el ultimo llamado
	}

	function getCounter(){
		return get_option('bcwc_cc_counter');
	}

}

?>