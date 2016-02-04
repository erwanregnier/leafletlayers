var map = L.map('leafmap').setView([49.89690130311624, 2.305455207824707], 13);
L.tileLayer('http://{s}.tile.openstreetmap.se/hydda/full/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a>'
}).addTo(map);

var overLayers = [
	{
		group: "Ressources",
		layers: []
	}
];

var markers_grouped = JSON.parse(markers_json);
var overlays2 = {};
for (i in markers_grouped) {
	var g_title=markers_grouped[i].title;
	//Create layer
	overlays2[g_title]=  new L.layerGroup();
	var obj2 = overlays2[g_title];
	//Retrieve markers for this layer
	for(x in markers_grouped[i].markers) {
		var m_id=markers_grouped[i].markers[x].id;
		L.marker([markers_grouped[i].markers[x].lat, markers_grouped[i].markers[x].lng]).bindPopup("<b>"+markers_grouped[i].markers[x].title+"</b><br>"+markers_grouped[i].markers[x].desc).addTo(obj2);
	}
	//Add the layer to control list
	var ovls = overLayers[0].layers;
	ovls[i-1] = {active:true, name:g_title, layer:obj2};
	
}
var panelLayers = new L.Control.PanelLayers(null,overLayers);
map.addControl(panelLayers);