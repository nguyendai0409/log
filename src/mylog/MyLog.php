<?php
namespace MyLog;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class MyLog
{
    private $channel;
    private $dir;
    private $name;
    private $dateFormat;
    private $format;
    private $extra;

    /**
     * MyLog constructor.
     *
     * @param string $channel
     * @param string $dir
     * @param string $dateFormat
     * @param string $format
     * @param array  $extra
     *
     */
    function __construct(
        $dir,
        $name,
        $channel = '',
        $dateFormat = '',
        $format = '',
        array $extra = []
    ) {
        $this->setChannel($channel);
        $this->setDir($dir);
        $this->setName($name);
        $this->setDateFormat($dateFormat);
        $this->setFormat($format);
        $this->setExtra();
    }

    /**
     * @param string $channel
     * set channel log
     */
    public function setChannel($channel = '')
    {
        if ($channel == '') {
            $this->channel = 'normal_log';
        } else {
            $this->channel = $channel;
        }
    }

    /**
     * @param $name
     * set name of file log
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $dir
     * set place save log
     */
    public function setDir($dir)
    {
        $this->dir = $dir;
    }//end function setDir

    /**
     * @param string $dateFormat
     * set date format default is 'd/m/Y H:i:s'
     */
    public function setDateFormat($dateFormat = '')
    {
        if ($dateFormat == '') {
            $this->dateFormat = 'd/m/Y H:i:s';
        } else {
            $this->dateFormat = $dateFormat;
        }
    }//end function setDateFormat

    /**
     * @param string $format
     * set format log default is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
     */
    public function setFormat($format = '')
    {
        $this->format = $format;
        if ($format === '') {
            $this->format
                = "[%datetime%] %channel%.%level_name%: %message%\n";
        } else {
            $this->format = $format;
        }
    }//end function setFormat

    /**
     * @param array $extra
     * set extra
     */
    public function setExtra($extra = [])
    {
        $this->extra = $extra;
    }//end function setExtra

    /**
     * @return string name channel
     */
    public function getChannel()
    {
        return $this->channel;
    }//end function getChannel

    /**
     * @return string place save log
     */
    public function getDir()
    {
        return $this->dir;
    }//end function getDir

    /**
     * @return string format write log
     */
    public function getFormat()
    {
        return $this->format;
    }//end function getFormat

    /**
     * @return string date format
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }//end function getDateFormat


    /**
     * @return array Extra
     */
    public function getExtra()
    {
        return $this->extra;
    }//end function getExtra

    /**
     * @param       $message
     * @param array $context
     * write log with level emergency
     */
    public function emergency($message, array $context = array())
    {
        $this->log(Logger::EMERGENCY, $message, $context);
    }//end function emergency

    /**
     * @param       $message
     * @param array $context
     * write log with level alert
     */
    public function alert($message, array $context = array())
    {
        $this->log(Logger::ALERT, $message, $context);
    }//end function alert

    /**
     * @param       $message
     * @param array $context
     * write log with level critical
     */
    public function critical($message, array $context = array())
    {
        $this->log(Logger::CRITICAL, $message, $context);
    }//end function critical

    /**
     * @param       $message
     * @param array $context
     * write log with level error
     */
    public function error($message, array $context = array())
    {
        $this->log(Logger::ERROR, $message, $context);
    }//end function error

    /**
     * @param       $message
     * @param array $context
     * write log with level warning
     */
    public function warning($message, array $context = array())
    {
        $this->log(Logger::WARNING, $message, $context);
    }//end function warning

    /**
     * @param       $message
     * @param array $context
     * write log with level notice
     */
    public function notice($message, array $context = array())
    {
        $this->log(Logger::NOTICE, $message, $context);
    }//end function notice

    /**
     * @param       $message
     * @param array $context
     * write log with level info
     */
    public function info($message, array $context = array())
    {
        $this->log(Logger::INFO, $message, $context);
    }//end function info

    /**
     * @param       $message
     * @param array $context
     * write log with level debug
     */
    public function debug($message, array $context = array())
    {
        $this->log(Logger::DEBUG, $message, $context);
    }//end function debug

    /**
     * @param       $level
     * @param       $message
     * @param array $context
     *
     * @return bool
     */
    private function log($level, $message, array $context = array())
    {
        if ($this->dir != '') {
            // Create the logger
            $logger = new Logger($this->channel);

            $output = $this->format;

            // finally, create a formatter
            $formatter = new LineFormatter($output, $this->dateFormat);
            $stream = new StreamHandler($this->dir.'/'.$this->name,
                Logger::DEBUG);

            $logger->pushProcessor(function ($record) {
                $record['extra'] = $this->extra;

                return $record;
            });

            $stream->setFormatter($formatter);
            $logger->pushHandler($stream);

            $message = $this->interpolate($message, $context);

            switch ($level) {
                case 100:
                    $logger->debug($message, $context);
                    break;
                case 200:
                    $logger->info($message, $context);
                    break;
                case 250:
                    $logger->notice($message, $context);
                    break;
                case 300:
                    $logger->warning($message, $context);
                    break;
                case 400:
                    $logger->error($message, $context);
                    break;
                case 500:
                    $logger->critical($message, $context);
                    break;
                case 550:
                    $logger->alert($message, $context);
                    break;
                case 600:
                    $logger->emergency($message, $context);
                    break;
                default:
                    break;
            }

            return true;
        } else {
            return false;
        }
    }//end function log

    /**
     * @param string $message
     * @param array  $context
     * Interpolates context values into the message placeholders.
     *
     * @return string
     */
    private function interpolate($message = '', $context = [])
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val)
                && (!is_object($val)
                    || method_exists($val, '__toString'))
            ) {
                $replace['{'.$key.'}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }//end function interpolate
}