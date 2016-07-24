<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "project".
 *
 * @property integer $project_id
 * @property string $project_title
 * @property integer $team_members
 * @property integer $created_at
 * @property integer $updated_at
 */
class Project extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_title', 'team_members'], 'required'],
            [['team_members'], 'integer'],
            [['project_title'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'project_id' => 'Project ID',
            'project_title' => 'Project Title',
            'team_members' => 'Team Members',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
