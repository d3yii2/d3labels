---
name: d3labels-ref
description: "Add or use d3yii2/d3labels record references: define a D3lRefDef type (migration + presenter class), create/read/delete D3lRefRecords links between two models, and show them with the D3RefList widget. Use when asked to link records of two models (order → invoice etc.), add a reference type, or list references in a view."
argument-hint: "owner: <OwnerModel> target: <TargetModel> code: <CODE> create in: <class::method> show in: <view> [module: <package>]"
---

# d3labels references (D3lRefDef / D3lRefRecords / D3RefList)

## Input

```
/d3labels-ref owner: <OwnerModel> target: <TargetModel> code: <CODE> create in: <class::method> show in: <view> [module: <package>]
```

Arguments: `$ARGUMENTS`

| Argument | Meaning | Example |
|----------|---------|---------|
| `owner` | Model whose record holds the reference (`d3l_ref_records.sys_model_id/record_id`) | `adrona\adronaorder\models\D3oOrder` |
| `target` | Model the reference points to (`ref_record_id`, `d3l_ref_def.sys_model_id`) | `d3modules\d3invoices\models\InvInvoice` |
| `code` | Definition code, ≤ 15 chars, unique | `INVOICE` |
| `create in` | Where the link is created | `adrona\adronaorder\components\CreateInvoice::createRef` |
| `show in` | View(s) where `D3RefList` is rendered | `vendor/adrona/adronaorder/views/order/view.php` |
| `module` | Package that owns the code constant and migration. Default: the owner's package | `vendor/adrona/adronaorder` |

Missing `owner`, `target` or `code` → ask. Missing `create in` / `show in` → do only the other steps and say so.

Full reference: `vendor/d3yii2/d3labels/doc/references.md`. Read it if something below is unclear.

## How it works (do not re-derive)

- `d3l_ref_def`: `id` (tinyint), `code` char(15), `sys_model_id` = **target** model class,
  `class_sys_model_id` = **presenter** class for the widget. Not bound to an owner model.
- `d3l_ref_records`: `sys_model_id` = **owner** class, `record_id` = owner id, `def_id`,
  `ref_record_id` = target id. No FK to owner/target tables.
- Class ids come from `d3system\dictionaries\SysModelsDictionary::getIdByClassName()`, which
  registers a missing class in `sys_models`.
- `D3lRefDefDictionary` (`getList`, `getIdByCode`, `getLabel`, `clearCache`) caches `id => code`
  for 1 h plus a static per-request copy. `D3lRefDef::afterSave/afterDelete` clear the cache, but
  not the static copy: don't create a definition and use its code in the same PHP process.
- `D3lRefRecords::createRef($code, $owner, $target)` – uses `->id` of both, idempotent, throws
  `D3ActiveRecordException2` on failure (unknown code → `def_id` required error).
- `D3lRefRecords::findAllRef($code, $owner)`, `D3lRefRecords::findByModel($owner, ?$code)` → `D3lRefRecords[]`.
- `D3lRefRecords::find()->findByModel($owner, ?$code)` / `->findByRefModel($target, ?$code)` → query.
- `D3RefList::widget(['model' => $owner, 'codes' => [...]])` renders a Blankon `ThPanel`; for each row
  it does `new <presenter>($ref_record_id)` and calls `createIcon()`, `createTypeLabel()`,
  `createLabel()`, `createUrl(string $ru)`. `title` is overwritten in `init()` (passing it has no effect).

## Step 1: Inspect

1. Resolve `owner`, `target` and `module` to classes and paths. Confirm both models have an `id` PK.
2. Check the code isn't taken:
   `grep -rn "D3L_REF_DEF_\|D3lRefDef(" vendor/<org> --include=*.php` and, if DB access exists,
   `SELECT * FROM d3l_ref_def`.
3. Look for an existing presenter of the target (`grep -rln "function createTypeLabel" vendor`).
   Reuse it if it exists (e.g. `d3modules\d3invoices\components\InvInvoiceRef`).
4. Check the module's migration path is registered in the app's console config.

## Step 2: Code constant

In the module's config/constant class (e.g. `OrderConfig`), next to existing ones:

```php
public const D3L_REF_DEF_<NAME>_CODE = '<CODE>';
```

Follow the project's PHP compatibility rule (AGENTS.md). Typed class constants (`const string`) need PHP 8.3.

## Step 3: Presenter class (only if none exists)

`<target package>/components/<Target>Ref.php`:

```php
<?php

namespace <ns>\components;

use <TargetClass>;
use Yii;
use yii\helpers\Html;

class <Target>Ref
{
    /** @var <Target>|null */
    private $model;

    public function __construct(int $id)
    {
        $this->model = <Target>::findOne($id);
    }

    public function createIcon(): string
    {
        return '<i class="fa fa-file-text-o"></i>';
    }

    public function createTypeLabel(): string
    {
        return Yii::t('<category>', '<Target type>');
    }

    public function createLabel(): string
    {
        return $this->model ? (string)<label expression> : Yii::t('<category>', 'Deleted');
    }

    /** @return array|string */
    public function createUrl(string $ru)
    {
        return $this->model ? ['/<module>/<controller>/view', 'id' => $this->model->id, 'ru' => $ru] : '#';
    }
}
```

