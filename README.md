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

Tabler (Bootstrap 5) views
-----
Tabler (Bootstrap 5) label templates are provided in `views-tabler/label` for
creation, definition editing, lists and badges. Install `ea/theme-bs5` and
`yiisoft/yii2-bootstrap5`, then configure
`ea\themeBs5\components\TablerTheme` and a matching layout in the host application.
The theme selects these templates automatically when widgets render the module's
original view paths; no application-owned copies or path map are needed. Original
views remain the fallback. Label fields, codes, colours, permissions and AJAX
action names are preserved; no migration is required for the templates.

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

Model controller
----------------
Add actions d3labelsattach and d3labelsremove

Access rules
```php
                  [
                        'allow' => true,
                        'actions' => [
                            'd3labelsattach',
                            'd3labelsremove',
                        ],
                        'roles' => [
                            'ModuleAdminRoleName',
                        ],
                    ],
```

Actions
```php
    public function actions(): array
    {
        return [
            'd3labelsattach' => [
                'class' => AttachAction::class,
                'modelName' => D3pPerson::class,
            ],
            'd3labelsremove' => [
                'class' => DeleteAction::class,
                'modelName' => D3pPerson::class,
            ],
        ];
    }
```

Label admin Controller
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
                            'labels',
                            'd3labelscreate',
                            'd3labelsdefinitionremove',
                        ],
                        'roles' => [
                            'ModuleAdminRoleName'
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
            'd3labelsdefinitionedit' => [
                'class' => DefinitionEditAction::class,
                'showCode' => true,
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
    public function actionLabels(): string
    {
        return $this->render('labels', [
            'active' => true,
        ]);

    }

}

```

Label admin view
```php
<?php

use d3yii2\d3labels\widgets\D3LabelCreate;
use eaBlankonThema\assetbundles\layout\LayoutAsset;
use yii2d3\d3persons\models\D3pPerson;

LayoutAsset::register($this);

/**
 * @var \d3system\yii2\web\D3SystemView $this
 */

$title = Yii::t('d3persons', 'Labels settings');
$this->title = $title;
$this->setPageHeader($title);
$this->setPageIcon('tags');
$this->setPageWiki('drpersons-conf-person-labels');
$this->addPageButtons(\eaBlankonThema\widget\ThReturnButton::widget([
        'link' => ['d3p-person/my-company-index']
]))

/**
 * @var yii\web\View $this
 *
 * */
?>
<div class="panel panel-tab panel-tab-double shadow">
    <div class="panel-body">
        <div class="tab-content">
            <div class="tab-pane fade in active">
                <?= D3LabelCreate::widget([
                    'modelClass' => D3pPerson::class,
                    'sysCompanyId' => Yii::$app->SysCmp->getActiveCompanyId(),
                    'showCode' => false,
                ])?>
            </div>
        </div>
    </div>
</div>

```

Panel as row for showing, adding and removing labels.
Compact variant
```php
 use cewood\cwstore\models\CwbrProduct;
use d3yii2\d3labels\widgets\D3LabelCreate;

?>
<?= D3LabelCreate::widget([
    'modelClass' => CwbrProduct::class,
    'sysCompanyId' => Yii::$app->SysCmp->getActiveCompanyId(),
    'showCode' => false
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
    'sysCompanyId' => $sysCompanyId,
    'filterPrompt' => 'Filtrēt birkas',
    'showLabelsForUserId' => true, //
    //'badgeRenderOptions' => ['iconsWithText' => true],
];
```

For bulk in grid view
--------------------

Controller
```php 
use d3yii2\d3labels\logic\D3LabelBulk;

    /**
     * @return \d3yii2\d3labels\logic\D3LabelBulk
     * @throws \yii\db\Exception
     */
    private function createLabelBulk(): D3LabelBulk
    {
        return new D3LabelBulk([
            'modelClassName' => MyClASS::class,
            'sysCompanyId' => Yii::$app->SysCmp->getActiveCompanyId(),
            'userId' => Yii::$app->user->id,
            'ignoreLabelsByCode' => [RkInvoice::LABEL_CODE_CLOSED]
        ]);
    }

    public function actionIndex()
    {
            $labelBulk = $this->createLabelBulk();
            return $this->render('index', [
                'dataProvider' => $searchModel->search(),
                'searchModel' => $searchModel,
                'bulkActions' => $labelBulk->list()
            ]);        
    }

    public function actionBulk()
    {
        $request = Yii::$app->request;
        $action = $request->post('action');
        /** @var int[] $selection */
        if (!$selection = $request->post('selection')) {
            return $this->redirect(['index']);
        }

        /**
         * check access for checked rows
         */
        foreach ($selection as $id) {
            $this->findModel($id);
        }
    
        /** Labels */
        $labelBulk = $this->createLabelBulk();
        if ($labelBulk->isBulkAction($action)) {
            if ($cnt = $labelBulk->processBulkAction($action,$selection)) {
                FlashHelper::addSuccess($labelBulk->successMessage($action, $cnt));
            } else {
                FlashHelper::addSuccess($labelBulk->nothingAddedMessage($action));
            }
            return $this->redirect(['index']);
        }
    }
```


Search model
------------
Add attribute label_type to search model

```php 
    use d3yii2\d3labels\components\QuerySearch;
    
    ...
    
    public $label_type;
    
    ...
    
    public function rules() {
        return [
            ['label_type','safe']
        ];
    }    
    
    ....
    
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

get modell attached labels
--------------------------

```php
$label = D3lLabel::findModelAttachedLabels(
    'd3modules\d3invoices\models\D3cCompany',
    $args['partnerId']
)
->all();
```

get list (id => label) of model all labels
---------------------------

```php 
use d3yii2\d3labels\dictionaries\D3lDefinitionDictionary;

$list = D3lDefinitionDictionary::getList($sysCompanyId, CwStorePack::class)
```
# NOTES
Attach Note to model
-
```php
use d3yii2\d3labels\logic\D3Note;

D3Note::attach($model, $noteContent);
```

Attach User Note to model
-
```php
use d3yii2\d3labels\logic\D3Note;

D3Note::attach($model, $noteContent, $userId);
```

Detach Note from model
-
```php
use d3yii2\d3labels\logic\D3Note;

D3Note::detach($model);
```

Detach User Note from model 
-
```php
use d3yii2\d3labels\logic\D3Note;

D3Note::detach($model, $userId);
```

Show Note in Grid View column 
-
```php
use d3yii2\d3labels\widgets\D3NoteColumn;

$columns[] = [
    'attribute' => 'notes',
    'header' => 'Piezīmes',
    'class' => D3NoteColumn::class,
];
```

Show Note in Grid View column with custom Class
-
```php
use d3yii2\d3labels\widgets\D3NoteColumn;

$columns[] = [
    'attribute' => 'notes',
    'header' => 'Piezīmes',
    'class' => D3NoteColumn::class,
    'modelClasss' => ExampleModel::class,
];
```

Show Note in Widget
-
```php
use d3yii2\d3labels\widgets\D3NoteView

<?= D3NoteView::widget([
    'model' => $model,
    'canEdit' => $canEdit,
    'addButtonLink' =>  [
        'notes-add',
        'id' => $model->id,
        'ru' => ReturnUrl::getToken()
    ]
]);
```

Add action to controller for adding notes

```php
    public function actions(): array
    {
        return [
            'notes-add' => [
                'class' => AttachNoteAction::class,
                'userId' => Yii::$app->user->id,
            ]
        ];
    }

```

# REFERENCES
Typed links from a record of one model (owner) to a record of another model (target),
e.g. order → invoices created from it. Full description: [doc/references.md](doc/references.md).

- `D3lRefDef` (`d3l_ref_def`) – reference type: `code`, target model class (`sys_model_id`),
  presenter class for the widget (`class_sys_model_id`). Created by migration.
- `D3lRefRecords` (`d3l_ref_records`) – the links. Run migration `m261019_213337_d3yii2_d3labels_init_ref`.
- `D3RefList` – widget that lists the owner's references.

Define reference type by migration
-
```php
$model = new D3lRefDef([
    'sys_model_id' => SysModelsDictionary::getIdByClassName(InvInvoice::class),          // target
    'code' => OrderConfig::D3L_REF_DEF_IN_INVOICE_CODE,                                   // max 15 chars
    'class_sys_model_id' => SysModelsDictionary::getIdByClassName(InvInvoiceRef::class), // presenter
]);
if (!$model->save()) {
    throw new D3ActiveRecordException2($model);
}
```

Presenter class methods used by `D3RefList`: `__construct(int $id)`, `createIcon()`, `createTypeLabel()`,
`createLabel()`, `createUrl(string $ru)`. Example: `d3modules\d3invoices\components\InvInvoiceRef`.

Create and read references
-
```php
use d3yii2\d3labels\models\D3lRefRecords;

D3lRefRecords::createRef(OrderConfig::D3L_REF_DEF_IN_INVOICE_CODE, $order, $invoice); // idempotent
$refs = D3lRefRecords::findAllRef(OrderConfig::D3L_REF_DEF_IN_INVOICE_CODE, $order);
$refs = D3lRefRecords::findByModel($order, 'INVOICE');
$refs = D3lRefRecords::find()->findByRefModel($invoice, 'INVOICE')->all(); // reverse lookup
```

Show references
-
```php
use d3yii2\d3labels\widgets\D3RefList;

<?= D3RefList::widget([
    'model' => $model,
    //'codes' => ['INVOICE'],
]) ?>
```

Claude Code skill
-
`claude/skills/d3labels-ref/SKILL.md` – step-by-step procedure for adding a reference type
(code constant, presenter, migration, `createRef()`, widget). Copy it to the project's
`.claude/skills/d3labels-ref/` to use it as `/d3labels-ref`.
