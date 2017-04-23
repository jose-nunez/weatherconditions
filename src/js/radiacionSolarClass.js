
var radiacionSolarClass = function(){
	var self = this;
	self.estaciones = {
		'180016': {clave:'180016','nombre':'Arica','latlng':{'lat':-18.3,'lng':-70.316}}
		,'200006': {clave:'200006','nombre':'Iquique','latlng':{'lat':-20.53,'lng':-70.183}}
		,'220008': {clave:'220008','nombre':'San Pedro De Atacama','latlng':{'lat':-22.917,'lng':-68.2}}
		,'230001': {clave:'230001','nombre':'Antofagasta','latlng':{'lat':-23.45,'lng':-70.4333}}
		,'270008': {clave:'270008','nombre':'Caldera','latlng':{'lat':-27.26,'lng':-70.76}}
		,'290004': {clave:'290004','nombre':'La Serena','latlng':{'lat':-29.902716416045926,'lng':-71.25080108642578}}
		,'270001': {clave:'270001','nombre':'Isla De Pascua','latlng':{'lat':-27.1606,'lng':-109.427}}
		,'330120': {clave:'330120','nombre':'Litoral Central','latlng':{'lat':-33.0208,'lng':-71.6425}}
		,'330077': {clave:'330077','nombre':'Cordillera Region Metropolitana','latlng':{'lat':-33.44290131937933,'lng':-70.1422119140625}}
		,'330020': {clave:'330020','nombre':'Santiago','latlng':{'lat':-33.445,'lng':-70.683}}
		,'340045': {clave:'340045','nombre':'Rancagua','latlng':{'lat':-34.1,'lng':-70.46}}
		,'350050': {clave:'350050','nombre':'Talca (universidad Autonoma)','latlng':{'lat':-35.4197,'lng':-71.6694}}
		,'360042': {clave:'360042','nombre':'Termas De Chillan','latlng':{'lat':-36.9036,'lng':-71.4103}}
		,'360019': {clave:'360019','nombre':'Concepcion','latlng':{'lat':-36.82646252256008,'lng':-73.04964065551758}}
		,'380028': {clave:'380028','nombre':'Temuco (universidad Catolica De Temuco)','latlng':{'lat':-38.7,'lng':-72.54}}
		,'390026': {clave:'390026','nombre':'Valdivia','latlng':{'lat':-39.8141,'lng':-73.248}}
		,'410005': {clave:'410005','nombre':'Puerto Montt','latlng':{'lat':-41.46537017728067,'lng':-72.93823242187499}}
		,'450004': {clave:'450004','nombre':'Coihaique','latlng':{'lat':-45.583,'lng':-72.117}}
		,'520006': {clave:'520006','nombre':'Punta Arenas','latlng':{'lat':-53.0009,'lng':-70.85}}
		,'950001': {clave:'950001','nombre':'Antartica','latlng':{'lat':-62.417,'lng':-58.883}}
	};

	self.getNearestStation = function(latlng_position){
		var dist=99999999,newdist,estacion;
		for(var i in self.estaciones){
			newdist = distancia(latlng_position,self.estaciones[i].latlng);
			if(dist>newdist){
				dist=newdist;
				estacion = self.estaciones[i];
			}
		}

		// console.log(estacion);
		return estacion;

		/*jQuery.ajax({
			type: 'POST',
			url: bcwc_baseUrl + '/wp-admin/admin-ajax.php',
			data: {
				action: 'bcwc_rs_getNearestStation',
				latlng: latlng_position
			},
			success: function(data, textStatus, XMLHttpRequest){
				console.log(data);
			},
			error: function(MLHttpRequest, textStatus, errorThrown){},
			complete: function(jqXHR,textStatus){}
		});*/
	}

	self.getState = function(latlng_position){
		/*
		lf {lat:-33.530520095021416,lng:-70.55797576904297}
		pte alto {lat:-33.607185251256205,lng:-70.5758285522461}
		antofa {lat:-23.40276490540795,lng:-69.169921875}
		pta arenas {lat:-53.2257684357902,lng:-71.103515625}
		quilicura {-33.34314878808268,lng:-70.78628540039062}
			*/

		self.printState();
		
		
		// CUCHUFLETA
		var estacion = self.getNearestStation(latlng_position);
		for(var i in RadiacionUVB){
			if(RadiacionUVB[i].indice == estacion.clave){
				self.printState(RadiacionUVB[i]);
				return;
			}
		}



		/*jQuery.ajax({
			type: 'POST',
			url: bcwc_baseUrl + '/wp-admin/admin-ajax.php',
			data: {
				action: 'bcwc_rs_getState',
				latlng: latlng_position
			},
			success: function(data, textStatus, XMLHttpRequest){
				result = JSON.parse(data);
				eval('result.estado = '+result.estado+';');
				self.printState(result);
			},
			error: function(MLHttpRequest, textStatus, errorThrown){},
			complete: function(jqXHR,textStatus){}
		});*/
	}

	self.printState = function(estacion){
		if(estacion){
			// jQuery('.bcwc_rs_comuna').html('Radiación solar en <strong>'+estacion.nombre+'</strong>');
			jQuery('.bcwc_rs_comuna').html('Radiación solar');
			jQuery('.bcwc_rs_obs').html('<br/>Observada: <strong><span title="'+estacion.nombre+'" class="indice_uv '+self.getClassIndice(estacion.indiceobs.split(':')[0])+'">'+estacion.indiceobs.split(':')[1]+'</span></strong> | ');
			jQuery('.bcwc_rs_obs_fecha').html(estacion.fechaobs+', '+estacion.horaobs);
			jQuery('.bcwc_rs_pro').html('<br/>Pronosticada: <strong><span class="indice_uv '+self.getClassIndice(estacion.indicepron.split(':')[0])+'">'+estacion.indicepron.split(':')[1]+'</span></strong> | ');
			jQuery('.bcwc_rs_pro_fecha').html(estacion.fechapron);
			jQuery('.bcwc_rs_loading').css('display','none');
		}
		else{
			jQuery('.bcwc_rs_comuna').html('Radiación solar en ');
			jQuery('.bcwc_rs_obs').html('');
			jQuery('.bcwc_rs_obs_fecha').html('');
			jQuery('.bcwc_rs_pro').html('');
			jQuery('.bcwc_rs_pro_fecha').html('');
			jQuery('.bcwc_rs_loading').css('display','');
		}


	}
	self.getClassIndice = function(indice){
		if(indice.indexOf('11')!=-1) return 'extremo';
		else if(indice.indexOf('8')!=-1 || indice.indexOf('9')!=-1 || indice.indexOf('10')!=-1) return 'muyalto';
		else if(indice.indexOf('6')!=-1 || indice.indexOf('7')!=-1) return 'alto';
		else if(indice.indexOf('3')!=-1 || indice.indexOf('4')!=-1 || indice.indexOf('5')!=-1) return 'moderado';
		else if(indice.indexOf('1')!=-1 || indice.indexOf('2')!=-1) return 'bajo';
		else return '';
	}



}