<?php 

class bcwc_radiacionSolarServices{

	function initServices(){
		add_action('wp_ajax_nopriv_bcwc_rs_getNearestStation', array($this,'getNearestStationService'));
		add_action('wp_ajax_bcwc_rs_getNearestStation', array($this,'getNearestStationService'));

		add_action('wp_ajax_nopriv_bcwc_rs_getState', array($this,'getStateService'));
		add_action('wp_ajax_bcwc_rs_getState', array($this,'getStateService'));
	}

	function cliente(){
		return '
			<li>
				<span class="bcwc_rs_comuna">Radiación solar en </span>
				<img class="bcwc_icon bcwc_rs_loading" src="'. BCWC_URL .'img/loading.gif" />
				<a href="http://www.meteochile.gob.cl/radiacion_uv.php" target="_blank"><img class="bcwc_icon" src="'. BCWC_URL .'img/hyperlink.png" title="Dirección Meteorológica de Chile" /></a>
				<span class="bcwc_rs_obs"></span><span class="bcwc_rs_obs_fecha"></span><br/>
				<span class="bcwc_rs_pro"></span><span class="bcwc_rs_pro_fecha"></span>
			</li>
		';
	}

	function loadStations(){
		$estaciones = array(
			'ARICA' => 										array('clave'=>'ARICA','url'=>'http://www.meteochile.gob.cl/js/uv_scar.js','latlng'=>array('lat'=>-18.3,'lng'=>-70.316))
			,'IQUIQUE' =>									array('clave'=>'IQUIQUE','url'=>'http://www.meteochile.gob.cl/js/uv_scda.js','latlng'=>array('lat'=>-20.53,'lng'=>-70.183))
			,'SAN PEDRO DE ATACAMA' =>						array('clave'=>'SAN PEDRO DE ATACAMA','url'=>'http://www.meteochile.gob.cl/js/uv_sanpedro.js','latlng'=>array('lat'=>-22.917,'lng'=>-68.2))
			,'ANTOFAGASTA' => 								array('clave'=>'ANTOFAGASTA','url'=>'http://www.meteochile.gob.cl/js/uv_scfa.js','latlng'=>array('lat'=>-23.45,'lng'=>-70.4333))
			,'CALDERA' =>									array('clave'=>'CALDERA','url'=>'http://www.meteochile.gob.cl/js/uv_sccl.js','latlng'=>array('lat'=>-27.26,'lng'=>-70.76))
			,'LA SERENA' =>									array('clave'=>'LA SERENA','url'=>'http://www.meteochile.gob.cl/js/uv_scse.js','latlng'=>array('lat'=>-29.902716416045926,'lng'=>-71.25080108642578))
			,'ISLA DE PASCUA' =>							array('clave'=>'ISLA DE PASCUA','url'=>'http://www.meteochile.gob.cl/js/uv_scip.js','latlng'=>array('lat'=>-27.1606,'lng'=>-109.427))
			,'LITORAL CENTRAL' =>							array('clave'=>'LITORAL CENTRAL','url'=>'http://www.meteochile.gob.cl/js/uv_scvm.js','latlng'=>array('lat'=>-33.0208,'lng'=>-71.6425))
			,'CORDILLERA REGION METROPOLITANA' =>			array('clave'=>'CORDILLERA REGION METROPOLITANA','url'=>'http://www.meteochile.gob.cl/js/uv_valle.js','latlng'=>array('lat'=>-33.44290131937933,'lng'=>-70.1422119140625))
			,'SANTIAGO' => 									array('clave'=>'SANTIAGO','url'=>'http://www.meteochile.gob.cl/js/uv_scel.js','latlng'=>array('lat'=>-33.445,'lng'=>-70.683))
			,'RANCAGUA' => 									array('clave'=>'RANCAGUA','url'=>'http://www.meteochile.gob.cl/js/uv_rancagua.js','latlng'=>array('lat'=>-34.1,'lng'=>-70.46))
			,'TALCA (UNIVERSIDAD AUTONOMA)' =>				array('clave'=>'TALCA (UNIVERSIDAD AUTONOMA)','url'=>'http://www.meteochile.gob.cl/js/uv_talca.js','latlng'=>array('lat'=>-35.4197,'lng'=>-71.6694))
			,'TERMAS DE CHILLAN' =>							array('clave'=>'TERMAS DE CHILLAN','url'=>'http://www.meteochile.gob.cl/js/uv_termas.js','latlng'=>array('lat'=>-36.9036,'lng'=>-71.4103))
			,'CONCEPCION' =>								array('clave'=>'CONCEPCION','url'=>'http://www.meteochile.gob.cl/js/uv_scie.js','latlng'=>array('lat'=>-36.82646252256008,'lng'=>-73.04964065551758))
			,'TEMUCO (UNIVERSIDAD CATOLICA DE TEMUCO)' =>	array('clave'=>'TEMUCO (UNIVERSIDAD CATOLICA DE TEMUCO)','url'=>'http://www.meteochile.gob.cl/js/uv_sctc.js','latlng'=>array('lat'=>-38.7,'lng'=>-72.54))
			,'VALDIVIA' => 									array('clave'=>'VALDIVIA','url'=>'http://www.meteochile.gob.cl/js/uv_scvd.js','latlng'=>array('lat'=>-39.8141,'lng'=>-73.248))
			,'PUERTO MONTT' =>								array('clave'=>'PUERTO MONTT','url'=>'http://www.meteochile.gob.cl/js/uv_scte.js','latlng'=>array('lat'=>-41.46537017728067,'lng'=>-72.93823242187499))
			,'COIHAIQUE' =>									array('clave'=>'COIHAIQUE','url'=>'http://www.meteochile.gob.cl/js/uv_sccy.js','latlng'=>array('lat'=>-45.583,'lng'=>-72.117))
			,'PUNTA ARENAS' =>								array('clave'=>'PUNTA ARENAS','url'=>'http://www.meteochile.gob.cl/js/uv_scci.js','latlng'=>array('lat'=>-53.0009,'lng'=>-70.85))
			,'ANTARTICA' =>									array('clave'=>'ANTARTICA',		'url'=>'http://www.meteochile.gob.cl/js/uv_scef.js','latlng'=>array('lat'=>-62.417,'lng'=>-58.883))
		);

		add_option('bcwc_rs_stations',$estaciones);
	}

