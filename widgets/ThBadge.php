<?php

namespace d3yii2\d3labels\widgets;

use Yii;
use yii\bootstrap\Widget;
use yii\helpers\Html;

/**
 * Class ThBadge
 * @package d3yii2\d3labels\widgets
 */
class ThBadge extends Widget
{

    const TYPE_SUCCESS = 'success';
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_DANGER = 'danger';
    const TYPE_DEFAULT = 'default';
    const TYPE_PRIMARY = 'primary';
    const TYPE_LILAC = 'lilac';
    const TYPE_INVERSE = 'inverse';
    const TYPE_TEALS = 'teals';

    public $type = false;
    public $faIcon = false;
    public $text = '';


    /**
     * @return string|void
     */
    public function run()
    {
        if (!$this->type) {
            $this->type = self::TYPE_WARNING;
        }

        $content = !empty($this->faIcon)
            ? '<i class="fa ' . $this->faIcon . '"></i> ' . $this->text
            : $this->text;

        return $this->getBadge($content, $this->type);
    }

    /**
     * @param string $content
     * @param string $type
     */
    protected function getBadge(string $content, string $type)
    {
        return  Html::tag('span', $content, ['class' => 'badge badge-' . $type]);
    }

    /**
     * @param string $content
     * @param string $type
     * @param string $url
     * @return string
     */
    protected function getBadgeLink(string $content, string $type, string $url)
    {
        $badge = Html::tag('span', $content, ['class' => 'badge badge-' . $type]);

        $link = Html::a($badge, $url);

        return $link;
    }
}
