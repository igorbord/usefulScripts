<?

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
    private $pathKeys = [];

    private function __construct($cfg = null, $pathKeys = null, $key = null)
    {
        if (!is_null($cfg)) {
            $this->cfgChild = $cfg;
            $this->pathKeys = $pathKeys;
            $this->pathKeys[] = $key;
        }
    }
    public  function __clone()
    {
    }
    public  function __wakeup()
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
        if (!isset($this->cfgChild) && isset(self::$cfg[$val]) && is_array(self::$cfg[$val]))
            return new self(self::$cfg[$val], $this->pathKeys, $val);

        if (isset($this->cfgChild) && isset($this->cfgChild[$val])) {
            if (is_array($this->cfgChild[$val]))
                return new self($this->cfgChild[$val], $this->pathKeys, $val);
            return $this->cfgChild[$val];
        } elseif (!isset($this->cfgChild) && isset(self::$cfg[$val]))
            return self::$cfg[$val];
        else
            throw new Exception('variable "' . $val . '" is not exist');
    }

    public function get(): mixed
    {
        if (isset($this->cfgChild))
            return $this->cfgChild;
        return self::$cfg;
    }

    public function __set($key, $val): void
    {
        $pathKeys = array_merge($this->pathKeys, [$key]);

        // Проверка, что данный параметр можно изменять
        if (!in_array(implode('->', $pathKeys), [
            'key1->key3->key4',
        ])) throw new Exception("Куда полез!? Нельзя переписывать конфиг!");

        $newArray = [];
        for ($i = count($pathKeys) - 1; $i >= 0; $i--) {
            $newArray = [$pathKeys[$i] => $pathKeys[$i] == $key ? $val : $newArray];
        }

        self::$cfg = array_replace_recursive(self::$cfg, $newArray);
        $this->saveConfig();
    }

    private function saveConfig(): object
    {
        $result = file_put_contents(CFG_PATH, json_encode(self::$cfg), LOCK_EX);
        if ($result === false) throw new Exception('Error save config');
        if (!file_exists(CFG_PATH)) throw new Exception('Config file not found');
        return $this;
    }
}
