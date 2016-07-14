<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "books".
 *
 * @property integer $book_id
 * @property string $name
 * @property integer $author
 * @property string $description
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $author0
 */
class Books extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'books';
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
            [['name', 'author', 'description'], 'required'],
            [['author', 'created_at', 'updated_at'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 20],

            [['author'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author' => 'id']],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'book_id' => 'Book ID',
            'name' => 'Name',
            'author' => 'Author',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor0()
    {
        return $this->hasOne(User::className(), ['id' => 'author']);
    }
}
