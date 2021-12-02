# webman-annotation



前言:
-------

[webman](https://www.workerman.net/doc/webman/) 注解插件。已实现功能

- 路由注解



安装
------------

```bash
composer require cshaptx4869/webman-annotation
```



## 配置

`config/route.php ` 文件添加如下代码：

```php
<?php

use Fairy\AnnotationScanner;

$scanner = new AnnotationScanner();
$scanner->registerRoute();
```



## 示例

```php
<?php
namespace app\controller;

use Fairy\Annotation\Route;
use support\Request;

class Index
{
    /**
     * 通过get或者post方式请求 /json 即可访问到当前控制器方法
     * @Route(url="/json", method="GET")
     */
    public function json(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
```



## IDE 注解插件支持

一些ide已经提供了对注释的支持，推荐安装，以便提供注解语法提示

- Eclipse via the [Symfony2 Plugin](http://symfony.dubture.com/)
- PHPStorm via the [PHP Annotations Plugin](http://plugins.jetbrains.com/plugin/7320) or the [Symfony2 Plugin](http://plugins.jetbrains.com/plugin/7219)