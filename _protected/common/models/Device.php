<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "device".
 *
 * @property integer $device_id
 * @property string $device_name
 * @property integer $created_at
 * @property integer $updated_at
 */
class Device extends \yii\db\ActiveRecord
{

    public $confirm_password ;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'device';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['device_name'],
                'required'
            ],
            [['created_at', 'updated_at'], 'integer'],
            [['device_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'device_id' => 'Device ID',
            'device_name' => 'Device NU Nam',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
