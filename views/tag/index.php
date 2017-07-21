<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\TagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\models\Webpage;
use app\models\Tag;

$this->title = Yii::t('app', 'Tags');
$this->params['breadcrumbs'][] = $this->title;

echo ('') . '<br/>' . PHP_EOL;
$html = <<<HTML
<div id="thirth">
    <div class="container">
        <h2>Hello World !</h2>
        <div class="large-6 small-12 columns \"">
            <img class="logo" src="/sites/all/themes/zurb-foundation/logo.png" />
        </div>
    </div>
</div>
<div id="five"><div id="fourt"><span class="next">hello</span>boe!</div></div>
HTML;

//echo ('$html: ' . htmlentities($html)) . '<br/>' . PHP_EOL;
echo ('$html: ' . $html) . '<br/>' . PHP_EOL;


$modelTag = new Tag();
$tags = $modelTag->getTags($html);
$tags = $modelTag->getHierarchy($tags);

echo('<pre>');
print_r($tags);
echo('</pre>');
?>
<div class="tag-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Tag'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'webpage_id',
            'name',
            'html:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>


