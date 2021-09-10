<?php

namespace CHK\AdminDebugLogs\Block\Adminhtml\Index;

use LimitIterator;
use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use SplFileObject;

/**
 * Class Index
 * @package CHK\AdminDebugLogs\Block\Adminhtml\Index
 */
class Index extends Container
{
    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var FileDriver
     */
    private $fileDriver;

    /**
     * Index constructor.
     *
     * @param DirectoryList $directoryList
     * @param FileDriver $fileDriver
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        DirectoryList $directoryList,
        FileDriver $fileDriver,
        Context $context,
        array $data = []
    ) {
        $this->fileDriver = $fileDriver;
        $this->directoryList = $directoryList;
        parent::__construct($context, $data);
    }

    /**
     * @return string`
     */
    public function getLogPath()
    {
        try {
            return $this->directoryList->getPath('log');
        } catch (FileSystemException $e) {
            $this->_logger->error('Failed to get the log directory path ' . $e);
        }
        return '';
    }

    /**
     * @param $path
     * @return string|void
     */
    public function getFileContent($filePath)
    {
        try {
            if ($this->getFileSizeInKb($filePath) > 300) {
                return $this->readLastLines($filePath);
            } else {
                return $this->fileDriver->fileGetContents($filePath);
            }
        } catch (FileSystemException $e) {
            $this->_logger->error('Failed to get the file content ' . $e);
        }
        return;
    }

    /**
     * @param $filename
     * @param int $num
     * @param false $reverse
     * @return string
     */
    public function readLastLines($filename, $num = 300, $reverse = false)
    {
        $file = new SplFileObject($filename, 'r');
        $file->seek(PHP_INT_MAX);
        $last_line = $file->key();
        $lines = new LimitIterator($file, $last_line - $num, $last_line);
        $arr = iterator_to_array($lines);
        if($reverse) $arr = array_reverse($arr);
        return implode('',$arr);
    }

    /**
     * get File size in KB
     *
     * @param $filePath
     * @return int
     */
    public function getFileSizeInKb($filePath): int
    {
        $size = filesize($filePath);
        if($size < 1024) {
            return 0;
        } else {
            return round($size / 1024);
        }
    }
}
