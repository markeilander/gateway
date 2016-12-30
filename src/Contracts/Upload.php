<?php

namespace Eilander\Gateway\Contracts;

/**
 * Interface Uplods.
 */
interface Upload
{
    /**
     * Upload file.
     *
     * @param string $file file to upload
     * @param array  $data
     *
     * @return mixed
     */
    public function upload($file, array $data = [], $validation = '');
}
