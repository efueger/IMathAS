<?php


namespace app\models;


use app\components\AppUtility;
use app\models\_base\BaseImasQimages;
use yii\db\Query;

class QImages extends BaseImasQimages {

    public static function getById($id){
        return QImages::findOne(['id' => $id]);
    }
    public static function getByQuestionSetId($id){
        return QImages::findAll(['qsetid' => $id]);
    }

    public static function getByFileName($filename){
        return QImages::findAll(['filename' => $filename]);
    }

    public static function deleteById($id){
        $data = QImages::getById($id);
        if($data){
            $data->delete();
        }
    }

    public static function setVariableAndText($id, $var, $alt){
        $data = QImages::getById($id);
        if($data){
            $data->var = $var;
            $data->alttext = $alt;
            $data->save();
        }
    }

    public function createQImages($qSetId,$params){
        $this->qsetid = $qSetId;
        $this->var = $params['var'];
        $this->filename = $params['filename'];
        $this->alttext = $params['alttext'];AppUtility::dump($this);
        $this->save();
    }

}