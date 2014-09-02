<?php
/**
 * КГ Подкаст Парсер
 *
 * @package   KG
 * @author    Sergey Ivanov <coldfs.ru@gmail.com>
 * @copyright 2014 coldfs
 */

/**
 * Генератор фаилов с коммандами
 */
class Kg_Command
{

    protected $_data = [];

    protected $_filename;

    public function __construct($filename)
    {
        $this->_filename = $filename;
    }

    public function add($command)
    {
        $this->_data[] = $command;
    }

    public function write()
    {
        file_put_contents($this->_filename, implode("\n", $this->_data));
        return true;
    }
}
