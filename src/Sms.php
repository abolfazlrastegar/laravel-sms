<?php

namespace Abolfazlrastegar\LaravelSms;

use Abolfazlrastegar\LaravelSms\Exception\createMessageErrorException;
use phpDocumentor\Reflection\Types\This;

class Sms
{
    /**
     * @var mixed|string
     */
    private $name_sms;

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
        $this->name_sms = $name_sms;
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
        $this->name_sms = config('sms.default');

        return $this;
    }

    /**
     * @return mixed
     * @throws createMessageErrorException
     */
    public function sendVerifyCode ()
    {
        $sms = $this->makeSms();

       return $sms->sendVerifyCode($this->mobile, $this->template, $this->params);
    }

    /**
     * @return mixed
     * @throws createMessageErrorException
     */
    public function sendMessages ()
    {
        $sms = $this->makeSms();

        return $sms->sendMessages($this->mobile, $this->message, $this->params);
    }

    /**
     * @return mixed
     * @throws createMessageErrorException
     *
     */
    public function sendMessageGroup ()
    {
        $sms = $this->makeSms();

        return $sms->sendMessageGroup($this->mobile, $this->message, $this->params);
    }
    /**
     * @return mixed
     * @throws createMessageErrorException
     */
    public function voiceCall ()
    {
        $sms = $this->makeSms();

        return $sms->voiceCall($this->mobile, $this->message, $this->params);
    }

    /**
     * @return mixed
     * @throws createMessageErrorException
     */
    private function makeSms ()
    {
        $sms_class = 'Abolfazlrastegar\LaravelSms\Drivers\\' . $this->name_sms;
        if (class_exists($sms_class, true))
        {
            return new $sms_class;
        }
        throw createMessageErrorException::notClass($this->name_sms, 'Abolfazlrastegar\LaravelSms\Drivers');
    }
}
