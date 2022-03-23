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

Configuration
-----

Add d3labels to migration path and run migration
```php
  'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => [
                '@vendor/d3yii2/d3labels/migrations',
            ]
  ]              
```


Add to modules d3labels
```php
    'modules' => [
        'd3labels' => [
            'class' => 'd3yii2\d3labels\Module',
        ],
    ]
```

Define Labels by migration
-----
Migration example for adding new label
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
         $def->setCode('WarningLabel');
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

Migration example for removing label
```php

use d3modules\d3accexport\logic\ExportRkInvoiceFormExtensions;
use d3modules\lietvediba\models\RkInvoice;
use d3yii2\d3labels\logic\D3LabelMaintenance;
use yii\db\Migration;

class m210426_100707_label_new_remove  extends Migration {

    public function safeUp() {
        $removedLabelsFromModelRecords = D3LabelMaintenance::removeLabel(ExportRkInvoiceFormExtensions::NEW,RkInvoice::class);
        echo 'Removed Labels FromModel Records: ' . $removedLabelsFromModelRecords .PHP_EOL;

    }

    public function safeDown() {
        echo "m210426_100707_label_new_remove cannot be reverted.\n";
        return false;
    }
}
```

Widget for creating labels
---------------

Controller
-------
```php

namespace cewood\cwstore\controllers;

use cewood\cwstore\models\CwbrProduct;
use ea\app\controllers\LayoutController;
use yii\filters\AccessControl;
use Yii;
use d3yii2\d3labels\components\CreateAction;
use d3yii2\d3labels\components\AttachAction;
use d3yii2\d3labels\components\DeleteAction;
use d3yii2\d3labels\components\DefinitionDeleteAction;

/**
 * Class RkInvoiceSettingsController
 * @package d3modules\lietvediba\controllers
 */
class SettingsController extends LayoutController
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'index',
                            'labels',
                            'd3labelscreate',
                            'd3labelsattach',
                            'd3labelsremove',
                            'd3labelsdefinitionremove',
                        ],
                        'roles' => [
                            'CwStoreFullUserRole'
                        ]
                    ],
                ]
            ],
        ];

    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'd3labelscreate' => [
                'class' => CreateAction::class,
                'modelName' => CwbrProduct::class,
                'sysCompanyId' => static function(){
                    return Yii::$app->SysCmp->getActiveCompanyId();
                }
            ],
            'd3labelsattach' => [
                'class' => AttachAction::class,
                'modelName' => CwbrProduct::class,
            ],
            'd3labelsremove' => [
                'class' => DeleteAction::class,
                'modelName' => CwbrProduct::class,
                'labelAccessRoles' => [
                    'WarningLabel' => [
                        'FinanceAdmin'
                    ]
                ]
            ],                
            'd3labelsdefinitionremove' => [
                'class' => DefinitionDeleteAction::class,
                'modelName' => CwbrProduct::class,
                //'sysLabelsIdList' => [2]
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('labels', [
            'active' => true,
        ]);

    }

    /**
     * @return string
     */
    public function actionLabels(): string
    {
        return $this->render('labels', [
            'active' => true,
        ]);

    }

}

```

Panel as row for showing, adding and removing labels.
Compact variant
```php
 use cewood\cwstore\models\CwbrProduct;
use d3yii2\d3labels\widgets\D3LabelCreate;

?>
<?= D3LabelCreate::widget([
    'modelClass' => CwbrProduct::class,
    'sysCompanyId' => Yii::$app->SysCmp->getActiveCompanyId()
]) ?>
```

Simple output in DetailView
```php
        echo ThDetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'label' => 'Labels',
                    'format' => 'raw',
                    'value' => D3LabelView::widget([
                        'model' => $model,
                        'sysCompanyId' => 1
                    ])
                ]            
            ]       
            ,
        ]);
```

Widget for model for displaying, attaching and removing labels from record
-----

```php
<?=\d3yii2\d3labels\widgets\D3LabelList::widget([
        //'title' => Yii::t('d3labels', 'Labels'), //Optional
        'sysCompanyId' => 1,
        'model' => $model,
        'readOnly' => true,
])?>
```

controller actions for widget D3LabelList
```php
    public function actions()
    {
        return [

            'd3labelsattach' => [
                'class' => AttachAction::class,
                'modelName' => CwbrProduct::class,
            ],
            'd3labelsremove' => [
                'class' => DeleteAction::class,
                'modelName' => CwbrProduct::class,
            ],
        ];
    }
```

for GridView D3LabelColumn
----
Column for grid
```php
<?php
$columns[] = [
    'class' => d3yii2\d3labels\components\D3LabelColumn::class,                    
    'model' => $searchModel,
    'modelClass' => \cewood\cwstore\models\CwbrProduct::class,
    'attribute'=>'label_type',
    'format'=>'raw',
    'filterNotAssignedLabel' => true,
    'label' => Yii::t('d3labels', 'Labels'),
    'sysCompanyId' => $sysCompanyId
    //'badgeRenderOptions' => ['iconsWithText' => true],
];
```
Search model
------------
Add attribute label_type

```php 
    QuerySearch::addFilter($query, $this->label_type, '`inv_invoice`.`id`');
```

Export to excel
--------------

```php
// create widget object
$labelWidget = new  D3LabelColumn([
    'model' => $searchModel,
    'modelClass' => \cewood\cwstore\models\CwbrProduct::class,
    'attribute'=>'label_type',
    'format'=>'raw',
    'label' => Yii::t('d3labels', 'Labels'),
    'sysCompanyId' => Yii::$app->SysCmp->getActiveCompanyId(),
    'dataProvider' => $dataProvider
]);

// columns
    [
        'header' => 'Labels',
        'value' => static function($model) use ($labelWidget){
            return $labelWidget->renderForExcel($model);
        }
    ]
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


detach from model record
---------------------

```php
use d3yii2\d3labels\logic\D3Label;

// by record id and labelDefId
$labelDefId = D3lDefinitionDictionary::findByCodeModel($labelCode, InvInvoice::class);
D3Label::detach($model->id, $labelDefId);

// by model and label code
D3Label::detachByModelCode($model->id, $labelCode);

```


```php
 echo D3LabelCreate::widget([
                    'modelClass' => RkInvoice::class,
                    'sysCompanyId' => Yii::$app->SysCmp->getActiveCompanyId()
                ]);
```

get list (id => label) of model all labels
---------------------------

```php 
use d3yii2\d3labels\dictionaries\D3lDefinitionDictionary;

$list = D3lDefinitionDictionary::getList($sysCompanyId, CwStorePack::class)
```