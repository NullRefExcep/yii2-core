<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2018 NRE
 */


namespace nullref\core\assets;

use yii\web\AssetBundle;

/**
 * Class ToolsAsset
 * @package nullref\core\assets
 */
class ToolsAsset extends AssetBundle
{
    public $sourcePath = '@nullref/core/assets';
    public $js = [
        'js/tools.js',
    ];
}