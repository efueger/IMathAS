<?php

namespace app\models;
use app\components\AppConstant;
use app\models\_base\BaseImasGroups;
use Yii;

use app\components\AppUtility;
use yii\db\Query;

class Groups extends BaseImasGroups
{

 public static function getIdAndName() {
     $user = Groups::find()->orderBy('name')->all();
     return $user;
 }
    public static function getByName($gpName)
    {
        $group = Groups::find()->where(['name' => $gpName])->one();
        return $group;
    }

    public static function getById($id)
    {
        $group = Groups::find()->where(['id' => $id])->one();
        return $group;
    }

    public function insertNewGroup($gpName)
    {
        $this->name = $gpName;
        $this->save();
    }

    public static function updateGroup($params)
    {
        $group = Groups::find()->where(['id' => $params['id']])->one();
        $group->name = $params['gpname'];
        $group->save();
    }
    public static function deleteById($id)
    {
        $group = Groups::find()->where(['id' => $id])->one();
         if($group)
         {
             $group->delete();
         }
    }

    public function insertNewGroupForUtilities($gpName)
    {
        $this->name = $gpName;
        $this->save();
        return $this->id;
    }

    public static function getIdNameByName()
    {
        $query = "SELECT id,name FROM imas_groups ORDER BY name";
        return Yii::$app->db->createCommand($query)->queryAll();
    }

    public static function getAllIdName()
    {
        $query = new Query();
        $query->select(['id', 'name'])
            ->from('imas_groups');
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }
}

