<?

namespace Request;

/**
 * Класс для безопасной работы с массивом $_REQUEST
 * Класс реализует паттерн Singleton
 */
class Request
{
    private static $instance = null;
    private static $storage; // переменная хранящая данные GET и POST

    private function __construct()
    {
    }
    private function __clone()
    {
    }
    private function __wakeup()
    {
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$storage = self::cleanInput($_REQUEST);
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __get($name)
    {
        if (isset(self::$storage[$name])) return self::$storage[$name];
    }
    
    // Запрещаю изменять данные
    private function __set($key, $val)
    {
    }

    // очистка данных от опасных символов
    private static function cleanInput($data)
    {
        if (is_array($data)) {
            $cleaned = [];
            foreach ($data as $key => $value) {
                $cleaned[$key] = self::cleanInput($value);
            }
            return $cleaned;
        }
        return trim(htmlspecialchars($data, ENT_QUOTES));
    }

    // возвращаем содержимое хранилища
    public function getRequest()
    {
        return self::$storage;
    }
}
