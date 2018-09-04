<?php


namespace alkurn\google;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\helpers\ArrayHelper;

class Google extends Widget
{
    const API_URL = '//maps.googleapis.com/maps/api/js?';
    public $language = 'en-US';
    public $sensor = true;
    public $apiKey = 'AIzaSyC2oRAljHGZArBeQc5OXY0MI5BBoQproWY';

    public $latitude = '';
    public $longitude = '';
    public $title = '';
    public $options = [];

    public $libraries = null;
    public $items = [];

    public function run()
    {
        $view = $this->getView();
        if (Yii::$app->google->apiKey) {
            $this->apiKey = Yii::$app->google->apiKey;
        }
        $this->libraries = Yii::$app->google->libraries;

        $queries = ['key' => $this->apiKey, 'language' => $this->language,];
        $libraries = (is_array($this->libraries)) ? implode(',', $this->libraries) : (is_string($this->libraries) ? $this->libraries : null);
        if (!is_null($libraries)) {
            $queries = array_merge(['libraries' => $libraries], $queries);
        }

        $jsFile = self::API_URL . http_build_query($queries);
        $view->registerJsFile($jsFile, ['depends' => JqueryAsset::class]);
        if (in_array(self::API_URL, $view->jsFiles)) {
            unset($view->jsFiles[$jsFile]);
        }
    }
}