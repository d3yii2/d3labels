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
    $def = new D3Definition(RkInvoice::class);
    $def->setLabel('Warning label');
    $def->setColor('#FFFFFF');
    $def->setSysCompanyId(14);
    $def->save();
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

for GridView D3LabelColumn
----

```php
<?php
$columns[] = [
    'class' => d3yii2\d3labels\components\D3LabelColumn::class,                    
    'model' => $searchModel,
    'attribute'=>'label_type',
    'format'=>'raw',
    'label' => Yii::t('d3labels', 'Labels'),
    //'badgeRenderOptions' => ['iconsWithText' => true],
];
```