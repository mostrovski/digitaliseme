<?php

namespace Digitaliseme\Core\Storage;

use Digitaliseme\Core\Exceptions\FileNotFoundException;
use SplFileInfo;

final class File
{
    private string $path = '';
    private string $originalName = '';
    private ?SplFileInfo $info = null;
    private bool $isUploaded = false;

    private function __construct() {}

    /**
     * @throws FileNotFoundException
     */
    public static function fromExisting(string $path): self
    {
        if (empty($path) || ! file_exists($path)) {
            throw new FileNotFoundException;
        }

        return (new self)->setPath($path);
    }

    /**
     * @throws FileNotFoundException
     */
    public static function fromUpload(array $file): self
    {
        if (! array_key_exists('tmp_name', $file) ||
            ! array_key_exists('name', $file) ||
            empty($file['tmp_name']) ||
            empty($file['name']) ||
            ! file_exists($file['tmp_name'])
        ) {
            throw new FileNotFoundException;
        }

        $instance = (new self)
            ->setPath($file['tmp_name'])
            ->setOriginalName($file['name']);
        $instance->isUploaded = true;

        return $instance;
    }

    public function getInfo(): SplFileInfo
    {
        if ($this->info === null) {
            $this->info = new SplFileInfo($this->getPath());
        }
        return $this->info;
    }

    public function mimeType(): string
    {
        return (string) mime_content_type($this->getPath());
    }

    public function extension(): string
    {
        if ($this->isUploaded) {
            $parts = explode('.', $this->getOriginalName());
            $last = array_key_last($parts);

            return $last === 0 ? '' : $parts[$last];
        }

        return $this->getInfo()->getExtension();
    }

    public function name(): string
    {
        if ($this->isUploaded) {
            return basename($this->getOriginalName(), '.'.$this->extension());
        }

        return $this->getInfo()->getBasename('.'.$this->extension());
    }

    public function moveTo(string $destination): bool
    {
        if ($this->isUploaded) {
            return move_uploaded_file($this->getPath(), $destination);
        }

        return rename($this->getPath(), $destination);
    }

    public function copyTo(string $destination): bool
    {
        if ($this->isUploaded) {
            return false;
        }

        return copy($this->getPath(), $destination);
    }

    public function delete(): bool
    {
        return unlink($this->getPath());
    }

    public function download(string $name = 'document'): void
    {
        ob_end_clean();
        header('Content-Type: '.$this->mimeType());
        header('Content-Disposition: attachment; filename='.$name.'.'.$this->extension());
        header('Content-Length: '.$this->getInfo()->getSize());
        readfile($this->getPath());
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setOriginalName(string $name): self
    {
        $this->originalName = basename($name);

        return $this;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }
}