- `createTypeLabel()` is HTML-encoded by the widget; `createIcon()` is raw HTML; `createLabel()` goes to `ThExternalLink`.
- Handle a deleted target (`findOne()` → `null`).
- Use the target model's existing label method if it has one (`getLabel()`, `__toString`).

## Step 4: Migration

Generate the file in the module's `migrations/` with the project's naming
(`m<yymmdd>_<hhmmss>_<vendor>_<package>_D3lRefDef<Name>.php`, or `php yii migrate/create` with the module path):

```php
<?php

use <ConfigClass>;
use <PresenterClass>;
use <TargetClass>;
use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException2;
use d3yii2\d3labels\models\D3lRefDef;
use yii\db\Migration;

class <migrationName> extends Migration
{
    public function safeUp()
    {
        $model = new D3lRefDef([
            'sys_model_id' => SysModelsDictionary::getIdByClassName(<Target>::class),
            'code' => <ConfigClass>::D3L_REF_DEF_<NAME>_CODE,
            'class_sys_model_id' => SysModelsDictionary::getIdByClassName(<Target>Ref::class),
        ]);
        if (!$model->save()) {
            throw new D3ActiveRecordException2($model);
        }
    }

    public function safeDown()
    {
        echo "<migrationName> cannot be reverted.\n";
        return false;
    }
}
```

- `sys_model_id` is the **target** class, not the owner. `class_sys_model_id` is the **presenter**.
- Fix the `safeDown()` message to the real class name.
- Don't call `createRef()` with this code in the same migration (static dictionary cache).

## Step 5: Create references

At the point where the link is created (`create in`), inside the existing transaction:

```php
use d3yii2\d3labels\models\D3lRefRecords;

D3lRefRecords::createRef(<ConfigClass>::D3L_REF_DEF_<NAME>_CODE, $owner, $target);
```

- Both records must already be saved (`id` set).
- Let `D3ActiveRecordException2` propagate so the transaction rolls back, unless the caller already
  catches and logs exceptions.
- If the link must be visible from the target too, add a second definition (owner as target, with an
  owner presenter) and a second `createRef($reverseCode, $target, $owner)`.

Reading, when the task needs it:

```php
$refs = D3lRefRecords::findAllRef(<ConfigClass>::D3L_REF_DEF_<NAME>_CODE, $owner);
$targetIds = ArrayHelper::getColumn($refs, 'ref_record_id');

$ownerRefs = D3lRefRecords::find()->findByRefModel($target, <ConfigClass>::D3L_REF_DEF_<NAME>_CODE)->all();
```

Cleanup, only when the task asks (no FK cascades):

```php
D3lRefRecords::deleteAll([
    'def_id' => D3lRefDefDictionary::getIdByCode(<ConfigClass>::D3L_REF_DEF_<NAME>_CODE),
    'ref_record_id' => $target->id,
]);
```

## Step 6: Widget

In each `show in` view (check first that it isn't already rendered there):

```php
use d3yii2\d3labels\widgets\D3RefList;

<?= D3RefList::widget([
    'model' => $model,
    // 'codes' => [<ConfigClass>::D3L_REF_DEF_<NAME>_CODE],
]) ?>
```

- Without `codes` it shows all references of the owner, so every used definition needs a presenter.
- The widget renders a Blankon/BS3 `ThPanel`. On a Tabler/BS5 page, warn that there is no Tabler view.
- Place it in a grid column like other panels of the view (`<div class="col-md-6">`).

## Step 7: Verify and report

- `php -l` every new or changed PHP file. If PHP isn't available, say so.
- If allowed, run the migration (`php yii migrate`) and check `SELECT * FROM d3l_ref_def WHERE code = '<CODE>'`.
- Manual check: perform the action from `create in`, open the owner view, the panel lists the target with a working link.
- Report:
  - files created or changed;
  - the code, the target class and the presenter class;
  - the migration name and whether it was run;
  - assumptions (presenter label/URL/icon, reverse reference, cleanup);
  - the README updated, per AGENTS.md documentation rules.

## Example

```
/d3labels-ref owner: adrona\adronaorder\models\D3oOrder target: d3modules\d3invoices\models\InvInvoice code: INVOICE create in: adrona\adronaorder\components\CreateInvoice::createRef show in: vendor/adrona/adronaorder/views/order/view.php
```

Result:
- `OrderConfig::D3L_REF_DEF_IN_INVOICE_CODE = 'INVOICE'`;
- presenter `d3modules\d3invoices\components\InvInvoiceRef` (reused);
- migration `m261025_183723_adrona_adronaorder_D3lRefInDefInvoice`;
- `D3lRefRecords::createRef(OrderConfig::D3L_REF_DEF_IN_INVOICE_CODE, $this->order, $invoice)` in `CreateInvoice`;
- `D3RefList::widget(['model' => $model])` in `views/order/view.php`.
