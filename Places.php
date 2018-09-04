<?php


namespace alkurn\google;

use yii\web\JqueryAsset;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\web\View;

class Places extends InputWidget
{

    const API_URL = '//maps.googleapis.com/maps/api/js?';
    public $libraries = 'places';
    public $language = 'en-US';
    public $sensor = true;
    public $apiKey = 'AIzaSyC2oRAljHGZArBeQc5OXY0MI5BBoQproWY';
    public $autocompleteOptions = [];

    /**
     * Renders the widget.
     */

    public function run()
    {
        Google::widget();
        $this->registerClientScript();
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        $elementId = $this->options['id'];
        $scriptOptions = json_encode($this->autocompleteOptions);
        $view = $this->getView();

        $js = <<<JS
            (function(){
                var input = document.getElementById('{$elementId}');
                var options = {$scriptOptions};
                new google.maps.places.Autocomplete(input, options);
            })();                        
JS;
        $view->registerJs($js, View::POS_READY);
    }
}
