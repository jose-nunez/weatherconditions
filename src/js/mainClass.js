
var bcwc_mainClass = function(){
	var self = this;
	self.timeout = 5000;
	self.isOnGeolocation = true;

	self.todayHTML = function(){
		var today = new Date();
		var dias=['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sábado'];
		var meses=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
		var hoy = dias[today.getDay()] +' '+ today.getDate() +' de '+ meses[today.getMonth()] +' de '+ today.getFullYear();
		jQuery('.bcwc_today').html(hoy);
	}

	self.timeConverter =function(UNIX_timestamp){
		var a = new Date(UNIX_timestamp*1000);
		var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
		var year = a.getFullYear();
		var month = months[a.getMonth()];
		var date = a.getDate();
		var hour = a.getHours();
		var min = a.getMinutes();
		var sec = a.getSeconds();
		var time = date + ',' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec ;
		return time;
	}

	self.setLocation = function(callback,params){
		setTimeout(function(){
			if(self.isOnGeolocation){
				self.isOnGeolocation = false;
				callback(params);
			}
		},self.timeout);

		if(navigator.geolocation){
			navigator.geolocation.getCurrentPosition(function(position){
				if(self.isOnGeolocation){
					self.isOnGeolocation = false;
					init_position = {lat:position.coords.latitude,lng:position.coords.longitude};
					callback(params);
				}
			});
		}
		else if(self.isOnGeolocation){
			self.isOnGeolocation = false;
			callback(params);
		}
	}
	self.callServices = function(functions){
		for(var i=0;i<functions.length;i++){
			(functions[i])(init_position);
		}
	}

	self.init = function(){
		self.todayHTML();
		
		var functions = [];
		if(jQuery.inArray('cc',servicios)!=-1){
			var condicionClima = new condicionClimaClass();
			functions.push(condicionClima.getState);
		}
		if(jQuery.inArray('ca',servicios)!=-1){
			var calidadAire = new calidadAireClass();
			functions.push(calidadAire.getState);
		}
		if(jQuery.inArray('rs',servicios)!=-1){
			var radiacionSolar = new radiacionSolarClass();
			functions.push(radiacionSolar.getState);
		}
		
		if(html5_location) self.setLocation(self.callServices,functions);
		else self.callServices(functions);
	}
}

var bcwc_main;

jQuery(document).ready(function(){
	bcwc_main = new bcwc_mainClass();
	bcwc_main.init();
});
/*
		var cuchufleta;
		cuchufleta={lat:-18.091033487001262,lng:-70.30426025390625}; // arica
		cuchufleta= {lat:-33.530520095021416,lng:-70.55797576904297}; // lf
		cuchufleta= {lat:-33.607185251256205,lng:-70.5758285522461}; // pte alto
		cuchufleta= {lat:-23.40276490540795,lng:-69.169921875}; // antofa
		cuchufleta= {lat:-53.2257684357902,lng:-71.103515625}; // pta arenas
		cuchufleta= {-33.34314878808268,lng:-70.78628540039062}; // quilicura
*/

var deg2rad = function(degrees){return degrees * Math.PI / 180;};
var rad2deg = function(radians){return radians * 180 / Math.PI;};
function distancia(latlng1,latlng2){
	var lat1 = latlng1['lat'];
	var lon1 = latlng1['lng'];
	var lat2 = latlng2['lat'];
	var lon2 = latlng2['lng'];
	var theta = lon1 - lon2;
	var dist = Math.sin(deg2rad(lat1)) * Math.sin(deg2rad(lat2)) + Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.cos(deg2rad(theta));
	dist = Math.acos(dist);
	dist = rad2deg(dist);
	miles = dist * 60 * 1.1515;
	return (miles * 1.609344);
}
