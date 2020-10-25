<?php
namespace kn\images;

use yii\grid\DataColumn;
use yii\helpers\Json;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class ViewImages extends DataColumn
{
    const PLUGIN_NAME = 'viewer';
    public $clientOptions = [];
    public $domain;

    protected $className;
    private   $_hashVar;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->className = 'viewer-' . $this->attribute;
        $this->hashPluginOptions();
        $this->registerPlugin();
    }

    protected function hashPluginOptions()
    {
        $encOptions     = empty($this->clientOptions) ? '{}' : Json::encode($this->clientOptions);
        $this->_hashVar = self::PLUGIN_NAME . '_' . hash('crc32', $encOptions);
        $this->grid->getView()->registerJs("var {$this->_hashVar} = {$encOptions};\n", View::POS_HEAD);
    }

    public function renderDataCellContent($model, $key, $index)
    {

        $values = $this->value === null ? (array)$model[$this->attribute] : call_user_func($this->value, $model);
        $html   = '<div class="' . $this->className . '">';
        foreach ($values as $key => $v) {
            if ($v) {
                $options = ArrayHelper::merge(['alt' => ' ', 'width' => '70'], $this->options);
                if ($key > 0) {
                    $options['style'] = ['display' => 'none'];
                }
                $html .= '<span>' . Html::img($v ?? '', $options) . '</span>';

            }
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * Registers js and css
     */
    public function registerPlugin()
    {
        $view = $this->grid->getView();
        ViewImagesAsset::register($view);
        $pluginName = self::PLUGIN_NAME;
        $js         = "$(\".viewer-$this->attribute\").$pluginName($this->_hashVar);";
        $className  = $this->className;
        $css        = <<<CSS
        .$className img{
cursor: zoom-in;
    }
CSS;
        $view->registerCss($css);
        $view->registerJs($js, View::POS_READY);
    }
}