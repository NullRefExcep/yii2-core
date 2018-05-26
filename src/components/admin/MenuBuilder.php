<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2018 NRE
 */

namespace nullref\core\components;


use nullref\core\interfaces\IMenuBuilder;

abstract class MenuBuilder implements IMenuBuilder
{
    /**
     * Filter menu items by specified roles
     *
     * @param $menu
     * @param $role
     * @param string $paramName
     * @return array
     */
    public function filterByRole($menu, $role, $paramName = 'roles')
    {
        if ($role === null) {
            return [];
        }
        $result = [];
        foreach ($menu as $key => $item) {
            if (isset($item[$paramName])) {
                if (is_array($role) && count(array_intersect($role, $item[$paramName]))
                    || in_array($role, $item[$paramName])) {
                    if (isset($item['items'])) {
                        $result[$key] = $item;
                        $result[$key]['items'] = $this->filterByRole($result[$key]['items'], $role, $paramName);
                    } else {
                        $result[$key] = $item;
                    }
                }
            } else {
                $result[$key] = $item;
            }
        }
        return $result;
    }
}
