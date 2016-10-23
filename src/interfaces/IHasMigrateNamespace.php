<?php
/**
 * @author    Dmytro Karpovych
 * @copyright 2016 NRE
 */


namespace nullref\core\interfaces;


interface IHasMigrateNamespace
{
    public function getMigrationNamespaces($defaults);
}