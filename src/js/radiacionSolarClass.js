
var radiacionSolarClass = function(){
	var self = this;

	self.getNearestStation = function(latlng_position){
		jQuery.ajax({
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
		});
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

		jQuery.ajax({
			type: 'POST',
			url: bcwc_baseUrl + '/wp-admin/admin-ajax.php',
			data: {
				action: 'bcwc_rs_getState',
				latlng: latlng_position
			},
			success: function(data, textStatus, XMLHttpRequest){
				// self.printState(JSON.parse(data));
				/*var */result = JSON.parse(data);
				eval('result.estado = '+result.estado+';');
				self.printState(result);
			},
			error: function(MLHttpRequest, textStatus, errorThrown){},
			complete: function(jqXHR,textStatus){}
		});
	}

	self.printState = function(result){
		if(result){
			jQuery('.bcwc_rs_comuna').html('Radiación solar en <strong>'+result.clave+'</strong>');
			jQuery('.bcwc_rs_obs').html('Observada: <strong><span class="indice_uv '+self.getClassIndice(result.estado.indice_obs)+'">'+result.estado.indice_obs.split(':')[1]+'</span></strong> | ');
			jQuery('.bcwc_rs_obs_fecha').html(result.estado.fecha_obs+', '+result.estado.hora_obs);
			jQuery('.bcwc_rs_pro').html('Pronosticada: <strong><span class="indice_uv '+self.getClassIndice(result.estado.indice_pro)+'">'+result.estado.indice_pro.split(':')[1]+'</span></strong> | ');
			jQuery('.bcwc_rs_pro_fecha').html(result.estado.fecha_pro);
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
		else if(indice.indexOf('1')!=-1 || indice.indexOf('2')!=-1) return 'bajo';
		else if(indice.indexOf('3')!=-1 || indice.indexOf('4')!=-1 || indice.indexOf('5')!=-1) return 'moderado';
		else if(indice.indexOf('6')!=-1 || indice.indexOf('7')!=-1) return 'alto';
		else if(indice.indexOf('8')!=-1 || indice.indexOf('9')!=-1 || indice.indexOf('10')!=-1) return 'muyalto';
		else return '';
	}
}