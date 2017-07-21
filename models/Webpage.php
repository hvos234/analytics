<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%webpage}}".
 *
 * @property integer $id
 * @property string $url
 * @property string $html
 */
class Webpage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%webpage}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url'], 'required'],
            [['html'], 'string'],
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
            'html' => Yii::t('app', 'Html'),
        ];
    }

    /**
     * @inheritdoc
     * @return WebpageQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new WebpageQuery(get_called_class());
    }
        
    public function beforeSave($insert)
    {        
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $html = $this->getHtml($this->url);
        if(!$html){
            return false;
        }
        
        $this->html = $html;
        
        return true;
    }
    
    
    public function getHtml($url){        
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $html = curl_exec($ch);
                
        // Statuscode 0 means the connection was closed (gracefully) before any output was returned
        if(0 === curl_errno($ch)){
            $this->addError('url', 'Url not found !, You probily need to use http:// or https:// !');
            curl_close($ch);
            return false;
        }
        
        if(curl_errno($ch)){
            $this->addError('url', curl_error($ch));
            curl_close($ch);
            return false;
        }
        
        if(curl_error($ch)){
            $this->addError('url', curl_error($ch));
            curl_close($ch);
            return false;
        }
        
        curl_close($ch);
        
        return $html;
    }
}
