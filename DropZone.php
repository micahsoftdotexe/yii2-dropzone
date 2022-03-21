<?php

namespace micahsoft\dropzone;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use app\components\DropZoneAsset;

class DropZone extends Widget
{
    public $options = [];
    public $autoDiscover = false;
    public $model;
    public $htmlOptions = [];
    public $events = [];
    public $url;
    public $id;
    public $message = 'Drag and drop files here';
    private $dropzoneVariable;
    public function init()
    {
        parent::init();
        DropZoneAsset::register($this->getView());

    }

    protected function hasModel()
    {
        return $this->model instanceof \yii\base\Model
        && $this->url !== null;
    }

    public function run()
    {
        if (!$this->hasModel()) {
            throw new \yii\base\InvalidConfigException('DropZone::$model and DropZone::$url must be set.');
        }
        $this->id = $this->id ?: 'dropzone'.bin2hex(random_bytes(3));
        $this->url = $this->url?:Yii::$app->request->baseUrl.'/dropzone/upload';
        $this->dropzoneVariable = 'dropzone'.bin2hex(random_bytes(3));
        $this->renderWidgetHtml();
        $this->renderWidgetJavascript();
    }

    private function renderWidgetHtml()
    {
        echo Html::beginForm($this->url, 'post', array_merge(['id' => $this->id, 'class' => 'dropzone', 'enctype' => 'multipart/form-data'], $this->htmlOptions));
        echo Html::tag('div', Html::tag('span', $this->message), ['id' => $this->id.'-message', 'class' => 'dz-message'] );
        echo Html::endForm();
    }

    private function renderWidgetJavascript()
    {
        $view = $this->getView();
        $js = 'Dropzone.autoDiscover = ' . (($this->autoDiscover) ? "true" : "false") . '; var ' . $this->dropzoneVariable . ' = new Dropzone("#' . $this->id . '", ' . Json::encode($this->options). ');';
        foreach ($this->events as $event => $function) {
            $js .= $this->dropzoneVariable . '.on("' . $event . '", ' . new \yii\web\JsExpression($function) . ');';
        }
        return $view->registerJs($js);
    }
}
