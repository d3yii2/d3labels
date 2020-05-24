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

use yii\db\Migration;
use \d3yii2\d3labels\logic\D3Definition;
use d3modules\lietvediba\models\RkInvoice;
use d3system\widgets\ThBadge;
class m190329_095047_invoice_labels extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
     {
         $def = new D3Definition(RkInvoice::class);
         $def->setLabel('Warning label');
         $def->setColor(ThBadge::TYPE_INVERSE);
         $def->setCompanyId(14);
         $def->save();
    }
    public function safeDown()
    {
        echo "m190329_095047_invoice_labels cannot be reverted.\n";
        return false;
    }

}     
```

Display Widget
-----

```php
<?=\d3yii2\d3labels\widgets\D3LabelList::widget([
        //'title' => Yii::t('d3labels', 'Labels'), //Optional
        'sysCompanyId' => 1,
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
    'sysCompanyId' => $sysCompanyId
    //'badgeRenderOptions' => ['iconsWithText' => true],
];
```

attach to model record
---------------------

```php
use d3yii2\d3labels\logic\D3Label;

D3Label::attach($model->id,$labelDefId);
```