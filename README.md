# yii2-Googleplaces
Google Places Auto Complete widget for Yii2


##Installation

Add below to your `composer.json` file

```
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/alkurn/yii2-googleplaces"
        }
    ],
    "requires": {
        "alkurn/yii2-googleplaces": "*"
    }
```

##Usage

Using widget and model.

```
use alkurn\googleplaces;

echo GooglePlaces::widget([
    'model' => $model,
    'attribute' => 'location'
]);
```

Using widget for custom field name and value.

```
use alkurn\googleplaces;
echo GooglePlaces::widget([
    'name' => 'place'
    'value' => 'Jakarta'
]);

```

Using active form.

```
use yii\bootstrap\ActiveForm;
use alkurn\googleplaces;

echo $form = ActiveForm::begin();
echo $form->field($model, 'location')->widget(GooglePlaces::className());
```
