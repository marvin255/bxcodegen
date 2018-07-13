<?php

namespace marvin255\bxcodegen\service\filesystem;

use InvalidArgumentException;

/**
 * Объект, который рекурсивно копирует содержимое одной папки в другую.
 */
class Copier implements CopierInterface
{
    /**
     * @var array
     */
    protected $transformers = [];

    /**
     * @inheritdoc
     */
    public function copyDir(DirectoryInterface $source, DirectoryInterface $destination)
    {
        if (!$this->transform($source, $destination)) {
            $destination->create();
            foreach ($source as $child) {
                if ($child instanceof DirectoryInterface) {
                    $destinationChild = $destination->createChildDirectory($child->getFoldername());
                    $this->copyDir($child, $destinationChild);
                } else {
                    $destinationChild = $destination->createChildFile($child->getBasename());
                    $this->copyFile($child, $destinationChild);
                }
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function copyFile(FileInterface $source, FileInterface $destination)
    {
        if (!$this->transform($source, $destination)) {
            $res = copy($source->getPathname(), $destination->getPathname());
            if ($res === false) {
                throw new InvalidArgumentException(
                    "Can't copy " . $source->getPathname() . ' to ' . $destination->getPathname()
                );
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function addTransformer($transformer)
    {
        if (!is_callable($transformer)) {
            throw new InvalidArgumentException(
                'Transformer item must be a callable instance'
            );
        }

        $this->transformers[] = $transformer;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function clearTransformers()
    {
        $this->transformers = [];

        return $this;
    }

    /**
     * Запускает события для того, чтобы изменить содержимое файлов при копировании
     * или отменить копирование какого-нибудь каталога совсем.
     *
     * Если функция вернет правду, то, значит, сработало одно из событий и
     * стандартное копирование будет отменено.
     *
     * @param mixed $from
     * @param mixed $to
     *
     * @return bool
     */
    protected function transform($from, $to)
    {
        $return = false;

        foreach ($this->transformers as $transformer) {
            $res = call_user_func_array($transformer, [$from, $to]);
            if ($res === true) {
                $return = true;
                break;
            }
        }

        return $return;
    }
}
