<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Website */

$this->title = Yii::t('app', 'Create Website');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Websites'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="website-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
