<?php

namespace CHK\AdminDebugLogs\Block\Adminhtml\Index;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File as FileDriver;

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
    public function getFileContent($path)
    {
        try {
            return $this->fileDriver->fileGetContents($path);
        } catch (FileSystemException $e) {
            $this->_logger->error('Failed to get the file content ' . $e);
        }
        return;
    }
}
