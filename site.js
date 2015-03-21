function initialize(){
	
	var vibyCoord = new google.maps.LatLng(59.060328, 14.871228);
	var betaniaCoord = new google.maps.LatLng(59.124188,15.081001);
	
	var mapProp = {
	  center: new google.maps.LatLng(59.095964, 14.960823),
	  zoom: 10,
	  mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	
	var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
	
	var vibyMarker = new google.maps.Marker({
		position: vibyCoord,
		map: map,
		title: 'Viby Kyrka'
	});
	
	var betaniaMarker = new google.maps.Marker({
		position: betaniaCoord,
		map: map,
		title: 'Betaniakyrkan'
	});
	
	var vibyInfoContent = '<div id="content">'+
      '<div id="siteNotice">'+
      '</div>'+
      '<h3>Viby kyrka</h3>'+
      '<div id="bodyContent">'+
      '<p>Här kommer vi vigas.</p>'+
      '</div>'+
      '</div>';
	
	var betaniaInfoContent = '<div id="content">'+
      '<div id="siteNotice">'+
      '</div>'+
      '<h3>Betaniakyrkan</h3>'+
      '<div id="bodyContent">'+
      '<p>Du som fått inbjudan är välkommen hit på bröllopsfest efter vigseln.</p>'+
      '</div>'+
      '</div>';
	
	var vibyInfo = new google.maps.InfoWindow({
		content: vibyInfoContent
	});

	var betaniaInfo = new google.maps.InfoWindow({
		content: betaniaInfoContent
	});
	
	google.maps.event.addListener(vibyMarker, 'click', function(){
		vibyInfo.open(map, vibyMarker);
	});

	google.maps.event.addListener(betaniaMarker, 'click', function(){
		betaniaInfo.open(map, betaniaMarker);
	});
}

google.maps.event.addDomListener(window, 'load', initialize);