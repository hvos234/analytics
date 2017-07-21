<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Webpage]].
 *
 * @see Webpage
 */
class WebpageQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Webpage[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Webpage|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
