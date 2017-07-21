<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%website}}".
 *
 * @property integer $id
 * @property string $url
 */
class Website extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%website}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url'], 'required'],
            [['url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'url' => Yii::t('app', 'Url'),
        ];
    }

    /**
     * @inheritdoc
     * @return WebsiteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new WebsiteQuery(get_called_class());
    }
}
