<?php
/**
 * Created by PhpStorm.
 * User: peyman
 * Date: 2/3/2019
 * Time: 11:26 PM
 */

namespace Peyman1992\TelegramLibrary\Session;

use Exception;
use Illuminate\Support\Facades\File;
use function is_dir;
use function is_writable;
use const DIRECTORY_SEPARATOR;

class FileSessionDriver extends SessionDriver
{
    private string $sessionFileDirectoryPath;
    private string $sessionPrefix;
    private string $sessionFileFormat;

    public function __construct(string $id, string $sessionFileDirectoryPath, string $sessionFilePrefix = "", string $sessionFileFormat = "")
    {
        if (!is_dir($sessionFileDirectoryPath)) {
            File::makeDirectory($sessionFileDirectoryPath, 0755, TRUE);
        }
        if (!is_dir($sessionFileDirectoryPath)) {
            throw new Exception("Cannot use FileSessionHandler, directory '{$sessionFileDirectoryPath}' not found", 1);
        }

        if (!is_writable($sessionFileDirectoryPath)) {
            throw new Exception("Cannot use FileSessionHandler, directory '{$sessionFileDirectoryPath}' is not writable", 2);
        }
        $this->sessionFileDirectoryPath = $sessionFileDirectoryPath;
        $this->sessionPrefix = $sessionFilePrefix;
        $this->sessionFileFormat = $sessionFileFormat;
        parent::__construct($id);
    }

    protected function getSessionFilePath(): string
    {
        return $this->sessionFileDirectoryPath . DIRECTORY_SEPARATOR . $this->sessionPrefix . $this->id . $this->sessionPrefix . $this->sessionFileFormat;
    }

    protected function read(): string
    {
        $sessionFilePath = $this->getSessionFilePath();
        if (file_exists($sessionFilePath)) {
            $result = @file_get_contents($sessionFilePath);
            if ($result === FALSE) {
                return "";
            }

            return $result;
        }

        return "";
    }

    protected function write(string $json): bool
    {
        $sessionFilePath = $this->getSessionFilePath();
        $result = @file_put_contents($sessionFilePath, $json);

        if ($result === FALSE) {
            return FALSE;
        }

        return TRUE;
    }

    public function destroy()
    {
        $sessionFilePath = $this->getSessionFilePath();
        if (file_exists($sessionFilePath))
            unlink($sessionFilePath);
    }
}