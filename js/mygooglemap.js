

var timeV;
var mapPosition = [
    ['General Booth Blvd', 36.7895102,-76.0011461, 4],
    ['Princess Anne Rd', 36.8151397,-76.1460425, 4],
    ['North Mall Drive', 36.8101266,-76.1089604, 4],
    ['Independence Blvd', 36.8647158,-76.1331874, 4],
    ['Cedar Road', 36.7240514,-76.3006731, 5],
    ['Harbour View Blvd', 36.8756877,-76.4416416, 3],
    ['Granby Street Unit A', 36.8514613,-76.2920737, 2],
    ['Villiage Ave, STE G', 37.1138813,-76.4655919, 1],
    ['Coliseum Drive', 37.0456836,-76.3916907, 1]
];
var contentString = [
    '<div id="content" class="infoWindow"><div class="info-data"><b class="info-title">Virginia Beach</b><br><div> 1485 General Booth Blvd, Ste. 107 <br> Virginia Beach, VA, 23454 <br> TEL. 757-428-5888 </div></div></div><div class="info-buttons"><a href="#" id="direction_link" data-toggle="modal" data-target="#direction_modal" class="btn btn-lg btn-success action directions">Directions</a><a href="" class="action zoomhere">Zoom Here</a><a href="https://order.pxsweb.com/menu/5e430419adb0d5f4367b23cc#ordering-for-prompt" class="action a-website">Order Now</a></div>',
    // '<div id="content" class="infoWindow"><div class="info-data"><b class="info-title">Virginia Beach</b><br><div> 1485 General Booth Blvd, Ste. 107 <br> Virginia Beach, VA, 23454 <br> TEL. 757-428-5888 </div></div></div><div class="info-buttons"><input type="button" id="direction_link" data-toggle="modal" data-target="#direction_modal" class="btn btn-lg btn-success action directions" value="Directions" /><a href="" class="action zoomhere">Zoom Here</a><a href="https://order.teriyakimadness.com/menu/woodlandstx" class="action a-website">Order Now</a></div>',
    '<div id="content" class="infoWindow"><div class="info-data"><b class="info-title">Virginia Beach</b><br><div> 4700 Princess Anne Rd <br> Virginia Beach, VA, 23464 <br> TEL. 757-227-9000 </div></div></div><div class="info-buttons"><a href="#" id="direction_link" data-toggle="modal" data-target="#direction_modal" class="btn btn-lg btn-success action directions">Directions</a><a href="" class="action zoomhere">Zoom Here</a><a href="https://order.pxsweb.com/menu/5e5d339d505ee9692c7b23e5#ordering-for-prompt" class="action a-website">Order Now</a></div>',
    '<div id="content" class="infoWindow"><div class="info-data"><b class="info-title">Virginia Beach</b><br><div> 2704 North Mall Drive, Ste 101 <br> Virginia Beach, VA, 23452 <br> TEL. 757-226-8558 </div></div></div><div class="info-buttons"><a href="#" id="direction_link" data-toggle="modal" data-target="#direction_modal" class="btn btn-lg btn-success action directions">Directions</a><a href="" class="action zoomhere">Zoom Here</a><a href="https://order.pxsweb.com/menu/5e5d33b6902ad53c587b23c7#ordering-for-prompt" class="action a-website">Order Now</a></div>',
    '<div id="content" class="infoWindow"><div class="info-data"><b class="info-title">Virginia Beach</b><br><div> 750 Independence Blvd， #4564 <br> Virginia Beach, VA, 23455 <br> TEL. 757-963-2888 </div></div></div><div class="info-buttons"><a href="#" id="direction_link" data-toggle="modal" data-target="#direction_modal" class="btn btn-lg btn-success action directions">Directions</a><a href="" class="action zoomhere">Zoom Here</a><a href="https://order.pxsweb.com/menu/5e5d33d5adb0d5e3527b23d0#ordering-for-prompt" class="action a-website">Order Now</a></div>',
    '<div id="content" class="infoWindow"><div class="info-data"><b class="info-title">Chesapeake</b><br><div> 1501 Cedar Road, Unit 116 <br> Chesapeake, VA, 23322 <br> TEL. 757-819-7776 </div></div></div><div class="info-buttons"><a href="#" id="direction_link" data-toggle="modal" data-target="#direction_modal" class="btn btn-lg btn-success action directions">Directions</a><a href="" class="action zoomhere">Zoom Here</a><a href="https://order.pxsweb.com/menu/5e5d33ee4f5ee99f057b23d1#ordering-for-prompt" class="action a-website">Order Now</a></div>',
    '<div id="content" class="infoWindow"><div class="info-data"><b class="info-title">Suffolk</b><br><div> 5889 Harbour View Blvd <br> Suffolk, VA 23435 <br> TEL. 757-686-1888 / 757-686-3888 </div></div></div><div class="info-buttons"><a href="#" id="direction_link" data-toggle="modal" data-target="#direction_modal" class="btn btn-lg btn-success action directions">Directions</a><a href="" class="action zoomhere">Zoom Here</a><a href="https://order.pxsweb.com/menu/5e5d3410505ee9ab307b23d0#ordering-for-prompt" class="action a-website">Order Now</a></div>',
    '<div id="content" class="infoWindow"><div class="info-data"><b class="info-title">Norfolk</b><br><div> 401 Granby Street Unit A <br> Norfolk, VA, 23510 <br> TEL. 757-390-2141 / 757-390-2412 </div></div></div><div class="info-buttons"><a href="#" id="direction_link" data-toggle="modal" data-target="#direction_modal" class="btn btn-lg btn-success action directions">Directions</a><a href="" class="action zoomhere">Zoom Here</a><a href="https://order.pxsweb.com/menu/5e5d34364f5ee9f0027b23f6#ordering-for-prompt" class="action a-website">Order Now</a></div>',
    '<div id="content" class="infoWindow"><div class="info-data"><b class="info-title">YorkTown</b><br><div> 209 Villiage Ave, STE G <br> YorkTown, VA, 23693 <br> TEL. 757-234-8899 </div></div></div><div class="info-buttons"><a href="#" id="direction_link" data-toggle="modal" data-target="#direction_modal" class="btn btn-lg btn-success action directions">Directions</a><a href="" class="action zoomhere">Zoom Here</a><a href="https://order.pxsweb.com/menu/5e5d34464f5ee904057b23eb#ordering-for-prompt" class="action a-website">Order Now</a></div>',
    '<div id="content" class="infoWindow"><div class="info-data"><b class="info-title">Hampton</b><br><div> 2040 Coliseum Drive <br> Unite A25 Hampton, VA, 23666 <br> TEL. 757-838-9666 / 757-838-8886 </div></div></div><div class="info-buttons"><a href="#" id="direction_link" data-toggle="modal" data-target="#direction_modal" class="btn btn-lg btn-success action directions">Directions</a><a href="" class="action zoomhere">Zoom Here</a><a href="https://order.pxsweb.com/menu/5e5d345cadb0d569547b23d2#ordering-for-prompt" class="action a-website">Order Now</a></div>'
];
var markPosition;
var markContent;
var map;
var marker;
var infowindow;
var mapProp;


