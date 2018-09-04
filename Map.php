<?php


namespace alkurn\google;

use yii\base\Widget;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\helpers\ArrayHelper;

class Map extends Widget
{
    const API_URL = '//maps.googleapis.com/maps/api/js?';
    public $language = 'en-US';
    public $sensor = true;
    public $apiKey = 'AIzaSyC2oRAljHGZArBeQc5OXY0MI5BBoQproWY';

    public $latitude = '';
    public $longitude = '';
    public $title = '';
    public $options = [];

    /**
     * Renders the widget.
     */
    public function run()
    {
        Google::widget();
        $this->registerClientScript();
        echo Html::beginTag('div', ['id' => $this->getId(), 'class' => $this->options['class']]);
        echo Html::endTag('div');
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        $latitude = $this->latitude;
        $longitude = $this->longitude;
        $title = $this->title;
        $mapId = $this->getId();
        $view = $this->getView();

        $js = <<<JS
                                                 
                var map;
                    initMap();		
                    function initMap() { 
                        var LatLng = new google.maps.LatLng("{$latitude}", "{$longitude}");  
                        map = new google.maps.Map(document.getElementById("{$mapId}"), {
                            center: LatLng,
                            zoom: 8,
                            title: "{$title}"
                        });
                        
                        var marker = new google.maps.Marker({position: LatLng,title:"{$title}"});
                        marker.setMap(map);
                    }                     
JS;
        $view->registerJs($js, View::POS_READY);
    }
}
