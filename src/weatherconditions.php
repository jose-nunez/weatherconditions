<?php 
/**
 * @package Bicicultura_Weather_Conditions
 * @version 1.0
 */
/*
Plugin Name: Bicicultura Weather Conditions
Plugin URI: www.bicicultura.cl
Description: Añade widget con información del tiempo, radiación solar y calidad del aire en Chile.
Version: 1.0
Author: José Núñez (Bicicultura)
Author URI: www.bicicultura.cl
*/

/* DEFINE CONSTANTES 
http://api.worldweatheronline.com/free/v2/weather.ashx?q=santiago,chile&key=af7c504ce33fbdf1c2df0d369aeb0&lang=es&format=json&showmap=yes
http://cdnjs.cloudflare.com/ajax/libs/jstimezonedetect/1.0.4/jstz.min.js

api.openweathermap.org/data/2.5/weather?lat=-33.4575106034059&lon=-70.58046340942381&lang=es&units=metric

*/
define('BCWC_URL',plugin_dir_url( __FILE__ ));
define('BCWC_ADMIN_URL',get_admin_url().'admin.php?page=bicicultura-weatherconditions/bicicultura-weatherconditions.php');

class bcWeatherConditions{

	private $services_loaded = false;
	private $servicesAdmin_loaded = false;

	/* INSTALACION ************************************************************/
	function install(){
		require_once 'calidadAireServices.php';global $bcwc_calidadAire; if(!$bcwc_calidadAire) $bcwc_calidadAire = new bcwc_calidadAireServices();
		$bcwc_calidadAire->loadStations();
		require_once 'radiacionSolarServices.php';global $bcwc_radiacionSolar; if(!$bcwc_radiacionSolar) $bcwc_radiacionSolar = new bcwc_radiacionSolarServices();
		$bcwc_radiacionSolar->loadStations();

		add_option('bcwc_use_html5_location',false);
		add_option('bcwc_demoId',true);
	}
	function uninstall(){
		delete_option('bcwc_use_html5_location');
		// delete_option('bcwc_demoId');
		delete_option('bcwc_ca_stations');
		delete_option('bcwc_rs_stations');
	}
	
	/* INICIACION ************************************************************/
	function init(){
		add_action('admin_menu',array($this,'menu'));
		wp_register_style('bcwc_css',BCWC_URL.'css/style.css');
		wp_register_script('bcwc_amin_js',BCWC_URL.'js/bc_weatherconditions_admin.min.js','jquery');
		wp_register_script('bcwc_js',BCWC_URL.'js/bc_weatherconditions.min.js','jquery');


		require_once 'calidadAireServices.php';global $bcwc_calidadAire; if(!$bcwc_calidadAire) $bcwc_calidadAire = new bcwc_calidadAireServices();
		$bcwc_calidadAire->initServices();

		require_once 'radiacionSolarServices.php';global $bcwc_radiacionSolar; if(!$bcwc_radiacionSolar) $bcwc_radiacionSolar = new bcwc_radiacionSolarServices();
		$bcwc_radiacionSolar->initServices();

		require_once 'condicionClimaServices.php';global $bcwc_condicionClima; if(!$bcwc_condicionClima) $bcwc_condicionClima = new bcwc_condicionClimaServices();
		$bcwc_condicionClima->initServices();

		/*require_once 'mapDemo.php';global $bcwc_condicionClima; if(!$bcwc_condicionClima) $bcwc_condicionClima = new bcwc_condicionClimaServices();
		$bcwc_condicionClima->initServices();*/

		add_action('widgets_init', array($this,'widgets'));
		add_shortcode('bicicultura-weatherconditions',array($this,'shortcode'));
		
		
		wp_register_script('leaflet',BCWC_URL.'lib/leaflet.min.js');
		wp_register_style('leaflet_css',BCWC_URL.'lib/leaflet.css');
		wp_register_script('leaflet-providers',BCWC_URL.'lib/leaflet-providers.min.js','leaflet');
		
		wp_register_script('bcwc_demo_js',BCWC_URL.'js/bc_weatherconditions_demo.min.js','leaflet');
		wp_register_style('bcwc_demo_css',BCWC_URL.'css/style_demo.css');
		
		require_once 'mapDemo.php';global $bcwc_demo; if(!$bcwc_demo) $bcwc_demo = new WeatherContidionsMapDemo();
		$bcwc_demo->initServices();
		add_shortcode('bicicultura-weatherconditions-map',array($this,'shortcode_demo'));


		// add_filter('page_template',array($this, 'printDemo'));

	}
	
	/*function printDemo($single_template) {
		global $post;

		$page_id = get_option('bcwc_demoId');
		if ($post->ID == $page_id) {
			$single_template = dirname( __FILE__ ) . '/mapDemo.php';
		}

		return $single_template;
	}*/
	

