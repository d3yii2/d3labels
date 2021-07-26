<?php

namespace d3yii2\d3labels\components;

/**
 * Class D3LabelAccess
 * @package d3yii2\d3labels\components
 */
class D3LabelAccess
{
    public const ACTION_CREATE = 'd3labelscreate';
    public const ACTION_ATTACH = 'd3labelsattach';
    public const ACTION_DELETE = 'd3labelsremove';
    public const ACTION_DEFINITION_DELETE = 'd3labelsdefinitionremove';
    public const ACTION_DEFINITION_EDIT = 'd3labelsdefinitionedit';

    /**
     * @param string $modelClass
     * @return array
     */
    public static function getControllerActions(string $modelClass): array
    {
        $controlerActions = [];

        $actions = self::getActions();

        foreach ($actions as $key => $def) {
            $controlerActions[$key] = self::getAction($key, $modelClass);
        }

        return $controlerActions;
    }

    /**
     * @return array
     */
    public static function getControllerBehaviors(): array
    {
        return [
            self::ACTION_CREATE,
            self::ACTION_ATTACH,
            self::ACTION_DELETE,
            self::ACTION_DEFINITION_DELETE,
            self::ACTION_DEFINITION_EDIT
        ];
    }

    /**
     * @param string $key
     * @param string $modelClass
     * @return array
     */
    public static function getAction(string $key, string $modelClass): array
    {
        $actions = self::getActions();
        $action = $actions[$key];
        $action['modelName'] = $modelClass;

        return $action;
    }

    /**
     * @return array
     */
    private static function getActions(): array
    {
        return [
            self::ACTION_CREATE => [
                'class' => CreateAction::class,
            ],
            self::ACTION_ATTACH => [
                'class' => AttachAction::class,
            ],
            self::ACTION_DELETE => [
                'class' => DeleteAction::class,
            ],
            self::ACTION_DEFINITION_DELETE => [
                'class' => DefinitionDeleteAction::class,
                'sysLabelsIdList' => [2]
            ],
            self::ACTION_DEFINITION_EDIT => [
                'class' => DefintionEditAction::class,
                'sysLabelsIdList' => [2]
            ]
        ];
    }
}