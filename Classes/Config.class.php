<?

namespace Classes;

use Exception;

/**
 * Класс для взаимодействия с файлом конфига
 * Класс реализует паттерн Singleton
 * Для работы класса нужно предварительно объявить константу CFG_PATH,
 * в которой указан путь к json файлу конфига
 */
class Config
{
    private static $instance = null;
    private static $cfg = null;
    private $cfgChild = null;

    private function __construct($cfg = null)
    {
        if (!is_null($cfg)) {
            $this->cfgChild = $cfg;
        }
    }
    private function __clone()
    {
    }
    private function __wakeup()
    {
    }

    public static function getInstance(): object
    {
        if (self::$instance !== null)
            return self::$instance;

        if (!file_exists(CFG_PATH)) throw new Exception('Config file not found');

        $cfg = json_decode(file_get_contents(CFG_PATH), 1);
        if (json_last_error() !== JSON_ERROR_NONE)
            throw new Exception('Config file is not JSON format');

        self::$cfg = $cfg;
        self::$instance = new self();
        return self::$instance;
    }

    public function __get($val)
    {
        if (isset(self::$cfg[$val]) && is_array(self::$cfg[$val]))
            return new self(self::$cfg[$val]);

        if (isset($this->cfgChild) && isset($this->cfgChild[$val]))
            return $this->cfgChild[$val];
        elseif (!isset($this->cfgChild) && isset(self::$cfg[$val]))
            return self::$cfg[$val];
        else
            throw new Exception('variable "' . $val . '" is not exist');
    }

    public function get()
    {
        if (isset($this->cfgChild))
            return $this->cfgChild;
        return self::$cfg;
    }

    // Запрещаю изменять данные
    public function __set($key, $val)
    {
    }
}
