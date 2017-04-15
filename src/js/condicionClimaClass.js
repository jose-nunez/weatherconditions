
var condicionClimaClass = function(){
	var self = this;

	self.getState = function(latlng_position){
		
		self.printState();

		jQuery.ajax({
			type: 'POST',
			url: bcwc_baseUrl + '/wp-admin/admin-ajax.php',
			data: {
				action: 'bcwc_cc_getState',
				latlng: latlng_position
			},
			success: function(data, textStatus, XMLHttpRequest){
				self.printState(JSON.parse(data));
			},
			error: function(MLHttpRequest, textStatus, errorThrown){},
			complete: function(jqXHR,textStatus){}
		});
	}

	self.printState = function(estado){
		if(estado){
			var icon_url = 'http://openweathermap.org/img/w/';
			// jQuery('.bcwc_cc_comuna').html('En <strong>'+estado.comuna+'</strong>');
			jQuery('.bcwc_cc_temp').html('<strong>'+estado.temp+'°</strong> ');
			jQuery('.bcwc_cc_icono').css('display','');
			jQuery('.bcwc_cc_icono').attr('src',icon_url+estado.icono+'.png');
			jQuery('.bcwc_cc_descripcion').html(estado.descripcion);
			jQuery('.bcwc_cc_tmax').html('Máxima: '+estado.tmax+'° | ');
			jQuery('.bcwc_cc_tmin').html('Mínima: '+estado.tmin+'° | ');
			jQuery('.bcwc_cc_humedad').html('Humedad: '+estado.humedad+'%');
		}
		else{
			// jQuery('.bcwc_cc_comuna').html('En ');
			jQuery('.bcwc_cc_icono').css('display','none');
			jQuery('.bcwc_cc_temp').html('');
			jQuery('.bcwc_cc_descripcion').html('');
			jQuery('.bcwc_cc_humedad').html('');
			jQuery('.bcwc_cc_tmax').html('');
			jQuery('.bcwc_cc_tmin').html('');
		}
	}
}