function myMap(value) {


    mapProp= {
        center:new google.maps.LatLng(36.852786,-75.977785),
        zoom:9,
        styles: [
            {elementType: 'geometry', stylers: [{color: '#242f3e'}]},
            {elementType: 'labels.text.stroke', stylers: [{color: '#242f3e'}]},
            {elementType: 'labels.text.fill', stylers: [{color: '#746855'}]},
            {
                featureType: 'administrative.locality',
                elementType: 'labels.text.fill',
                stylers: [{color: '#d59563'}]
            },
            {
                featureType: 'poi',
                elementType: 'labels.text.fill',
                stylers: [{color: '#d59563'}]
            },
            {
                featureType: 'poi.park',
                elementType: 'geometry',
                stylers: [{color: '#263c3f'}]
            },
            {
                featureType: 'poi.park',
                elementType: 'labels.text.fill',
                stylers: [{color: '#6b9a76'}]
            },
            {
                featureType: 'road',
                elementType: 'geometry',
                stylers: [{color: '#38414e'}]
            },
            {
                featureType: 'road',
                elementType: 'geometry.stroke',
                stylers: [{color: '#212a37'}]
            },
            {
                featureType: 'road',
                elementType: 'labels.text.fill',
                stylers: [{color: '#9ca5b3'}]
            },
            {
                featureType: 'road.highway',
                elementType: 'geometry',
                stylers: [{color: '#746855'}]
            },
            {
                featureType: 'road.highway',
                elementType: 'geometry.stroke',
                stylers: [{color: '#1f2835'}]
            },
            {
                featureType: 'road.highway',
                elementType: 'labels.text.fill',
                stylers: [{color: '#f3d19c'}]
            },
            {
                featureType: 'transit',
                elementType: 'geometry',
                stylers: [{color: '#2f3948'}]
            },
            {
                featureType: 'transit.station',
                elementType: 'labels.text.fill',
                stylers: [{color: '#d59563'}]
            },
            {
                featureType: 'water',
                elementType: 'geometry',
                stylers: [{color: '#17263c'}]
            },
            {
                featureType: 'water',
                elementType: 'labels.text.fill',
                stylers: [{color: '#515c6d'}]
            },
            {
                featureType: 'water',
                elementType: 'labels.text.stroke',
                stylers: [{color: '#17263c'}]
            }
        ]
        // panControl: true,
        // streetViewControl: true,
        // mapTypeControl: true,
        // overviewMapControl: true,
        // scaleControl: true
    };

    map = new google.maps.Map(document.getElementById("googleMap"), mapProp);

    infowindow = new google.maps.InfoWindow({
        content: ""
    });


    for (var i = 0; i < mapPosition.length; i++) {
        markPosition = mapPosition[i];
        markContent = contentString[i];
        marker = new google.maps.Marker({
            position: {lat: markPosition[1], lng: markPosition[2]},
            map: map,
            title: 'Click the Zoom',
            animation: google.maps.Animation.DROP,
            info: markContent,
            icon: 'img/icon.png'
        });

        (function (marker, data) {
            google.maps.event.addListener(marker, "click", function (e) {
                window.clearTimeout(timeV);
                // clearTimeout(this);
                timeV = window.setTimeout(function () {
                    map.setZoom(9);
                    map.setCenter(marker.getPosition());
                }, 6000);

                map.setZoom(12);
                map.setCenter(marker.getPosition());

                infowindow.setContent(this.info);
                infowindow.open(map, marker);
            });
        })(marker, markPosition);
    }

    if (value != null){
        value = value - 1;

        markPosition = mapPosition[value];
        markContent = contentString[value];
        marker = new google.maps.Marker({
            position: {lat: markPosition[1], lng: markPosition[2]},
            map: map,
            title: 'Click the Zoom',
            animation: google.maps.Animation.DROP,
            info: markContent,
            icon: 'img/icon.png'
        });

        window.clearTimeout(timeV);
        timeV = window.setTimeout(function () {
            map.setZoom(9);
            map.setCenter(marker.getPosition());
        }, 6000);

        map.setZoom(12);
        map.setCenter(marker.getPosition());

        infowindow.setContent(markContent);
        infowindow.open(map, marker);
    }


}
