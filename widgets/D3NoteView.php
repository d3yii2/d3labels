<?php

namespace d3yii2\d3labels\widgets;

use d3system\dictionaries\SysModelsDictionary;
use d3yii2\d3labels\logic\D3Note;
use Exception;
use Yii;
use yii\base\Widget;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * Class D3NoteView
 * Use for displaying notes without adding, removing. Can use in DetailViev
 * @package d3yii2\d3labels\widgets
 * @property string $modelClass
 * @property yii\web\Controller
 * @property string $returnURLToken
 */
class D3NoteView extends Widget
{
    /** @var ActiveRecord */
    public $model;

    /** @var null|int **/
    public ?int $userId = null;

    /**
     * @var array
     */
    private $attached = [];


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
        $content = '';
        
        $notes = [];
        foreach ($this->attached as $note) {

            $notes[] = Html::tag('span', $note->notes, ['class' => 'd3notes-item']);
        }

        $content .= implode(' ', $notes);
        
        return $content;
    }
}
