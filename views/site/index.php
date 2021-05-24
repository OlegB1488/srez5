<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'My Yii Application';
?>
<div class="site-index">
   <? if (!Yii::$app->user->isGuest) : ?>
    <p>
        <?= Html::a('Create Request', ['/front/create'], ['class' => 'btn btn-success']) ?>
    </p>
    <? endif; ?>


    <? foreach ($request as $item) : ?>
        <?= $item->name?>
        <?= $item->created_at?>
        <?= $item->category->name?>
        <?=\yii\helpers\Html::img($item->before_img,['width'=>100])?>
    <? endforeach; ?>

</div>
