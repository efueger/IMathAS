<?php
namespace app\models\forms;

use app\models\_base\BaseImasCourses;
use Yii;
use yii\base\Model;
class EnrollStudentsForm extends model{


    public $section;


private $_user = false;

/**
* @return array the validation rules.
*/
public function rules()
{
return   [[['section'],'string'] ,
];

}

public function attributeLabels()
{
return [ 'section' => 'Assign to Section (optional) :'

];
}


}