<?php

/* @var $this yii\web\View
 * @var $a frontend\controllers\SiteController
 * @var $b frontend\controllers\SiteController
 */

use yii\helpers\Html;

$this->title = 'Rólunk';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>ASDadogasd</p>

    <?= Html::encode($a) ?>
    <br>

    <?php foreach ($b as $c): ?>
        <p style="font-weight: bold"><?= $c ?></p>
    <?php endforeach; ?>

    <br>

    <code><?= __FILE__ ?></code>
</div>
