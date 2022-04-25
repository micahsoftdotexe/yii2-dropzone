<?php

namespace micahsoftdotexe\dropzone;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use micahsoftdotexe\dropzone\DropZoneAsset;

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

    protected function hasUrl()
    {
        return ($this->url !== null);
    }

    public function run()
    {
        if (!$this->hasUrl()) {
            throw new \yii\base\InvalidConfigException('DropZone::$url must be set.');
        }
        if ($id) {
            $this->id = $id;
        }
        $this->id = $this->id ?: 'dropzone'.bin2hex(random_bytes(3));
        if ($url) {
            $this->url = $url;
        }
        if (!$this->hasUrl()) {
            throw new \yii\base\InvalidConfigException('DropZone::$url must be set.');
        }
        $this->url = $this->url?:Yii::$app->request->baseUrl.'/dropzone/upload';
        //Cannot let the dropzone variable be the id because id can have dashes and other weird characters
        $this->dropzoneVariable = \yii\helpers\Inflector::variablize($this->id);
        $this->renderWidgetHtml();
        $this->renderWidgetJavascript();
    }

    private function renderWidgetHtml()
    {
        echo Html::beginForm($this->url, 'post', array_merge(['id' => $this->id, 'enctype' => 'multipart/form-data'], $this->htmlOptions));
        echo Html::tag('div', Html::tag('span', $this->message), ['id' => $this->id.'-message', 'class' => 'dz-message'] );
        echo Html::endForm();
    }

    private function renderWidgetJavascript()
    {
        $view = $this->getView();
        $js = 'Dropzone.autoDiscover = ' . (($this->autoDiscover) ? "true" : "false") . ';';
        //! The following line is important, it assigns the css class after autoDiscover is set

        $js .= '$("#' . $this->id . '").addClass("dropzone");';
        $js .= 'let ' . $this->dropzoneVariable . ' = new Dropzone("#' . $this->id . '", ' . Json::encode($this->options) . ');';

        foreach ($this->events as $event => $function) {
            $js .= $this->dropzoneVariable . '.on("' . $event . '", ' . new \yii\web\JsExpression($function) . ');';
        }
        return $view->registerJs($js);
    }
}
