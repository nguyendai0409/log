<?php
namespace MyLog;

use \SplFileObject;
use \LimitIterator;

class ReadLog
{
    /*
     * folder include file log
     */
    private $dir;

    /**
     * @var int Limit row log default = 1000
     */
    private $limitRow;

    /**
     * ReadLog constructor.
     *
     * @param $dir (require dir include file log)
     */
    function __construct($dir)
    {
        $this->setDir($dir);
        $this->setLimitRow(0);

        if(is_dir($this->dir))
        {
            return true;
        } else {
            return false;
        }
    }//end function __construct

    /**
     * @param $dir
     *  //set folder include log
     * @return bool
     */
    public function setDir($dir)
    {
        if ($dir != '') {
            $this->dir = $dir;
        }

        if(is_dir($this->dir))
        {
            return true;
        } else {
            return false;
        }
    }//end function setDir

    /**
     * @param int $row
     * limit row log return
     */
    public function setLimitRow($row = 0)
    {
        if ($row == 0) {
            $this->limitRow = 1000;
        } else {
            $this->limitRow = $row;
        }
    }

    /**
     * @return array list file log in folder log
     */
    public function getListFileLog()
    {
        $list = scandir('writeloghere', 1);
        unset($list[sizeof($list) - 1]);
        unset($list[sizeof($list) - 1]);

        return $list;
    }//end function getListFileLog

    /**
     * @param     $name
     * @param int $startLine
     * @param int $limitRow
     *
     * get content file log
     *
     * @return array row log
     */
    public function getContentLog($name, $startLine = 0, $limitRow = 0)
    {
        if ($limitRow != 0) {
            $this->limitRow = $limitRow;
        }

        if ($startLine <= $this->countLineLog($name)) {

            $arrayLine = [];
            $file = new SplFileObject($this->dir.'/'.$name);
            $fileIterator = new LimitIterator($file, $startLine - 1,
                $this->limitRow);

            foreach ($fileIterator as $line) {
                $arrayLine[] = $line;
            }

            unset($file);

            return $arrayLine;
        } else {
            return [];
        }
    }//end function getContentLog

    /**
     * @param $name name file log
     *
     * @return int all row of file log
     */
    public function countLineLog($name)
    {
        $countLine = count(file($this->dir.'/'.$name));

        return $countLine;
    }//end function countLineLog

    /**
     * @param $name
     *  del file log
     * @return bool
     */
    public function delFileLog($name)
    {
        if ($name != '') {
            $file = $this->dir.'/'.$name;

            if (file_exists($file))
            {
                unlink($file);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }//end function delFileLog
}//end class