<?php

namespace d3yii2\d3labels\widgets;

use d3yii2\d3labels\logic\D3Note;
use eaBlankonThema\widget\ThButton;
use eaBlankonThema\widget\ThPanel;
use Exception;
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
        $headerHtml = '';
        if ($this->canEdit && $this->addButtonLink) {
            $headerHtml .= ThButton::widget([
                'label' => Yii::t('d3labels', 'Add'),
                'link' =>$this->addButtonLink,
                'icon' => ThButton::ICON_PLUS,
                'type' => ThButton::TYPE_SUCCESS,
                'size' => ThButton::SIZE_SMALL
            ]);
        }
        $headerHtml .= $this->title ?? Yii::t('d3labels', 'Notes');
        $bodyHtml = '';
        foreach ($this->attached as $note) {
            $bodyHtml .= ThPanel::widget([
                'header' => trim($note->time . ' ' . ($note->userName??'')),
                'body' => $note->notes
            ]);
        }
        return ThPanel::widget([
            'type' => ThPanel::TYPE_DEFAULT,
            'header' => $headerHtml,
            'body' => $bodyHtml,
        ]);
    }
}
