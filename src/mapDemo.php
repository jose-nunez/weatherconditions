<?php
	class WeatherContidionsMapDemo{
		function printMap($height='500px',$width='100%'){

			if(wp_style_is('leaflet_css','registered') && !wp_style_is('leaflet_css','enqueued')) wp_enqueue_style('leaflet_css');
			if(wp_script_is('leaflet','registered') && !wp_script_is('leaflet','enqueued')) wp_enqueue_script('leaflet');
			if(wp_script_is('leaflet-providers','registered') && !wp_script_is('leaflet-providers','enqueued')) wp_enqueue_script('leaflet-providers');
			if(wp_script_is('bcwc_demo_js','registered') && !wp_script_is('bcwc_demo_js','enqueued')) wp_enqueue_script('bcwc_demo_js');
			if(wp_style_is('bcwc_demo_css','registered') && !wp_style_is('bcwc_demo_css','enqueued')) wp_enqueue_style('bcwc_demo_css');

			
			echo '<pre>'.print_r($height.' '.$width,true).'</pre>';	

			return '
				<div id="bcwc-map" style="height:'.$this->standarSize($height) .';width:'.$this->standarSize($width).'">
					
				</div>
			';
		}

		function standarSize($dimention){
			$dimention = ''.$dimention;
			if(is_string($dimention) && strpos($dimention,'px')===false && strpos($dimention,'%')===false){
				$val = intval($dimention);
				if($val==0) $val=300; 
				return ''. $val .'px';
			}
			else return $dimention;
		}

		function initServices(){
			add_action('wp_ajax_nopriv_bcwc_demo_getChileBounds', array($this,'getChileBoundsService'));
			add_action('wp_ajax_bcwc_demo_getChileBounds', array($this,'getChileBoundsService'));
		}

		function getChileBoundsService(){
			die($this->getChileBounds());
		}
		function getChileBounds(){
			$chilebounds = BCWC_URL . "data/chilebounds.geojson";
			return file_get_contents($chilebounds);
		}
	}
?>