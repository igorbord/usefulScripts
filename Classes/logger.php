<?

namespace Classes;

/**
 * Логирование
 */
class Logger
{
    /**
     * Логирование данных в файл
     * @param string $type Тип сообщения (Ошибка или Информационное сообщение)
     * @param mixed $data Данные для логирования
     * @param int $dealId ID сделки
     */
    public static function fileLog($data, int $dealId = null, string $type = 'INF'): void
    {
        if (LOG_PATH == '')
            throw new \Exception("LOG_PATH is not exist");

        $date = new \DateTime();
        $dir = LOG_PATH . "/{$date->format('Y-m-d')}";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if ($type === 'F_ERR')
            $filePath = "{$dir}/{$date->format('H')}_errors.log";
        else
            $filePath = "{$dir}/{$date->format('H')}.log";

        if (isset($dealId))
            $logData = "[{$date->format('Y-m-d H:i:s')}]\t{$type}\tdeal_{$dealId} => " . print_r($data, 1);
        else
            $logData = "[{$date->format('Y-m-d H:i:s')}]\t{$type}\t => " . print_r($data, 1);

        file_put_contents($filePath, $logData . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    /**
     * Удаление старых файлов логов
     */
    public static function deleteOldFileLogs(): void
    {
        foreach (glob(LOG_CATALOG_PATH . "/*") as $catalogCompany) {
            foreach (glob($catalogCompany . '/*') as $catalog) {
                if (time() - filemtime($catalog) > (86400 * DAY_CLEAR_LOGS)) {
                    self::recursiveRemoveDir($catalog);
                }
            }
        }
    }

    private static function recursiveRemoveDir(string $dir): void
    {
        $includes = new FilesystemIterator($dir);
        foreach ($includes as $include) {
            if (is_dir($include) && !is_link($include)) {
                self::recursiveRemoveDir($include);
            } else {
                unlink($include);
            }
        }
        rmdir($dir);
    }
}
