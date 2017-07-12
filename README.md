# yii2-google
Google Places Auto Complete widget for Yii2


##Installation

Add below to your `composer.json` file

```
    "requires": {
        "alkurn/yii2-google": "dev-master"
    }
```
```
composer require alkurn/yii2-google
```
##Usage

Using widget and model.
Using widget for custom field name and value.

```
use alkurn\google\Places;
use alkurn\google\Map;
echo Places::widget([
    'name' => 'place'
    'value' => 'Jakarta'
]);

use alkurn\google\Map;
echo Map::widget([
    'latitude' => '34.000',
    'longitude' => '84.000',
    'title' => 'India'
]);

```

Using active form.

```
use yii\bootstrap\ActiveForm;
use alkurn\google\Places;

echo $form = ActiveForm::begin();
echo $form->field($model, 'location')->widget(Places::className());

```
