<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Website]].
 *
 * @see Website
 */
class WebsiteQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Website[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Website|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
