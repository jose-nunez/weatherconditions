<?php 

class bcwc_calidadAireServices{

	function initServices(){
		add_action('wp_ajax_bcwc_ca_updateStations', array($this,'updateStationsService'));
		
		add_action('wp_ajax_nopriv_bcwc_ca_getNearestStation', array($this,'getNearestStationService'));
		add_action('wp_ajax_bcwc_ca_getNearestStation', array($this,'getNearestStationService'));

		add_action('wp_ajax_nopriv_bcwc_ca_getState', array($this,'getStateService'));
		add_action('wp_ajax_bcwc_ca_getState', array($this,'getStateService'));
	}

	function cliente(){
		return '
			<div class="bcwc_wrapper">
					<h3>
						<span class="bcwc_ca_comuna">Calidad del aire </span>
						<span class="bcwc_ca_status"></span>
						<img class="bcwc_icon bcwc_ca_loading" src="'. BCWC_URL .'img/loading.gif" />
						<a href="http://sinca.mma.gob.cl/" target="_blank">
							<img class="bcwc_icon" src="'. BCWC_URL .'img/hyperlink.png" title="Sistema de Información Nacional de Calidad del Aire"/>
						</a>
					</h3> 
				<img style="display:none;" class="bcwc_icon bcwc_ca_icon" src="" />
			</div>
		';
	}

	function loadStations(){
		add_option('bcwc_ca_stations');
		$this->updateStations();
	}

	function printStations(){
		$txt='';
		$estaciones = get_option('bcwc_ca_stations');
		foreach ($estaciones as $key => $station) {
			$txt.= $station['nombre'].' - '.$station['comuna'].'</br>';
		}
		return $txt;
	}

	function updateStationsService(){
		die($this->updateStations());
	}
	function updateStations(){
		$exito = false;
		if(current_user_can('activate_plugins')){
			$estaciones_bk = get_option('bcwc_ca_stations');
			update_option('bcwc_ca_stations','');
			try{
				$estaciones = array();
				$url = "http://sinca.mma.gob.cl/index.php/json/listado/";
				$json = file_get_contents($url);
				$listado = json_decode($json, true);
				$estaciones = array();
				foreach ($listado as $key => $item) {
					$estacion = array(
						'clave'=>	$item['key'],
						'nombre'=>	$item['nombre'],
						'comuna'=>	$item['comuna'],
						'latlng'=>	array('lat'=>$item['latitud'],'lng'=> $item['longitud'])
					);
					array_push($estaciones,$estacion);
				}
				return update_option('bcwc_ca_stations',$estaciones);
			}
			catch(Exception $e){
				update_option('bcwc_ca_stations',$estaciones_bk);
				return false;
			}
		}
		return false;
	}

	function getNearestStationService(){
		die(json_encode($this->getNearestStation($_POST['latlng'])));
	}
	function getNearestStation($latlng,$exclude){
		if(!$exclude) $exclude = array();

		$estaciones = get_option('bcwc_ca_stations');
		$miEstacion = null;
		$dist = 9999999;
		global $bcwc_plugin;
		foreach ($estaciones as $key => $estacion) {
			$newDist = $bcwc_plugin->distancia($estacion['latlng'],$latlng);
			if($dist > $newDist && !in_array($estacion['clave'],$exclude)){
			 	$dist = $newDist;
				$miEstacion=$estacion;
			}
		}
		return $miEstacion;
	}
	function getStateService(){
		$latlng = $_POST['latlng'];
		$estacion = $this->getState($latlng);
		die(json_encode($estacion));
	}
	
	function getState($latlng,$exclude){
		if(!$exclude) $exclude = array();

		$estacion = $this->getNearestStation($latlng,$exclude);

		if(!$estacion) return false;
		else{
			$state = $this->getState_aux($estacion['clave']);
			if($state==0){
				array_push($exclude,$estacion['clave']);
				return $this->getState($latlng,$exclude);
			}
			else{ 
				$estacion['estado'] = $state;
				// $estacion['excludes'] = $exclude;
				return $estacion;
			}
		}
	}
	function getState_aux($clave){
		$selectedPar="PM25";//Material particulado respirable  fino (MP 2,5)
		/*
		$selectedPar="FULL";//TODO
		$selectedPar="PM10";//Material particulado respirable (MP  10)
		$selectedPar="0008";//Ozono (O3)
		$selectedPar="0001";//Dióxido de azufre (SO2)
		$selectedPar="0003";//Dióxido de nitrógeno (NO2 )
		$selectedPar="0004";//Monóxido de carbono (CO)
		*/
		$url = 'http://sinca.mma.gob.cl/index.php/json/estacion/key/'.$clave.'/par/'.$selectedPar;
		return intval(file_get_contents($url));
	}
}

?>