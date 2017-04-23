function bcwc_updateStations(){
	jQuery('#update_message').html('Actualizando...');
	jQuery.ajax({
		type: 'POST',
		url: bcwc_baseUrl + '/wp-admin/admin-ajax.php',
		data: {
			action: 'bcwc_ca_updateStations'
		},
		success: function(data, textStatus, XMLHttpRequest){
			if(data==1) jQuery('#update_message').html('Se ha actualizado con éxito');
			else jQuery('#update_message').html('Error al actualizar el listado');
		},
		error: function(MLHttpRequest, textStatus, errorThrown){},
		complete: function(jqXHR,textStatus){}
	});
}