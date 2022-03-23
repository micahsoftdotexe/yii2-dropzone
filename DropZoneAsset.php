<?php
namespace micahsoftdotexe\dropzone;

use yii\web\AssetBundle;

class DropZoneAsset extends AssetBundle
{
    public $sourcePath = '@vendor/enyo/dropzone/dist';
    public $css = [
        'min/dropzone.min.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
    ];
    public $js = [
        'min/dropzone.min.js',
    ];
}
