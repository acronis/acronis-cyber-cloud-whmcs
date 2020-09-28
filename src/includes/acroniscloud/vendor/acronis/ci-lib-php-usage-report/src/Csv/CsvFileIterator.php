<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Csv;

use Iterator;

class CsvFileIterator implements Iterator
{
    const UNLIMITED_LINE_LENGTH = 0;
    const DEFAULT_DELIMITER = ',';

    const RESOURCE_TYPE_STREAM = 'stream';

    protected $fileHandler;
    private $currentRow;
    private $currentIndex;

    /**
     * @throws \RuntimeException
     * @param resource $fileHandler Valid stream resource is opened for reading
     */
    public function __construct($fileHandler)
    {
        $this->init($fileHandler);
    }

    /**
     * @return array|false|mixed|null
     * @throws \RuntimeException
     */
    public function current()
    {
        if (is_null($this->currentRow)) {
            $this->currentRow = $this->readNext();
        }

        return $this->currentRow;
    }

    public function next()
    {
        $this->currentIndex++;
        $this->currentRow = null;
    }

    public function key()
    {
        return $this->currentIndex;
    }

    /**
     * @return bool
     * @throws \RuntimeException
     */
    public function valid()
    {
        return !$this->lastRowIsReached();
    }

    /**
     * @throws \RuntimeException
     */
    public function rewind()
    {
        $this->currentIndex = 0;
        $this->currentRow = null;
        $this->rewindFileHandler();
    }

    /**
     * @return array|false|null
     * @throws \RuntimeException
     * @throws \RuntimeException
     */
    protected function readNext()
    {
        $this->check();
        $data = fgetcsv($this->fileHandler, static::UNLIMITED_LINE_LENGTH, static::DEFAULT_DELIMITER);
        if ($data === false) {
            if (feof($this->fileHandler)) {
                // we return [null] to make similar value of empty line inside the CSV file and in the end of it.
                return [null];
            }

            throw new \RuntimeException('Unable to read data from file handler.');
        }

        return $data;
    }

    private function handlerIsOpened()
    {
        return get_resource_type($this->fileHandler) === static::RESOURCE_TYPE_STREAM;
    }

    /**
     * @throws \RuntimeException
     */
    private function rewindFileHandler()
    {
        $this->check();
        rewind($this->fileHandler);
    }

    /**
     * @return bool
     * @throws \RuntimeException
     */
    private function lastRowIsReached()
    {
        $this->check();
        return feof($this->fileHandler);
    }

    /**
     * @throws \RuntimeException
     */
    private function check()
    {
        if (!$this->handlerIsOpened()) {
            throw new \RuntimeException('Trying to read data from invalid file handler.');
        }
    }

    /**
     * @param $fileHandler
     * @throws \RuntimeException
     */
    private function init($fileHandler)
    {
        if (is_null($fileHandler)) {
            throw new \RuntimeException('Null file handler is passed to CSV iterator.');
        }

        $resourceType = get_resource_type($fileHandler);
        if ($resourceType !== static::RESOURCE_TYPE_STREAM) {
            throw new \RuntimeException(\sprintf(
                'Wrong resource type "%s" is passed to CSV iterator, "%s" is expected.',
                $resourceType,
                static::RESOURCE_TYPE_STREAM
            ));
        }

        $this->fileHandler = $fileHandler;
        $this->currentIndex = 0;
    }
}