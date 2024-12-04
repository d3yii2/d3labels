<?php

namespace d3yii2\d3labels\widgets;

use d3yii2\d3labels\logic\D3Note;
use Exception;
use ReflectionClass;
use Yii;
use yii\base\Widget;
use yii\db\ActiveRecord;


/**
 * Class D3NoteView
 * Use for displaying notes without adding, removing. Can use in DetailView
 * @package d3yii2\d3labels\widgets
 * @property string $modelClass
 * @property yii\web\Controller
 * @property string $returnURLToken
 */
class D3NoteView extends Widget
{

    public const TYPE_PANEL = 'panel';
    public const TYPE_TABLE = 'table';

    public ?string $title = null;
    public ?array $addButtonLink = null;
    public ?array $removeButtonLink = null;
    public ?bool $canEdit = false;
    public ?string $type = self::TYPE_PANEL;

    /** @var object|null|ActiveRecord */
    public ?object $model = null;

    /** @var null|int **/
    public ?int $userId = null;

    /**
     * @var \d3yii2\d3labels\models\D3Note[]
     */
    private array $attached = [];


    public function getViewPath(): string
    {
        $directory = 'views';
        if (method_exists($this->getView(), 'getThemeName')) {
            $directory .= '-' . $this->getView()->getThemeName();
        }
        $class = new ReflectionClass($this);
        return dirname($class->getFileName()) . DIRECTORY_SEPARATOR . $directory;
    }

    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();
        $this->attached = D3Note::getAttachedNotes($this->model, $this->userId);
    }

    /**
     * Render the table with available labels for the model
     * @return string
     * @throws Exception
     */
    public function run(): string
    {
        if ($this->type === self::TYPE_PANEL) {
            return $this->render(
            'd3-note-view-panel' ,
            [
                'addButtonLink' => $this->addButtonLink,
                'removeButtonLink' => $this->removeButtonLink,
                'canEdit' => $this->canEdit,
                'title' => $this->title,
                'attached' => $this->attached,
            ]);
        }
        if ($this->type === self::TYPE_TABLE) {
            return $this->render(
            'd3-note-view-table' ,
            [
                'addButtonLink' => $this->addButtonLink,
                'removeButtonLink' => $this->removeButtonLink,
                'canEdit' => $this->canEdit,
                'title' => $this->title,
                'attached' => $this->attached,
            ]);
        }
        throw new Exception('Unknown type: ' . $this->type);
    }
}
