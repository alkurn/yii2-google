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
    public $data = [];

    /**
     * Renders the widget.
     */
    public function run()
    {
        $height = isset($this->options['height']) ? $this->options['height'] : '500px';
        $width = isset($this->options['width']) ? $this->options['width'] : '100%';

        echo Html::beginTag('div', ['id' => (empty($this->options['id']) ? $this->getId() : $this->options['id']), 'class' => $this->options['class'], 'height' => $height, 'width' => $width, 'style'=>'height:'.$height.';width:'.$width.';']);
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
        $data = $this->data;
        $mapId = (empty($this->options['id']) ? $this->getId() : $this->options['id']);
        $view = $this->getView();

        $items = [];
        foreach($data as $item){
            $items[] = ['id' => $item->id, 'name' => $item->name, 'address' => $item->address, 'logo' => $item->logo, 'latitude' => $item->latitude, 'longitude' => $item->longitude];
        }
        $data = json_encode($items);
        if (Yii::$app->Map->apiKey) {
            $this->apiKey = Yii::$app->Map->apiKey;
        }

        $view->registerJsFile(self::API_URL . http_build_query([
                'key' => $this->apiKey,
                'language' => $this->language,
                //'callback' => $this->callback
            ]));

        $js = <<<JS
         
       initMap();
       function initMap() { 
           
        var locations = $data; 
        //var imageUrl = 'http://chart.apis.google.com/chart?cht=mm&chs=24x32&' + 'chco=FFFFFF,008CFF,000000&ext=.png'; 
        var imageUrl = 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'; 
        
        var map = new google.maps.Map(document.getElementById("{$mapId}"), {zoom: 3, center: {lat: 55.8508774, lng: -4.231964}}); 
        
        // Add some markers to the map.
        // Note: The code uses the JavaScript Array.prototype.map() method to
        // create an array of markers based on a given "locations" array.
        // The map() method here has nothing to do with the Google Maps API.
        
        var markers = locations.map(function(location, i) {    
            
             var contentString = '<div class="content">'+
                '<h2 class="nameHeading">' + location.name + '</h2>'+
                '<h3 class="addressHeading">' + location.address +'</h1>'+
                '<div class="bodyContent">'+'</div>'+
            '</div>';
             
            var icon = 'http://maps.google.com/mapfiles/ms/icons/green-dot.png';
            var infoWindow = new google.maps.InfoWindow({content: contentString});
            var position = new google.maps.LatLng(location.latitude, location.longitude);    
            var marker = new google.maps.Marker({position: position, map: map, title: location.name, icon:icon});
            marker.addListener('click', function() {infoWindow.open(map, marker)});
            return marker

        });
        
        // Add a marker clusterer to manage the markers.
        var markerCluster = new MarkerClusterer(map, markers, {imagePath: imageUrl});
      }
      
                         
JS;
        $view->registerJsFile('https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js');
        $view->registerJs($js, View::POS_READY);
    }
}
