<?php
namespace common\widgets\taginput;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

class TagsinputWidget extends \yii\widgets\InputWidget
{
    /**
     * The name of the jQuery plugin to use for this widget.
     */
    const PLUGIN_NAME = 'tagsinput';
    /**
     * @var array the JQuery plugin options for the bootstrap-tagsinput plugin.
     * @see http://timschlechter.github.io/bootstrap-tagsinput/examples/#options
     */
    public $clientOptions = [];
    /**
     * @var array the HTML attributes for the input tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];
    /**
     * @var string the hashed variable to store the pluginOptions
     */
    protected $_hashVar;
    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerClientScript();
        if ($this->hasModel()) {
            echo Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textInput($this->name, $this->value, $this->options);
        }
    }
    /**
     * Generates a hashed variable to store the plugin `clientOptions`. Helps in reusing the variable for similar
     * options passed for other widgets on the same page. The following special data attribute will also be
     * setup for the input widget, that can be accessed through javascript:
     *
     * - 'data-plugin-tagsinput' will store the hashed variable storing the plugin options.
     *
     * @param View $view the view instance
     */
    protected function hashPluginOptions($view)
    {
        $encOptions = empty($this->clientOptions) ? '{}' : Json::encode($this->clientOptions);
        $this->_hashVar = self::PLUGIN_NAME . '_' . hash('crc32', $encOptions);
        $this->options['data-plugin-' . self::PLUGIN_NAME] = $this->_hashVar;
        $view->registerJs("var {$this->_hashVar} = {$encOptions};\n", View::POS_HEAD);
    }
    /**
     * Registers the needed client script and options.
     */
    public function registerClientScript()
    {
        $js = '';
        $view = $this->getView();
        $this->hashPluginOptions($view);
        $id = $this->options['id'];
        $js .= '$("#' . $id . '").' . self::PLUGIN_NAME . "(" . $this->_hashVar . ");\n";
        TagsinputAsset::register($view);
        $view->registerJs($js);
    }
}
