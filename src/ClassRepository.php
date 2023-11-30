<?php

declare(strict_types=1);

namespace mjfklib\Container;

class ClassRepository
{
    /**
     * @template T of object
     * @param string[] $classes
     * @param class-string<T> $filterClassName
     * @return array<string,\ReflectionClass<T>>
     */
    public static function getReflectionClasses(
        array $classes,
        string $filterClassName
    ): array {
        $refClasses = [];

        $filterClass = new \ReflectionClass($filterClassName);

        if ($filterClass->isInterface()) {
            foreach ($classes as $className) {
                if (!class_exists($className)) {
                    continue;
                }

                $refClass = new \ReflectionClass($className);
                if ($refClass->isInstantiable() && $refClass->implementsInterface($filterClass)) {
                    /** @var \ReflectionClass<T> $refClass */
                    $refClasses[$refClass->getName()] = $refClass;
                }
            }
        } else {
            foreach ($classes as $className) {
                if (!class_exists($className)) {
                    continue;
                }

                $refClass = new \ReflectionClass($className);
                if ($refClass->isInstantiable() && $refClass->isSubclassOf($filterClass)) {
                    /** @var \ReflectionClass<T> $refClass */
                    $refClasses[$refClass->getName()] = $refClass;
                }
            }
        }

        return $refClasses;
    }


    /**
     * @var array<string,\ReflectionClass<object>>
     */
    public readonly array $classes;


    /**
     * @param string $appDir
     * @param string $appNamespace
     */
    public function __construct(
        string $appDir,
        string $appNamespace
    ) {
        $this->classes = $this->findClasses($appDir . '/src', $appNamespace);
    }


    /**
     * @param string $appNamespace
     * @param string $srcDir
     * @return array<string,\ReflectionClass<object>>
     */
    protected function findClasses(
        string $srcDir,
        string $appNamespace
    ): array {
        if ($appNamespace === '') {
            throw new \RuntimeException("Missing application namespace");
        }
        if (!is_dir($srcDir)) {
            throw new \RuntimeException("Not a directory: {$srcDir}");
        }

        $classes = [];
        $files = new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($srcDir)
            ),
            '/^.+\.php$/i',
            \RecursiveRegexIterator::GET_MATCH
        );

        foreach ($files as $file) {
            try {
                /** @var string[] $file */
                $filePath = array_shift($file) ?? '';
                /** @var class-string<object> $className */
                $className = $appNamespace . str_replace('/', '\\', substr($filePath, strlen($srcDir), -4));
                $refClass = new \ReflectionClass($className);
                $classes[$refClass->getName()] = $refClass;
            } catch (\ReflectionException) {
            }
        }

        return $classes;
    }


    /**
     * Returns instance of \ReflectionClass for the given file path
     *
     * @param string $appNamespace
     * @param string $srcDir
     * @param string $filePath
     * @return \ReflectionClass<object>|null
     */
    protected function getReflectionClass(
        string $appNamespace,
        string $srcDir,
        string $filePath
    ): \ReflectionClass|null {
        try {
            /** @var class-string<object> $className */
            $className = $appNamespace . str_replace('/', '\\', substr($filePath, strlen($srcDir), -4));
            return new \ReflectionClass($className);
        } catch (\ReflectionException) {
            return null;
        }
    }


    /**
     * @param \ReflectionClass<object> $refClass
     * @param class-string<T> $name
     * @return T|null
     * @template T
     */
    public static function getAttribute(
        \ReflectionClass $refClass,
        string $name
    ): mixed {
        $refAttrs = $refClass->getAttributes(
            $name,
            \ReflectionAttribute::IS_INSTANCEOF
        );
        return (count($refAttrs) > 0) ? $refAttrs[0]->newInstance() : null;
    }


    /**
     * @template T of object
     * @param class-string<T> $filterClassName
     * @return array<string,\ReflectionClass<T>>
     */
    public function getClasses(string $filterClassName): array
    {
        $filterClass = new \ReflectionClass($filterClassName);

        /** @var array<string,\ReflectionClass<T>> */
        return $filterClass->isInterface()
            ? array_filter(
                $this->classes,
                fn (\ReflectionClass $class) => $class->isInstantiable()
                    && $class->implementsInterface($filterClass)
            )
            : array_filter(
                $this->classes,
                fn (\ReflectionClass $class) => $class->isInstantiable()
                    && $class->isSubclassOf($filterClass)
            );
    }
}
