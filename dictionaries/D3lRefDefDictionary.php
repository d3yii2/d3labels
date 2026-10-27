<?php

namespace d3yii2\d3labels\dictionaries;

use d3yii2\d3labels\models\D3lRefDef;
use Exception;
use Yii;
use yii\helpers\ArrayHelper;

class D3lRefDefDictionary
{

    private const string CACHE_KEY_LIST = 'D3lRefDefDictionaryList';
    /**
     * @var array|false|mixed|string
     */
    private static ?array $list = null;


    /**
     * @return string[]
     */
    public static function getList(): array
    {
        if (self::$list !== null) {
            return self::$list;
        }
        return self::$list = Yii::$app->cache->getOrSet(
            self::CACHE_KEY_LIST,
            static function () {
                return ArrayHelper::map(
                    D3lRefDef::find()
                        ->select([
                            'id' => '`d3l_ref_def`.`id`',
                            'name' => '`d3l_ref_def`.`code`',
                        ])
                        ->orderBy([
                            '`d3l_ref_def`.`code`' => SORT_ASC,
                        ])
                        ->asArray()
                        ->all(),
                    'id',
                    'name'
                );
            },
            60 * 60
        );
    }

    /**
     * @throws Exception
     */
    public static function getIdByCode(string $code): ?int
    {
        return array_flip(self::getList())[$code]??null;
    }


    /**
     * get label
     * @param int $id
     * @return string|null
     */
    public static function getLabel(int $id): ?string
    {
        return self::getList()[$id] ?? null;
    }

    public static function clearCache(): void
    {
        Yii::$app->cache->delete(self::CACHE_KEY_LIST);
        self::$list = null;
    }
}
