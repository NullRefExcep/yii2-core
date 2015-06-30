<?php
namespace nullref\core\widgets;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use Yii;

/**
 * @author    Dmytro Karpovych
 * @copyright 2015 NRE
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Menu extends \yii\widgets\Menu
{
    public $encodeLabels = false;
    public $activateParents = true;
    public $options = ['class' => 'nav navbar-nav side-nav'];
    public $labelTemplate = '<a href="javascript:void(0);" data-toggle="collapse" data-target="{target}"> {label} <i class="fa fa-fw fa-caret-down"></i></a>';
    public $submenuTemplate = "\n<ul id=\"{id}\" class=\"collapse {in}\">\n{items}\n</ul>\n";

    protected $_counter = 0;

    protected function renderItem($item)
    {

        if (isset($item['url'])) {
            $template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);

            return strtr($template, [
                '{url}' => Html::encode(Url::to($item['url'])),
                '{label}' => $item['label'],
            ]);
        } else {
            $template = ArrayHelper::getValue($item, 'template', $this->labelTemplate);
            $result = strtr($template, [
                '{label}' => $item['label'],
            ]);
            if (!empty($item['items'])) {
                $target = 'submenu-' . ($this->_counter++);
                $result = strtr($result, [
                    '{target}' => '#' . $target,
                ]);
                $submenuTemplate = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);
                $result .= strtr($submenuTemplate, [
                    '{items}' => $this->renderItems($item['items']),
                    '{id}' => $target,
                    '{in}' => $item['active'] ? 'in' : '',
                ]);
            }
            return $result;
        }
    }

    protected function renderItems($items)
    {
        $n = count($items);
        $lines = [];
        foreach ($items as $i => $item) {
            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
            $tag = ArrayHelper::remove($options, 'tag', 'li');
            $class = [];
            if ($item['active']) {
                $class[] = $this->activeCssClass;
            }
            if ($i === 0 && $this->firstItemCssClass !== null) {
                $class[] = $this->firstItemCssClass;
            }
            if ($i === $n - 1 && $this->lastItemCssClass !== null) {
                $class[] = $this->lastItemCssClass;
            }
            if (!empty($class)) {
                if (empty($options['class'])) {
                    $options['class'] = implode(' ', $class);
                } else {
                    $options['class'] .= ' ' . implode(' ', $class);
                }
            }

            $menu = $this->renderItem($item);

            if ($tag === false) {
                $lines[] = $menu;
            } else {
                $lines[] = Html::tag($tag, $menu, $options);
            }
        }

        return implode("\n", $lines);
    }

    protected function isItemActive($item)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = $item['url'][0];
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            if (ltrim($route, '/') !== $this->route) {
                return false || (strrpos($this->route, ltrim($route, '/'), -strlen($this->route)) !== false);
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                $params = $item['url'];
                unset($params[0]);
                foreach ($params as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }

} 