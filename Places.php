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
        $this->registerClientScript();
        if ($this->hasModel()) {
            echo Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textInput($this->name, $this->value, $this->options);
        }
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        $elementId = $this->options['id'];
        $scriptOptions = json_encode($this->autocompleteOptions);
        $view = $this->getView();

        if (\Yii::$app->Places->apiKey) {
            $this->apiKey = \Yii::$app->Places->apiKey;
        }

        $jsFile = self::API_URL . http_build_query(['libraries' => $this->libraries,'key' => $this->apiKey, 'language' => $this->language]);
        $view->registerJsFile($jsFile, ['depends' => JqueryAsset::class]);
        if (in_array(self::API_URL, $view->jsFiles)) {
            unset($view->jsFiles[$jsFile]);
        }


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
