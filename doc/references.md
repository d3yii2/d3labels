# References between records (D3lRefDef, D3lRefRecords, D3RefList)

References store typed links from a record of one model (the **owner**) to a record of another
model (the **target**). Example: an order (`D3oOrder`) keeps links to the invoices (`InvInvoice`)
that were created from it, and the order view lists them with links.

Three parts work together:

| Part | Class | Table | Role |
|------|-------|-------|------|
| Definition | `d3yii2\d3labels\models\D3lRefDef` | `d3l_ref_def` | Reference type: code, target model class, presenter class |
| Reference record | `d3yii2\d3labels\models\D3lRefRecords` | `d3l_ref_records` | One link: owner record → target record of a definition |
| Widget | `d3yii2\d3labels\widgets\D3RefList` | – | Panel that lists the owner's references |

Tables are created by migration `m261019_213337_d3yii2_d3labels_init_ref`.
Model classes are registered in `sys_models` (d3system) and referenced by `sys_model_id`.

## Data model

```
d3l_ref_def                          d3l_ref_records
-----------                          ---------------
id                 tinyint  PK  <─── def_id            tinyint  FK
code               char(15)          sys_model_id      tinyint  FK sys_models  (owner model class)
sys_model_id       tinyint  FK       record_id         int      (owner record id)
                   (target model class)
class_sys_model_id tinyint  FK       ref_record_id     int      (target record id)
                   (presenter class)
```

- `d3l_ref_def.sys_model_id` – class of the **target** model, the class whose id is stored in
  `d3l_ref_records.ref_record_id` (e.g. `InvInvoice`). Used for reverse lookups (`findByRefModel`).
- `d3l_ref_def.class_sys_model_id` – **presenter** class that `D3RefList` instantiates to render a
  row (e.g. `d3modules\d3invoices\components\InvInvoiceRef`). Required if the widget is used.
- `d3l_ref_def.code` – unique code used in PHP (`D3lRefRecords::createRef('INVOICE', ...)`).
- The definition is **not** bound to an owner model. The owner class is stored on each
  `d3l_ref_records` row, so one definition can be used by several owner models.

Limits:
- `code` is at most 15 characters. Uniqueness is a convention; there is no unique index.
  `D3lRefDefDictionary::getIdByCode()` returns the last id for a duplicated code.
- `id`/`def_id`/`sys_model_id` are `tinyint unsigned`: at most 255 definitions and 255 model classes.
- `record_id`/`ref_record_id` are `int unsigned` (not bigint).
- `ref_record_id` has no foreign key to the target table. Deleting a target or owner record does
  not delete its references.

## 1. Defining a reference type (D3lRefDef)

### Code constant

Keep the code in a constant of the module that uses it:

```php
class OrderConfig
{
    public const D3L_REF_DEF_IN_INVOICE_CODE = 'INVOICE';
}
```

### Presenter class

`D3RefList` creates the presenter with `new $presenterClass($refRecordId)` and calls four methods.
There is no interface; implement all of them:

| Method | Returns | Used for |
|--------|---------|----------|
| `__construct(int $id)` | – | Receives `ref_record_id`, loads the target record |
| `createIcon(): string` | HTML | Icon in the first column (`<i class="fa fa-file-text-o"></i>`) |
| `createTypeLabel(): string` | text (encoded by the view) | Type name in the first column |
| `createLabel(): string` | text | Link text |
| `createUrl(string $ru): array\|string` | route | Link URL; `$ru` is the `ReturnUrl` token |

Example (`d3modules\d3invoices\components\InvInvoiceRef`):

```php
namespace d3modules\d3invoices\components;

use d3modules\d3invoices\models\InvInvoice;
use Yii;

class InvInvoiceRef
{
    private $invoice;

    public function __construct(int $id)
    {
        $this->invoice = InvInvoice::findOne($id);
    }

    public function createIcon(): string
    {
        return '<i class="fa fa-file-text-o"></i>';
    }

    public function createTypeLabel(): string
    {
        return Yii::t('d3invoices', 'Invoice');
    }

    public function createLabel(): string
    {
        return $this->invoice->getLabel(false);
    }

    public function createUrl(string $ru): array
    {
        return ['/invoices/inv-invoice/view', 'id' => $this->invoice->id, 'ru' => $ru];
    }
}
```

