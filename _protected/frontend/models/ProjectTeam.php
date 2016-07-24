<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "project_team".
 *
 * @property integer $project_team_id
 * @property integer $project_id
 * @property string $member_1
 * @property string $member_2
 * @property string $member_3
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Project $project
 */
class ProjectTeam extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_team';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_id', 'member_1', 'member_2', 'member_3', 'created_at', 'updated_at'], 'required'],
            [['project_id', 'created_at', 'updated_at'], 'integer'],
            [['member_1', 'member_2', 'member_3'], 'string', 'max' => 20],
            [['project_id'], 'unique'],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'project_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'project_team_id' => 'Project Team ID',
            'project_id' => 'Project ID',
            'member_1' => 'Member 1',
            'member_2' => 'Member 2',
            'member_3' => 'Member 3',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['project_id' => 'project_id']);
    }
}
