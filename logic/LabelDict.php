<?php


namespace d3yii2\d3labels\logic;

use d3yii2\d3labels\models\D3lDefinition;
use yii\base\Exception;


class LabelDict
{


    /** @var D3lDefinition[] */
    private static $list;


    /**
     * @param string $className
     * @param string $code
     * @return D3lDefinition
     * @throws Exception
     */
    public static function getDefinitionByCode(string $className, string $code): D3lDefinition
    {
        if (!isset(self::$list[$className])) {
            self::loadList($className);
        }

        foreach (self::$list[$className] as $definition) {
            if ($definition->code === $code) {
                return $definition;
            }
        }
        throw new Exception('Incorrect label code: ' . $code . ' for model ' . $className);
    }

    public static function loadList(string $className): void
    {
        $def = new D3Definition($className);
        self::$list[$className] = $def->getAllByModel();
    }


}
