var marker;
var greenIcon = L.icon({
	iconUrl: leafletlayers_img_path+'marker-icon.png',
	iconSize:    [25, 41],
	iconAnchor:  [12, 41],
	popupAnchor: [1, -34],
	shadowSize:  [41, 41]
});
var editing=false;
var add_marker_button=document.getElementById('leafletlayers-show-form');
var add_marker_div=document.getElementById('leafletlayers-form-div');
var add_marker_message=document.getElementById('leafletlayers-form-message');
var add_marker_form=document.getElementById('leafletlayers-form');
var leafmap = document.getElementById('leafmap');
var sending_message = document.getElementById('leafletlayers-sending');
var add_form_submit=document.getElementById("leafletlayers-form-submit");
add_marker_button.addEventListener('click', add_marker_button_status);

function add_marker_button_status(e)
{
	if(check_state(add_marker_div,'leafletlayers-form-div-adding'))
	{
		remove_class(add_marker_div,'leafletlayers-form-div-adding');
		remove_class(leafmap,'leaflet-container-adding');
		add_marker_button.innerHTML ='Ajouter un POI <i class="material-icons right">location_on</i>';
		reset_collab_form();
	}
	else
	{
		add_marker_button.innerHTML ='Annuler <i class="material-icons right">cancel</i>';
		init_collab_form();
		add_class(leafmap,'leaflet-container-adding');
		add_class(add_marker_div,'leafletlayers-form-div-adding');
	}

}
function check_state(el,LFclassName) {
	if (el.classList)
		return el.classList.contains(LFclassName);
	else
		return new RegExp('(^| )'+LFclassName+'( |$)', 'gi').test(el.className);
	}
	
function add_class(el,LFclassName) {
	if (el.classList)
	  el.classList.add(LFclassName);
	else
	  el.className += ' ' + LFclassName;
	
	}
function remove_class(el,LFclassName) {
	if (el.classList)
	  el.classList.remove(LFclassName);
	else
	  el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
	
	}
	
function reset_collab_form()
{
	map.off('click', clicknadd_marker);
	if(marker)
	{
		map.removeLayer(marker);
		marker=null;
		document.getElementById('mlat').value ='';
	    document.getElementById('mlng').value ='';
	}
	add_form_submit.removeEventListener('click',collab_form_submit);
	add_marker_form.reset();
}
function init_collab_form()
{
	map.on('click', clicknadd_marker);
	add_form_submit.addEventListener('click',collab_form_submit);
	document.getElementById('mlat').value ='';
	document.getElementById('mlng').value ='';
	remove_class(add_marker_message,'success');
	remove_class(add_marker_message,'error');
}

function clicknadd_marker(e) {

   if(map.getZoom() > 16)
   {
	   if(!marker)
	   { 
		marker = L.marker(e.latlng,{icon: greenIcon});
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
}

function collab_form_submit(e)
{
	e.preventDefault();
	remove_class(add_marker_message,'success');
	remove_class(add_marker_message,'error');
	sending_message.style.display='block';
	add_form_submit.disabled=true;
	add_form_submit.style.display='none';
	try
   {
     xhr = new XMLHttpRequest(); 
   } catch(e)
   { 
     try { xhr = new ActiveXObject("Msxml2.XMLHTTP"); } 
     catch (e2)
    { 
       try { xhr = new ActiveXObject("Microsoft.XMLHTTP"); } 
       catch (e) {'Sorry : your browser doesn\'t support this feature'}
    }
  }
	xhr.onreadystatechange = function() {		
		if(xhr.readyState == 4 && xhr.status == 200)
		{
			if(xhr.responseText=='added')
			{
				add_marker_message.innerHTML=leafletlayers_success_txt;
				add_class(add_marker_message,'success');
				sending_message.style.display='none';
				add_form_submit.disabled=false;
				add_form_submit.style.display='block';
				add_marker_button.click();	
			}
			else
			{
				add_marker_message.innerHTML=leafletlayers_error_txt+' ('+xhr.responseText+')';
				add_class(add_marker_message,'error');
				sending_message.style.display='none';
				add_form_submit.disabled=false;
				add_form_submit.style.display='block';	
			}
		}
		else if(xhr.readyState == 4)
		{
			add_marker_message.innerHTML=leafletlayers_error_txt+' ('+xhr.status+')';
			add_class(add_marker_message,'error');
			sending_message.style.display='none';
			add_form_submit.disabled=false;
			add_form_submit.style.display='block';
		}
	};
	xhr.open("post", ajax_url, true);
    xhr.send(new FormData(add_marker_form));

}