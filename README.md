# Yii2-core

[![Latest Stable Version](https://poser.pugx.org/nullref/yii2-core/v/stable)](https://packagist.org/packages/nullref/yii2-core) [![Total Downloads](https://poser.pugx.org/nullref/yii2-core/downloads)](https://packagist.org/packages/nullref/yii2-core) [![Latest Unstable Version](https://poser.pugx.org/nullref/yii2-core/v/unstable)](https://packagist.org/packages/nullref/yii2-core) [![License](https://poser.pugx.org/nullref/yii2-core/license)](https://packagist.org/packages/nullref/yii2-core)

Module for administration

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist nullref/yii2-core "*"
```

or add

```
"nullref/yii2-core": "*"
```

to the require section of your `composer.json` file.

Core module for fast web development based on Yii2.
This package contains:

- components:
    * EntityManager - component for simple managing of entities (models)

- interfaces:
    * IAdminModule - interface for modules which can be used by [nullref\yii2-admin](https://github.com/NullRefExcep/yii2-admin)
    * IRoleContainer - interface which provide roles for RBAC
    * IEntityManager - interface EntityManager
    * IEntityManageble - interface for classes which contain EntityManager