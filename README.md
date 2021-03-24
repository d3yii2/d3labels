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

DB
----
![DB strukture](https://github.com/d3yii2/d3labels/blob/master/doc/DbSchema.png)

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
         //$def->setCompanyId(14);
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
        'model' => $model,
        'readOnly' => true,
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

// by record id and labelDefId
$labelDefId = D3lDefinitionDictionary::findByCodeModel($labelCode,InvInvoice::class);
D3Label::attach($model->id,$labelDefId);

// by model and label code
D3Label::attachByModelCode($model,$labelCode);

```


dettach from model record
---------------------

```php
use d3yii2\d3labels\logic\D3Label;

// by record id and labelDefId
$labelDefId = D3lDefinitionDictionary::findByCodeModel($labelCode,InvInvoice::class);
D3Label::detach($model->id,$labelDefId);

// by model and label code
D3Label::detachByModelCode($model,$labelCode);

```

Widget and action adding to controller for managing labels
-------------------------
controller actions
```php
    public function actions()
    {
        return [
            'd3labelscreate' => [
                'class' => '\d3yii2\d3labels\components\CreateAction',
                'modelName' => RkInvoice::class,
                'sysCompanyId' => static function(){
                    return Yii::$app->SysCmp->getActiveCompanyId();
                }
            ],
            'd3labelsattach' => [
                'class' => '\d3yii2\d3labels\components\AttachAction',
                'modelName' => RkInvoice::class,
            ],
            'd3labelsremove' => [
                'class' => '\d3yii2\d3labels\components\DeleteAction',
                'modelName' => RkInvoice::class,
            ],
            'd3labelsdefinitionremove' => [
                'class' => '\d3yii2\d3labels\components\DefinitionDeleteAction',
                'modelName' => RkInvoice::class,
                'sysLabelsIdList' => [2]
            ],
        ];
    }
```

```php
 echo D3LabelCreate::widget([
                    'modelClass' => RkInvoice::class,
                    'sysCompanyId' => Yii::$app->SysCmp->getActiveCompanyId()
                ]);
```

Widget and controller for adding and removing labels from model
------------------------------

Define actions in controller
```php
            
    public function actions()
    {
        return [
            
            'd3labelsattach' => [
                'class' => '\d3yii2\d3labels\components\AttachAction',
                'modelName' => RkInvoice::class,
            ],
            'd3labelsremove' => [
                'class' => '\d3yii2\d3labels\components\DeleteAction',
                'modelName' => RkInvoice::class,
            ],
            'd3labelsdefinitionremove' => [
                'class' => '\d3yii2\d3labels\components\DefinitionDeleteAction',
                'modelName' => RkInvoice::class,
                'sysLabelsIdList' => [2]
            ],
        ];
    }
```

Widget
```php
echo D3LabelList::widget([
    'model' => $model,
    'sysCompanyId' => Yii::$app->SysCmp->activeCompanyId,
    'readOnly' => !$fullAccess,
]);
```