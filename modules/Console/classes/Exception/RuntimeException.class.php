<?php
class Miao_Console_Exception_RuntimeException extends Miao_Console_Exception
{
    /**
     * Usage
     *
     * @var string
     */
    protected $usage = '';

    /**
     * Constructor
     *
     * @param string $message
     * @param string $usage
     */
    public function __construct($message, $usage = '')
    {
        $this->usage = $usage;
        parent::__construct($message);
    }

    /**
     * Returns the usage
     *
     * @return string
     */
    public function getUsageMessage()
    {
        return $this->usage;
    }
}
