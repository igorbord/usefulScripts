<?

/**
 * Для работы класса нужно предварительно объявить константу CFG_PATH,
 * в которой указан путь к json файлу конфига
 */
class Config
{
    private static $instance = null;
    private static $cfg = null;

    private function __construct($cfg = null)
    {
        if (!is_null($cfg)) {
            self::$cfg = $cfg;
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
        if (is_array(self::$cfg[$val])) {
            return new self(self::$cfg[$val]);
        }
        return self::$cfg[$val];
    }

    public function setToken($value): object
    {
        self::$cfg['token'] = $value;
        if (!file_exists(CFG_PATH)) throw new Exception('Config file not found');
        file_put_contents(CFG_PATH, json_encode(self::$cfg), LOCK_EX);
        return $this;
    }

    public function get()
    {
        return self::$cfg;
    }
}
