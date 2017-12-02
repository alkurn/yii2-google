<?php


namespace alkurn\google;

use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\ArrayHelper;
use Yii;

class MapMarkerClustering extends Widget
{
    const API_URL = '//maps.googleapis.com/maps/api/js?';
    public $language = 'en-US';
    public $callback = 'initMap';
    public $sensor = true;
    public $apiKey = 'AIzaSyDQ0l9MIiNQIdB__VDKCzEqkEz2Wcoqq0A';

    public $latitude = '';
    public $longitude = '';
    public $title = '';
    public $options = [];
    public $items = [];

    /**
     * Renders the widget.
     */
    public function run()
    {
        $height = isset($this->options['height']) ? $this->options['height'] : '500px';
        $width = isset($this->options['width']) ? $this->options['width'] : '100%';

        echo Html::beginTag('div', ['id' => (empty($this->options['id']) ? $this->getId() : $this->options['id']), 'class' => $this->options['class'], 'height' => $height, 'width' => $width, 'style' => 'height:' . $height . ';width:' . $width . ';']);
        echo Html::endTag('div');
        $this->registerClientScript();
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {

        $latitude = $this->latitude;
        $longitude = $this->longitude;
        $title = $this->title;
        $mapId = (empty($this->options['id']) ? $this->getId() : $this->options['id']);
        $view = $this->getView();
        $items = [];

        foreach ($this->items as $item) {
            $items[] = ['id' => $item['id'], 'name' => $item['name'], 'address' => $item['address'], 'logo' => $item['logo'], 'latitude' => $item['latitude'], 'longitude' => $item['longitude'], 'status' => $item['status']];
        }

        $data = json_encode($items);
        if (Yii::$app->Map->apiKey) {
            $this->apiKey = Yii::$app->Map->apiKey;
        }

        $view->registerJsFile(self::API_URL . http_build_query([
                'key' => $this->apiKey,
                'language' => $this->language,
            ]));

        $js = <<<JS

       initMap();

       function initMap() {
        var locations = $data; 
        //var imageUrl = 'http://chart.apis.google.com/chart?cht=mm&chs=24x32&' + 'chco=FFFFFF,008CFF,000000&ext=.png'; 
        var imageUrl = 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'; 
        var styleNight = [
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
          ];
        
        var styles = [ { "elementType": "geometry", "stylers": [ { "color": "#212121" } ] }, { "elementType": "labels.icon", "stylers": [ { "visibility": "off" } ] }, { "elementType": "labels.text.fill", "stylers": [ { "color": "#757575" } ] }, { "elementType": "labels.text.stroke", "stylers": [ { "color": "#212121" } ] }, { "featureType": "administrative", "elementType": "geometry", "stylers": [ { "color": "#757575" } ] }, { "featureType": "administrative.country", "elementType": "labels.text.fill", "stylers": [ { "color": "#9e9e9e" } ] }, { "featureType": "administrative.land_parcel", "stylers": [ { "visibility": "off" } ] }, { "featureType": "administrative.locality", "elementType": "labels.text.fill", "stylers": [ { "color": "#bdbdbd" } ] }, { "featureType": "poi", "elementType": "labels.text.fill", "stylers": [ { "color": "#757575" } ] }, { "featureType": "poi.park", "elementType": "geometry", "stylers": [ { "color": "#181818" } ] }, { "featureType": "poi.park", "elementType": "labels.text.fill", "stylers": [ { "color": "#616161" } ] }, { "featureType": "poi.park", "elementType": "labels.text.stroke", "stylers": [ { "color": "#1b1b1b" } ] }, { "featureType": "road", "elementType": "geometry.fill", "stylers": [ { "color": "#2c2c2c" } ] }, { "featureType": "road", "elementType": "labels.text.fill", "stylers": [ { "color": "#8a8a8a" } ] }, { "featureType": "road.arterial", "elementType": "geometry", "stylers": [ { "color": "#373737" } ] }, { "featureType": "road.highway", "elementType": "geometry", "stylers": [ { "color": "#3c3c3c" } ] }, { "featureType": "road.highway.controlled_access", "elementType": "geometry", "stylers": [ { "color": "#4e4e4e" } ] }, { "featureType": "road.local", "elementType": "labels.text.fill", "stylers": [ { "color": "#616161" } ] }, { "featureType": "transit", "elementType": "labels.text.fill", "stylers": [ { "color": "#757575" } ] }, { "featureType": "water", "elementType": "geometry", "stylers": [ { "color": "#000000" } ] }, { "featureType": "water", "elementType": "labels.text.fill", "stylers": [ { "color": "#3d3d3d" } ] } ];
        
        var map = new google.maps.Map(document.getElementById("{$mapId}"), {zoom: 3, center: {lat: 55.8508774, lng: -4.231964}, styles:styles});
        
        // Add some markers to the map.
        // Note: The code uses the JavaScript Array.prototype.map() method to
        // create an array of markers based on a given "locations" array.
        // The map() method here has nothing to do with the Google Maps API.
        var markers = locations.map(function(location, i) {   
            
            var icon = 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png';
            var statusTitle = '';
            
            switch (location.status){
                case '1':
                icon = 'http://maps.google.com/mapfiles/ms/icons/red-dot.png';
                statusTitle = 'Response Status: InActive';
                break;
                case '2':
                icon = 'http://maps.google.com/mapfiles/ms/icons/green-dot.png';
                statusTitle = 'Response Status: Active';
                break
                default:
            }
            
            var contentString = '<div class="content">'+
                '<h2 class="nameHeading">' + location.name + '</h2>'+
                '<h3 class="addressHeading">' + location.address +'</h3>' +
                '<h4 class="statusHeading">' + statusTitle +'</h3>'+
                '<div class="bodyContent">'+'</div>'+
            '</div>';         
            
            
            var infoWindow = new google.maps.InfoWindow({content: contentString});
            var position = new google.maps.LatLng(location.latitude, location.longitude);    
            var marker = new google.maps.Marker({position: position, map: map, title: location.name, icon:icon});
            marker.addListener('click', function() {infoWindow.open(map, marker)});
            return marker;
        });
        
        // Add a marker clusterer to manage the markers.
        var markerCluster = new MarkerClusterer(map, markers, {imagePath: imageUrl});
      }                         
JS;
        $view->registerJsFile('https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js');
        $view->registerJs($js, View::POS_READY);
    }
}
