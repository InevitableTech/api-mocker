<?php

namespace Genesis\Api\Mocker\Base;

use Genesis\Api\Mocker\Contract\StorageHandler;

/**
 * FileStorage class.
 * 
 * Stores in individual endpoint files. Should it?
 */
class FileStorage implements StorageHandler
{
    CONST INDEX = 'index';

    public function save(string $endpoint, $data): bool
    {
        if ($endpoint === self::INDEX) {
            throw new AppException(self::INDEX . ' is a reserved file name. Please use another.');
        }

        $filename = $this->noramlise($endpoint);
        $filePath = $this->getFile($filename);
        $this->indexFile($filePath);
        file_put_contents($filePath, json_encode($data));

        return true;
    }

    public function get(string $endpoint): array
    {
        $filename = $this->getFile($endpoint);

        if (!file_exists($filename)) {
            throw new AppException('Filename ' . $filename . ' not found.');
        }

        $response = json_decode(file_get_contents($filename), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \UnexpectedValueException('Expected to have valid json: ' . json_last_error_msg());
        }

        return $response;
    }

    public function getIndex(): array
    {
        $filename = $this->getFile(self::INDEX);

        if (! file_exists($filename)) {
            return [];
        }

        $files = json_decode(file_get_contents($filename), true);

        return $files ?? [];
    }

    /**
     * @return string
     */
    public function purge(): ?array
    {
        $index = $this->getIndex();

        foreach ($index as $file) {
            unlink($file);
        }

        $this->clearIndex();

        return $index;
    }

    /**
     * @param string $endpoint Can be simple string or regex.
     *
     * @return string
     */
    private function noramlise(string $endpoint): string
    {
        return $endpoint;
    }

    private function getFile($id): string
    {
        return sys_get_temp_dir() . '/' . $id . '.json';
    }

    /**
     * @param string $file
     * @param mixed  $endpoint
     *
     * @return string
     */
    private function indexFile($file)
    {
        $index = $this->getIndex();

        if (!$index || array_search($file, $index) === false) {
            $index[] = $file;
            $this->saveIndex($index);
        }
    }

    private function saveIndex($index)
    {
        file_put_contents($this->getFile(self::INDEX), json_encode($index));
    }

    private function clearIndex()
    {
        file_put_contents($this->getFile(self::INDEX), '');
    }
}
