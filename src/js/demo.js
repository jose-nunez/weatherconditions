console.log('holitaa');
// var mymap = L.map('bcwc-map').setView({lat: -21.983801417384697, lng: -69.19189453125001}, 6);
var mymap = L.map('bcwc-map').setView({lat: -36.24427318493909, lng: -71.19140625000001},4);
L.tileLayer.provider('OpenMapSurfer.Roads').addTo(mymap);
var myBound;
jQuery.ajax({
	type: 'GET',
	url: '/wp-admin/admin-ajax.php',
	data: {
		action: 'bcwc_demo_getChileBounds'
	},
	success: function(data, textStatus, XMLHttpRequest){
		// console.log('Esto llego:',JSON.parse(data));
		myBound = L.geoJSON(JSON.parse(data),{clickable:false}).addTo(mymap);
		myBound.addEventListener('click',function(e){
			// console.log(e.latlng)
			if(typeof bcwc_main != 'undefined' && bcwc_main!=null){
				console.log('Si hay');
				init_position = {lat:e.latlng.lat,lng:e.latlng.lng};
				bcwc_main.init();
			}
			else console.log('No hay bcwc_main');
		});
	},
	error: function(MLHttpRequest, textStatus, errorThrown){},
	complete: function(jqXHR,textStatus){}
});