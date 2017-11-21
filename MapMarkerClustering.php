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
        echo Html::beginTag('div', ['id' => (empty($this->options['id']) ? $this->getId() : $this->options['id']), 'class' => $this->options['class'], 'height'=>$this->options['height'], 'width'=>$this->options['width'], 'style'=>'height:500px;width:100%;']);
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
           var locations = [
            {lat: -31.563910, lng: 147.154312, info:'Text 1'},
            {lat: -33.718234, lng: 150.363181, info:'Text 2'},
            {lat: -33.727111, lng: 150.371124, info:'Text 3'},
            {lat: -33.848588, lng: 151.209834, info:'Text 4'},
            {lat: -33.851702, lng: 151.216968, info:'Text 5'},
            {lat: -34.671264, lng: 150.863657, info:'Text 6'},
            {lat: -35.304724, lng: 148.662905, info:'Text 7'},
            {lat: -36.817685, lng: 175.699196, info:'Text 8'},
            {lat: -36.828611, lng: 175.790222, info:'Text 9'},
            {lat: -37.750000, lng: 145.116667, info:'Text 10'},
            {lat: -37.759859, lng: 145.128708, info:'Text 11'},
            {lat: -37.765015, lng: 145.133858, info:'Text 12'},
            {lat: -37.770104, lng: 145.143299, info:'Text 13'},
            {lat: -37.773700, lng: 145.145187, info:'Text 14'},
            {lat: -37.774785, lng: 145.137978, info:'Text 15'},
            {lat: -37.819616, lng: 144.968119, info:'Text 16'},
            {lat: -38.330766, lng: 144.695692, info:'Text 17'},
            {lat: -39.927193, lng: 175.053218, info:'Text 18'},
            {lat: -41.330162, lng: 174.865694, info:'Text 19'},
            {lat: -42.734358, lng: 147.439506, info:'Text 20'},
            {lat: -42.734358, lng: 147.501315, info:'Text 21'},
            {lat: -42.735258, lng: 147.438000, info:'Text 22'},
            {lat: -43.999792, lng: 170.463352, info:'Text 23'},
          ];  

        //var imageUrl = 'http://chart.apis.google.com/chart?cht=mm&chs=24x32&' + 'chco=FFFFFF,008CFF,000000&ext=.png'; 
        var imageUrl = 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'; 
        
        var map = new google.maps.Map(document.getElementById("{$mapId}"), {zoom: 3, center: {lat: -28.024, lng: 140.887}});
        
        // Create an array of alphabetical characters used to label the markers.
        var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        
        // Add some markers to the map.
        // Note: The code uses the JavaScript Array.prototype.map() method to
        // create an array of markers based on a given "locations" array.
        // The map() method here has nothing to do with the Google Maps API.
        
        var markers = locations.map(function(location, i) {            
          var LatLng = new google.maps.LatLng(location.lat, location.lng);    
            return new google.maps.Marker({position: LatLng, label: labels[i % labels.length]
          });
        });
        
        // Add a marker clusterer to manage the markers.
        var markerCluster = new MarkerClusterer(map, markers, {imagePath: imageUrl});
      }
      
                         
JS;
        $view->registerJsFile('https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js');
        $view->registerJs($js, View::POS_READY);
    }
}
