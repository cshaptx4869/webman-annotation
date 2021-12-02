<?php

namespace Fairy;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Webman\Route;

class AnnotationScanner
{
    /**
     * @var $annotationReader AnnotationReader
     */
    protected $annotationReader;

    /**
     * 注解读取白名单
     * @var array
     */
    protected $whitelist = [
        "author", "var", "after", "afterClass", "backupGlobals", "backupStaticAttributes", "before", "beforeClass", "codeCoverageIgnore*",
        "covers", "coversDefaultClass", "coversNothing", "dataProvider", "depends", "doesNotPerformAssertions",
        "expectedException", "expectedExceptionCode", "expectedExceptionMessage", "expectedExceptionMessageRegExp", "group",
        "large", "medium", "preserveGlobalState", "requires", "runTestsInSeparateProcesses", "runInSeparateProcess", "small",
        "test", "testdox", "testWith", "ticket", "uses"
    ];

    public function __construct($whitelist = [])
    {
        $this->whitelist = array_merge($this->whitelist, $whitelist);
        $this->init();
    }

    protected function init()
    {
        AnnotationRegistry::registerLoader('class_exists');
        foreach ($this->whitelist as $name) {
            AnnotationReader::addGlobalIgnoredName($name);
        }
        $this->annotationReader = new AnnotationReader();
    }

    /**
     * 注册路由
     * @param string $scanPath
     * @throws \ReflectionException
     */
    public function registerRoute($scanPath = null)
    {
        empty($scanPath) && $scanPath = app_path();
        $dirIterator = new \RecursiveDirectoryIterator($scanPath);
        $iterator = new \RecursiveIteratorIterator($dirIterator);
        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            // 忽略目录和非php文件
            if (is_dir($file) || $file->getExtension() != 'php') {
                continue;
            }

            $filePath = str_replace('\\', '/', $file->getPathname());
            // 文件路径里不带controller的文件忽略
            if (strpos($filePath, 'controller') === false) {
                continue;
            }

            // 获取控制器类名
            $className = str_replace('/', '\\', substr(substr($filePath, strlen(base_path())), 0, -4));
            if (!class_exists($className)) {
                echo "Class $className not found, skip route for it\n";
                continue;
            }

            // 通过反射找到这个类的所有共有方法作为action
            $class = new \ReflectionClass($className);
            $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                $action = $method->name;
                if (in_array($action, ['__construct', '__destruct'])) {
                    continue;
                }
                $methodAnnotation = $this->annotationReader->getMethodAnnotation($method, \Fairy\Annotation\Route::class);
                if ($methodAnnotation) {
                    $route = strpos($methodAnnotation->url, '/') !== 0 ? '/' . $methodAnnotation->url : $methodAnnotation->url;
                    $httpMethod = array_map('strtoupper', explode(',', $methodAnnotation->method));
                    Route::add($httpMethod, $route, [$className, $action]);
                }
            }
        }
    }
}
