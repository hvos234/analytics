<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Webpage */

$this->title = Yii::t('app', 'Create Webpage');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Webpages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="webpage-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
