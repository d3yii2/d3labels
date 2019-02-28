<?php

namespace d3yii2\d3labels\widgets;

use Yii;
use yii\bootstrap\Widget;
use yii\helpers\Html;

/**
 * Class ThBadgeList
 * @package d3yii2\d3labels\widgets
 */
class ThBadgeList extends ThBadge
{
    public $items = [];
    public $separator = ' ';

    /**
     * @return string|void
     */
    public function run()
    {
        $badges = [];

        foreach ($this->items as $item) {

            $type = isset($item['type']) ? $item['type'] : parent::TYPE_WARNING;

            $badgeContent = !empty($item['faIcon'])
                ? '<i class="fa ' . $item['faIcon'] . '"></i> ' . $item['text']
                : $item['text'];

            $badges[] = parent::getBadge($badgeContent, $type);
        }

        echo implode($this->separator, $badges);
    }
}
