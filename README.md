D3Labels
========
Assign multiple labels to model

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist d3yii2/d3labels "*"
```

or add

```
"d3yii2/d3labels": "*"
```

to the require section of your `composer.json` file.


Define Labels
-----
Migration example
```php
public function safeUp()
{
    $items = [
        [
            'modelClass' => 'd3modules\lietvediba\models\RkInvoice',
            'label' => ['title' => 'Test #1', 'icon' => 'test1'],
        ],
        [
            'modelClass' => 'd3modules\lietvediba\models\RkInvoice',
            'label' => ['title' => 'Test #2', 'icon' => 'test2'],
        ]
    ];

    foreach ($items as $label) {
        $def = new \d3yii2\d3labels\logic\D3Definition($label['modelClass'], $label['label']);
        $def->save();
    }

    return true;
}
```

Display Widget
-----

```php
<?=\d3yii2\d3labels\widgets\D3LabelList::widget([
        //'title' => Yii::t('d3labels', 'Labels'), //Optional
        'model' => $model
])?>
```