	/* SERVICES ************************************************************/
	function loadAdminServices(){
		if(!$this->servicesAdmin_loaded){
			if(wp_script_is('bcwc_amin_js','registered') && !wp_script_is('bcwc_amin_js','enqueued')) wp_enqueue_script('bcwc_amin_js');
			$this->globalParams();
			$this->servicesAdmin_loaded = true;//Para no cargar parametros dos veces
		}
	}
	function loadServices($servicios){
		if(!$this->services_loaded){
			if(wp_style_is('bcwc_css','registered') && !wp_style_is('bcwc_css','enqueued')) wp_enqueue_style('bcwc_css');
			if(wp_script_is('bcwc_js','registered') && !wp_script_is('bcwc_js','enqueued')) wp_enqueue_script('bcwc_js');
			$this->globalParams($servicios);
			$this->services_loaded = true;//Para no cargar parametros dos veces
		}

	}
	function globalParams($servicios=false){
		$bcwc_use_html5_location = get_option('bcwc_use_html5_location');
		$initP = $this->getIPLocation();
		?>
			<script>
				var bcwc_baseUrl;
				var init_position;
				var BCWC_URL;
				var servicios;
				var html5_location;
				jQuery(document).ready(function(){
					html5_location = <?php echo $bcwc_use_html5_location?"true":"false" ?>;
					bcwc_baseUrl = '<?php echo site_url(); ?>';
					BCWC_URL = '<?php echo BCWC_URL; ?>';
					init_position = <?php echo '{lat:'. $initP['lat'] .',lng:'. $initP['lng'] .'}'; ?>;
					<?php echo $servicios?'servicios = ["'.implode('","',$servicios).'"];' :''; ?>
				});
			</script>
		<?php
	}

	// Function to get the client IP address
	/*function get_client_ip() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}*/

	function get_client_ip() {
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		   $ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}

	function getIPLocation(){
		$url = "http://freegeoip.net/json/".$this->get_client_ip();
		$json = file_get_contents($url);
		$content = json_decode($json, true);
		if($content['latitude'] && $content['longitude'])
			return array('lat'=>$content['latitude'],'lng'=>$content['longitude']);
		else 
			// return array('lat'=>'-33.445','lng'=>'-70.66'); // SANTIAGUITO
			return array('lat'=>'-53.225','lng'=>'71.103'); // pta arenas
	}

	function distancia($latlng1,$latlng2){
		$lat1=$latlng1['lat'];
		$lon1=$latlng1['lng'];
		$lat2=$latlng2['lat'];
		$lon2=$latlng2['lng'];
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		return ($miles * 1.609344);
	}
	
	/* WIDGET ************************************************************/
	function widgets(){
		require_once 'widget.php';
		register_widget('bcwc_Widget');
	}
	/* SHORTCODE ************************************************************/
	/* [bicicultura-weatherconditions aid=103983769749096_22669 alto=400 ancho=600]  */
	function shortcode($atts, $content=null, $code=""){
		return $this->cliente($atts);
	}

	function cliente($servicios){
		$this->loadServices($servicios);
		$result = '<ul>';
		if(in_array('td',$servicios)){
			$result .= '<li>Hoy es <span class="bcwc_today"></span></li>';
		}
		if(in_array('cc',$servicios)){
			global $bcwc_condicionClima;
			$result .= $bcwc_condicionClima->cliente();
		}
		if(in_array('ca',$servicios)){
			global $bcwc_calidadAire;
			$result .= $bcwc_calidadAire->cliente();
		}
		if(in_array('rs',$servicios)){
			global $bcwc_radiacionSolar;
			$result .= $bcwc_radiacionSolar->cliente();
		}
		return $result.'</ul>';
	}

	// [bicicultura-weatherconditions-map height=100% width=100%]
	function shortcode_demo($atts, $content=null, $code=""){
		global $bcwc_demo;
		return $bcwc_demo->printMap(isset($atts['height'])? $atts['height']:null,isset($atts['width'])? $atts['width']:null);
	}

	/* ADMINISTRACIÓN ***********************************************************/
	function menu(){
		add_menu_page('Bicicultura Weather Conditions', 'Weather Conditions', 'administrator', __FILE__, array($this,'options'),plugins_url('/img/icon_20.png', __FILE__));
		add_action('admin_init', array($this,'register_settings'));
	}
	function register_settings(){
		register_setting('bcwc_setting','bcwc_use_html5_location');
		// register_setting('bcwc_setting', 'bcwc_demoId');
	}
	function options(){
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		require_once 'opciones.php';
		$page = new AdminPage();
	}
}

global $bcwc_plugin;
$bcwc_plugin = new bcWeatherConditions();
add_action('init', array($bcwc_plugin,'init'),0); //Prioridad 0 para iniciar widget
register_activation_hook( __FILE__, array($bcwc_plugin,'install'));
register_deactivation_hook( __FILE__,array($bcwc_plugin,'uninstall'));

?>