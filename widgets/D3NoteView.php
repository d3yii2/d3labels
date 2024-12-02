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

    public ?string $title = null;
    public ?array $addButtonLink = null;
    public ?bool $canEdit = false;

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
        if (method_exists($this->getView(), 'getTheme')) {
            $directory .= '-' . $this->getView()->getTheme();
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

//        if (!method_exists($this->view, 'getTheme')
//            || !$theme = $this->view->getTheme() ?? null) {
//            $this->viewPath = '@d3yii2/d3labels/views/note-path/view';
//        } else {
//            $this->viewPath = '@d3yii2/d3labels/views-' . $theme . '/note-path/view';;
//        }

        $this->attached = D3Note::getAttachedNotes($this->model, $this->userId);
    }

    /**
     * Render the table with available labels for the model
     * @return string
     * @throws Exception
     */
    public function run(): string
    {
        return $this->render('d3-note-view', [
            'addButtonLink' => $this->addButtonLink,
            'canEdit' => $this->canEdit,
            'title' => $this->title,
            'attached' => $this->attached,
        ]);
    }
}
