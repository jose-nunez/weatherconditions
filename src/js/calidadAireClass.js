
var calidadAireClass = function(){
	var self = this;

	self.getNearestStation = function(){
		jQuery.ajax({
			type: 'POST',
			url: bcwc_baseUrl + '/wp-admin/admin-ajax.php',
			data: {
				action: 'bcwc_ca_getNearestStation',
				latlng: init_position
			},
			success: function(data, textStatus, XMLHttpRequest){
				console.log(data);
			},
			error: function(MLHttpRequest, textStatus, errorThrown){},
			complete: function(jqXHR,textStatus){}
		});
	}

	self.getState = function(){
		
		self.printState();

		jQuery.ajax({
			type: 'POST',
			url: bcwc_baseUrl + '/wp-admin/admin-ajax.php',
			data: {
				action: 'bcwc_ca_getState',
				latlng: init_position
			},
			success: function(data, textStatus, XMLHttpRequest){
				self.printState(JSON.parse(data));
			},
			error: function(MLHttpRequest, textStatus, errorThrown){},
			complete: function(jqXHR,textStatus){}
		});
	}

	self.printState = function(estacion){
		if(estacion){
			var iconos = [];
			var url = 'http://sinca.mma.gob.cl/mapainteractivo/img/';
			iconos.push(['Sin datos',"sindatos.png"]);
			iconos.push(['Bueno',"bueno.png"]);
			iconos.push(['Regular',"regular.png"]);
			iconos.push(['Alerta',"alerta.png"]);
			iconos.push(['Preemergencia',"preemergencia.png"]);
			iconos.push(['Emergencia',"emergencia.png"]);
			iconos.push(['datos',"datos.png"]);
			
			jQuery('.bcwc_ca_loading').css('display','none');
			jQuery('.bcwc_ca_status').html('<strong>'+iconos[estacion.estado][0]+' </strong>');
			jQuery('.bcwc_ca_status').removeClass('bueno regular alerta preemergencia emergencia preliminar nodisponible');
			jQuery('.bcwc_ca_status').addClass(iconos[estacion.estado][0].toLowerCase());
		}
		else{
			jQuery('.bcwc_ca_loading').css('display','');
			jQuery('.bcwc_ca_status').html('');
		}
	}
}
