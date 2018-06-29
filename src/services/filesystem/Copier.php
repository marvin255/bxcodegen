<?php

namespace marvin255\bxcodegen\services\filesystem;

use InvalidArgumentException;

/**
 * Объект, который рекурсивно копирует содержимое одной папки в другую.
 */
class Copier implements CopierInterface
{
    /**
     * @inheritdoc
     */
    public function copyDir(DirectoryInterface $source, DirectoryInterface $destination)
    {
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

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function copyFile(FileInterface $source, FileInterface $destination)
    {
        $res = file_put_contents(
            $destination->getPathname(),
            file_get_contents($source->getPathname())
        );

        if ($res === false) {
            throw new InvalidArgumentException(
                "Can't copy " . $source->getPathname() . ' to ' . $destination->getPathname()
            );
        }

        return $this;
    }
}
