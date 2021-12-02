<?php

namespace Fairy\Annotation;

/**
 * 注解路由
 * @Annotation
 * @Target("METHOD")
 */
final class Route
{
    /**
     * 路由地址
     * @Required
     * @var string
     */
    public $url;

    /**
     * 请求方法
     * @var string
     */
    public $method = "GET";
}