If the target record may have been deleted, handle `findOne()` returning `null`
(otherwise the widget fails on that row).

### Migration

Create the definition in a migration of the module that owns the code:

```php
use adrona\adronaorder\OrderConfig;
use d3modules\d3invoices\components\InvInvoiceRef;
use d3modules\d3invoices\models\InvInvoice;
use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException2;
use d3yii2\d3labels\models\D3lRefDef;
use yii\db\Migration;

class m261025_183723_adrona_adronaorder_D3lRefInDefInvoice extends Migration
{
    public function safeUp()
    {
        $model = new D3lRefDef([
            'sys_model_id' => SysModelsDictionary::getIdByClassName(InvInvoice::class),        // target model
            'code' => OrderConfig::D3L_REF_DEF_IN_INVOICE_CODE,
            'class_sys_model_id' => SysModelsDictionary::getIdByClassName(InvInvoiceRef::class), // presenter
        ]);
        if (!$model->save()) {
            throw new D3ActiveRecordException2($model);
        }
    }

    public function safeDown()
    {
        echo "m261025_183723_adrona_adronaorder_D3lRefInDefInvoice cannot be reverted.\n";
        return false;
    }
}
```

`SysModelsDictionary::getIdByClassName()` registers the class in `sys_models` if it is missing,
so the presenter class doesn't need a separate registration.

### Dictionary and cache

`d3yii2\d3labels\dictionaries\D3lRefDefDictionary` caches the list `id => code` for one hour
(`Yii::$app->cache`, key `D3lRefDefDictionaryList`) and in a static property for the request.

| Method | Returns |
|--------|---------|
| `getList(): array` | `[id => code]`, sorted by code |
| `getIdByCode(string $code): ?int` | definition id or `null` |
| `getLabel(int $id): ?string` | code of the definition |
| `clearCache(): void` | deletes the cache entry |

`D3lRefDef::afterSave()`/`afterDelete()` call `clearCache()`. Notes:
- If definitions are inserted with plain SQL (`$this->insert(...)`), call
  `D3lRefDefDictionary::clearCache()` yourself.
- `clearCache()` does not reset the static per-request list. If the same PHP process reads the
  list, then creates a definition and then calls `createRef()` with the new code, the code is not
  found. Create definitions and references in separate requests/migrations.

`D3lRefDef::optsD3lRefDefDictionary()` returns the same list for dropdowns.

## 2. Reference records (D3lRefRecords)

### Creating a reference

```php
use d3yii2\d3labels\models\D3lRefRecords;

D3lRefRecords::createRef(
    OrderConfig::D3L_REF_DEF_IN_INVOICE_CODE, // definition code
    $order,                                   // owner ActiveRecord
    $invoice                                  // target ActiveRecord
);
```

- Stores `sys_model_id` = owner class, `record_id` = `$order->id`, `def_id` = id of the code,
  `ref_record_id` = `$invoice->id`. Both models must have an `id` attribute.
- Idempotent: if the same row exists, nothing is saved.
- Throws `D3ActiveRecordException2` if validation fails, also when the code is unknown
  (`def_id` is required).
- Call it in the same DB transaction as the business operation that creates the link
  (see `adrona\adronaorder\components\CreateInvoice::createRef()`).
- The link is one-directional. To show it from the target side too, create a second definition and
  a reverse reference, or use `findByRefModel()`.

### Reading references of an owner

```php
// all references of a definition
$refs = D3lRefRecords::findAllRef(OrderConfig::D3L_REF_DEF_IN_INVOICE_CODE, $order); // D3lRefRecords[]

// all references, optionally filtered by code
$refs = D3lRefRecords::findByModel($order);
$refs = D3lRefRecords::findByModel($order, 'INVOICE');

// load target models
$invoiceIds = \yii\helpers\ArrayHelper::getColumn($refs, 'ref_record_id');
$invoices = InvInvoice::find()->where(['id' => $invoiceIds])->all();

// check existence (e.g. to hide the "Create invoice" button)
if (!D3lRefRecords::findAllRef(OrderConfig::D3L_REF_DEF_IN_INVOICE_CODE, $order)) { ... }
```