	function printStations(){
		$txt='';
		$estaciones = get_option('bcwc_rs_stations');
		foreach ($estaciones as $key => $station) {
			$txt.= $station['clave'].'<br/>';
		}
		return $txt;
	}

	function getNearestStationService(){
		die($this->getNearestStation($_POST['latlng']));
	}
	function getNearestStation($latlng,$exclude){
		if(!$exclude) $exclude = array();

		$estaciones = get_option('bcwc_rs_stations');
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
		die(json_encode($this->getState($latlng)));
	}

	function getState($latlng,$exclude){
		if(!$exclude) $exclude = array();

		$estacion = $this->getNearestStation($latlng,$exclude);

		if(!$estacion) return false;
		else{
			$state = $this->getState_aux($estacion['clave'],$estacion['url']);
			if(!$state){
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

	function getState_aux($clave,$url){
		if($this->checkURL($url)){
			$result =  file_get_contents($url);
			$result = str_replace("\n","",$result);
			$result = str_replace("['".$clave."']","",$result);
			$result = str_replace(";",",",$result);
			$result = str_replace("=",":",$result);
			$result = substr($result,0,strlen($result)-1);
			return '{'.$result.'}';
		}
		else return false;
	}

	function checkURL($url){
		$file_headers = @get_headers($url);
		if(strpos($file_headers[0],'404')) return false;
		else return true;
	}

}

?>