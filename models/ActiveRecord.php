<?php

namespace app\models;

use yii\db\ActiveRecord as BaseActiveRecord;

class ActiveRecord extends BaseActiveRecord
{

    public function attributeLabels()
    {
        return Model::attributeLabelsList();
    }

}
