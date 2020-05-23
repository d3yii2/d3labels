<?php

namespace d3yii2\d3labels\dictionaries;

use d3system\dictionaries\SysModelsDictionary;
use d3yii2\d3labels\models\D3lDefinition;
use Yii;


class D3lDefinitionDictionary{

    private const CACHE_KEY_LIST = 'D3lLabelDictionaryList';





    public static function getList(int $sysCompanyId, string $modelClass): array
    {
        $modelId = SysModelsDictionary::getIdByClassName($modelClass);
        $fullList = self::getFullSelect();
        $list = [];
        foreach($fullList as $row){
            if($row['sys_company_id'] && (int)$row['sys_company_id'] !== $sysCompanyId){
                continue;
            }
            if((int)$row['model_id'] !== $modelId){
                continue;
            }
            $list[$row['id']] = $row['label'];
        }

        return $list;
    }



    public static function findByCodeModel(string $code, string $modelClass, int $sysCompanyId = 0)
    {
        $fullList = self::getFullSelect();
        $list = [];
        $modelId = SysModelsDictionary::getIdByClassName($modelClass);
        foreach($fullList as $row){
            if($row['code'] !== $code){
                continue;
            }
            if((int)$row['model_id'] !== $modelId){
                continue;
            }
            if($sysCompanyId && $row['sys_company_id'] && (int)$row['sys_company_id'] !== $sysCompanyId){
                continue;
            }

            return (int)$row['id'];
        }

        return false;
    }

    public static function rowlList(string $modelClass, int $sysCompanyId = 0)
    {
        $fullList = self::getFullSelect();
        $list = [];
        $modelId = SysModelsDictionary::getIdByClassName($modelClass);
        foreach($fullList as $row){
            if((int)$row['model_id'] !== $modelId){
                continue;
            }
            if($sysCompanyId && $row['sys_company_id'] && (int)$row['sys_company_id'] !== $sysCompanyId){
                continue;
            }
            $list[] = $row;

        }

        return $list;
    }

    public static function clearCache(): void
    {
        Yii::$app->cache->delete(self::CACHE_KEY_LIST);
    }

    /**
     * @return bool|false|mixed
     */
    public static function getFullSelect()
    {
        $fullList = Yii::$app->cache->getOrSet(
            self::CACHE_KEY_LIST,
            static function () {
                return
                    D3lDefinition::find()
                        ->orderBy(['label' => SORT_ASC])
                        ->asArray()
                        ->all();
            }
        );
        return $fullList;
    }
}
