var map = L.map('leafmap').setView([49.89690130311624, 2.305455207824707], 14);
L.tileLayer('http://{s}.tile.openstreetmap.se/hydda/full/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a>'
}).addTo(map);
var marker;
if(document.getElementById('mlat').value && document.getElementById('mlng').value)
{
	marker = L.marker(L.latLng(document.getElementById('mlat').value,document.getElementById('mlng').value));
	marker.addTo(map);
}
map.on('click', function(e) {
   if(map.getZoom() > 17)
   {
	   if(!marker)
	   { 
	   	marker = L.marker(e.latlng);
	   	marker.addTo(map);
	   }
	   else
	   {
		 marker.setLatLng(e.latlng);
		 marker.update();  
	   }
	   document.getElementById('mlat').value = e.latlng.lat;
	   document.getElementById('mlng').value = e.latlng.lng;
   }
   else
   {
		alert(zoom_error_txt);   
   }
});