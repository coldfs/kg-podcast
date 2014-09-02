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

    /**
     * @var array команды
     */
    protected $_data = [];

    /**
     * @var string фаил для записи
     */
    protected $_filename;


    /**
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->_filename = $filename;
    }

    /**
     * Добавляем команду
     *
     * @param string $command
     */
    public function add($command)
    {
        $this->_data[] = $command;
    }

    /**
     * Записываем фаил
     *
     * @return bool
     */
    public function write()
    {
        file_put_contents($this->_filename, implode("\n", $this->_data));
        return true;
    }
}
