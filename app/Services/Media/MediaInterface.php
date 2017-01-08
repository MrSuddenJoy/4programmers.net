<?php

namespace Coyote\Services\Media;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface MediaInterface
{
    /**
     * @return string
     */
    public function getFilename();

    /**
     * @param string $filename
     */
    public function setFilename($filename);

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return Url
     */
    public function url();

    /**
     * @return mixed
     */
    public function get();

    /**
     * @return int
     */
    public function size();

    /**
     * @param UploadedFile $uploadedFile
     * @return MediaInterface
     */
    public function upload(UploadedFile $uploadedFile);

    /**
     * @param mixed $content
     * @return MediaInterface
     */
    public function put($content);

    /**
     * @return bool
     */
    public function delete();
}
