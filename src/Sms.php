<?php

namespace Abolfazlrastegar\LaravelSms;

use Abolfazlrastegar\LaravelSms\Exception\createMessageErrorException;
use phpDocumentor\Reflection\Types\This;

class Sms
{
    /**
     * @var mixed|string
     */
    private $driver_sms;

    /**
     * @var string | array
     */
    private $mobile;

    /**
     * @var string
     */
    private $template;

    /**
     * @var string | array
     */
    private $message;

    /**
     * @var string | array
     */
    private $params = null;

    public function __construct($name_sms = '')
    {
        $this->setDriver($name_sms);
    }

    /**
     * @param $name_sms
     * @return static
     */
    public static function make ($name_sms = '')
    {
        return new static($name_sms);
    }


    /**
     * @param $mobile
     * @return $this
     */
    public function mobile ($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * @param $template
     * @return $this
     */
    public function template ($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @param $message
     * @return $this
     */
    public function message ($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param $params
     * @return $this
     */
    public function params ($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @return $this
     */
    public function defaultSms ()
    {
        $this->setDriver(config('sms.default'));

        return $this;
    }

    /**
     * @return mixed
     * @throws createMessageErrorException
     */
    public function sendVerifyCode ()
    {
        return $this->driver_sms->sendVerifyCode($this->mobile, $this->template, $this->params);
    }

    /**
     * @return mixed
     * @throws createMessageErrorException
     */
    public function sendMessages ()
    {
        return $this->driver_sms->sendMessages($this->mobile, $this->message, $this->params);
    }

    /**
     * @return mixed
     * @throws createMessageErrorException
     *
     */
    public function sendMessageGroup ()
    {
        return $this->driver_sms->sendMessageGroup($this->mobile, $this->message, $this->params);
    }
    /**
     * @return mixed
     * @throws createMessageErrorException
     */
    public function voiceCall ()
    {
        return $this->driver_sms->voiceCall($this->mobile, $this->message, $this->params);
    }

    /**
     * @return mixed
     * @throws createMessageErrorException
     */
    private function makeSms ($driverName)
    {
        $sms_class = 'Abolfazlrastegar\LaravelSms\Drivers\\' . $driverName;
        if (class_exists($sms_class, true))
        {
            return new $sms_class;
        }
        throw createMessageErrorException::notClass($driverName, 'Abolfazlrastegar\LaravelSms\Drivers');
    }

    private function setDriver($driverName) {
        $this->driver_sms = $this->makeSms($driverName);
    }
}