### Query methods (D3lRefRecordsQuery)

| Method | Condition |
|--------|-----------|
| `findByModel(object $model, ?string $code = null)` | owner: `record_id = $model->id`, `sys_model_id` = owner class; with `$code` joins `d3l_ref_def` |
| `findByRefModel(object $refModel, ?string $code = null)` | reverse: `ref_record_id = $refModel->id` and `d3l_ref_def.sys_model_id` = target class |

```php
// which orders reference this invoice?
$refs = D3lRefRecords::find()
    ->findByRefModel($invoice, OrderConfig::D3L_REF_DEF_IN_INVOICE_CODE)
    ->all();
foreach ($refs as $ref) {
    $ownerClass = $ref->sysModel->class_name; // owner model class
    $ownerId = $ref->record_id;
}
```

Relations: `getDef()` → `D3lRefDef`, `getSysModel()` → `SysModels` (owner class).

### Deleting references

There is no helper. Delete with `deleteAll()`, e.g. in the owner's or target's `afterDelete()`:

```php
D3lRefRecords::deleteAll([
    'sys_model_id' => SysModelsDictionary::getIdByClassName(D3oOrder::class),
    'record_id' => $order->id,
]);

D3lRefRecords::deleteAll([
    'def_id' => D3lRefDefDictionary::getIdByCode(OrderConfig::D3L_REF_DEF_IN_INVOICE_CODE),
    'ref_record_id' => $invoice->id,
]);
```

## 3. Widget D3RefList

Lists the references of an owner record in a panel: icon and type label, and a link to the target
record (with `ReturnUrl` token). Shows "No references" if there are none.

```php
use d3yii2\d3labels\widgets\D3RefList;

<?= D3RefList::widget([
    'model' => $model,            // required, owner ActiveRecord
    // 'codes' => ['INVOICE'],    // optional, only these definition codes
]) ?>
```

| Property | Type | Default | Purpose |
|----------|------|---------|---------|
| `model` | `?ActiveRecord` | `null` | Owner record. Required; otherwise `yii\base\Exception('Model is not set')` |
| `codes` | `?array` | `null` | Filter by `d3l_ref_def.code` |
| `title` | `?string` | `Yii::t('d3labels', 'Refernces')` | Panel header. Currently `init()` always overwrites it, so a passed value is ignored |

How it works:
- Selects `d3l_ref_records` by owner class (`sys_model_id`) and `$model->primaryKey`, joined with
  `d3l_ref_def`.
- For every row it creates `new <presenter>(ref_record_id)` from `d3l_ref_def.class_sys_model_id`
  (one presenter per row, usually one DB query per row).
- View: `widgets/views/ref-list.php`, rendered with `eaBlankonThema` `ThPanel` and `ThExternalLink`
  (Bootstrap 3 / Blankon). There is no Tabler variant.
- Translation category `d3labels`: messages `Refernces`, `No references`.

Requirements:
- Every definition used by the owner has `class_sys_model_id` set and the class exists
  (otherwise "Undefined array key" / class not found).
- The presenter implements the methods listed in section 1.

Used in: `vendor/adrona/adronaorder/views/order/view.php`,
`vendor/d3modules/d3invoices/views/inv-invoice/_view.php`.

## Checklist for a new reference type

1. Code constant in the owning module (≤ 15 chars).
2. Presenter class for the target model.
3. Migration that creates `D3lRefDef` (target model + presenter).
4. `D3lRefRecords::createRef()` where the link is created.
5. `D3RefList::widget(['model' => $owner])` in the owner's view.
6. Optional: cleanup in `afterDelete()`, reverse reference.

A Claude Code skill with this procedure is in `claude/skills/d3labels-ref/SKILL.md`.
