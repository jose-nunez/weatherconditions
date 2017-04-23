<?php
	class WeatherContidionsMapDemo{
		function printMap(){

			if(wp_style_is('bcwc_demo_css','registered') && !wp_style_is('bcwc_demo_css','enqueued')) wp_enqueue_style('bcwc_demo_css');
			if(wp_script_is('bcwc_demo_js','registered') && !wp_script_is('bcwc_demo_js','enqueued')) wp_enqueue_script('bcwc_demo_js');
			if(wp_script_is('leaflet','registered') && !wp_script_is('leaflet','enqueued')) wp_enqueue_script('leaflet');
			if(wp_script_is('leaflet-providers','registered') && !wp_script_is('leaflet-providers','enqueued')) wp_enqueue_script('leaflet-providers');

			
			return ' 
				<div class="bcwc-map-frame">
					
				</div>
			';
		}
	}
?>