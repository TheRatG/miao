<?php
/**
 * @author vpak
 * @date 2013-08-12 16:02:31
 */

namespace Miao\Office\Action;

class KCaptcha extends Action
{
    public function execute()
    {
        $captcha = new \Miao\Form\KCaptcha();
        $captcha->generate();
        $value = $captcha->getKeyString();
        \Miao\Session::getInstance()
            ->save( \Miao\Form\Control\Captcha::SKEY_NAME, $value );
    }
}