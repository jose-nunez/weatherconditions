<?php
	class WeatherContidionsMapDemo{
		function printMap(){

			if(wp_style_is('leaflet_css','registered') && !wp_style_is('leaflet_css','enqueued')) wp_enqueue_style('leaflet_css');
			if(wp_script_is('leaflet','registered') && !wp_script_is('leaflet','enqueued')) wp_enqueue_script('leaflet');
			if(wp_script_is('leaflet-providers','registered') && !wp_script_is('leaflet-providers','enqueued')) wp_enqueue_script('leaflet-providers');
			if(wp_script_is('bcwc_demo_js','registered') && !wp_script_is('bcwc_demo_js','enqueued')) wp_enqueue_script('bcwc_demo_js');
			if(wp_style_is('bcwc_demo_css','registered') && !wp_style_is('bcwc_demo_css','enqueued')) wp_enqueue_style('bcwc_demo_css');

			
			return '
				<div id="bcwc-map">
					
				</div>
			';
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