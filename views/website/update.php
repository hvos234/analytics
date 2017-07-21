<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Website */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Website',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Websites'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="website-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
