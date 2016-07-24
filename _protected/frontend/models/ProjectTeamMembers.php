<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "project_team_members".
 *
 * @property integer $project_team_member_id
 * @property integer $project_id
 * @property string $member_name
 * @property string $member_email
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Project $project
 */
class ProjectTeamMembers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_team_members';
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
            [['project_id', 'member_name', 'member_email'], 'safe'],

            [['project_id', 'member_name', 'member_email'], 'required'],
            [['project_id', 'created_at', 'updated_at'], 'integer'],
            [['member_name'], 'string', 'max' => 20],
            [['member_email'], 'string', 'max' => 320],
            [['member_email'],'email'],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'project_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'project_team_member_id' => 'Project Team Member ID',
            'project_id' => 'Project ID',
            'member_name' => 'Member Name',
            'member_email' => 'Member Email',
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
