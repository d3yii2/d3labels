<?php

namespace d3yii2\d3labels\widgets;

use Closure;
use d3yii2\d3labels\logic\D3Note;
use yii\db\ActiveRecord;
use yii\grid\DataColumn;
use yii\helpers\Html;

class D3NoteColumn extends DataColumn
{
    
    /**
     * Renders a data cell.
     * @param mixed $model the data model being rendered
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data item among the item array returned by [[GridView::dataProvider]].
     * @return string the rendering result
     */
    public function renderDataCell($model, $key, $index)
    {
        if ($this->contentOptions instanceof Closure) {
            $options = call_user_func($this->contentOptions, $model, $key, $index, $this);
        } else {
            $options = $this->contentOptions;
        }
        
        $options['id'] = 'd3notes-data-col-' . $this->attribute . '-' . $key;
        $options['class'] = 'd3notes-data-col-' . $this->attribute;
        
        return Html::tag('td', $this->renderDataCellContent($model, $key, $index), $options);
    }
    
    /**
     * @param mixed $model
     * @param mixed $key
     * @param int $index
     * @return string|null
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $cellValue = '';

        $notes = D3Note::getAttachedNotes($model->id);
            
            foreach ($notes as $note)  {
                $cellValue = Html::tag('span', Html::encode($note->notes));
            }
            
        return $cellValue;
    }
}
