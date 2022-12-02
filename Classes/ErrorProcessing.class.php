<?
class ErrorProcessing
{
    public function getErrorName(int $error): string
    {
        $errors = [
            E_ERROR             => 'ERROR',
            E_WARNING           => 'WARNING',
            E_PARSE             => 'PARSE',
            E_NOTICE            => 'NOTICE',
            E_CORE_ERROR        => 'CORE_ERROR',
            E_CORE_WARNING      => 'CORE_WARNING',
            E_COMPILE_ERROR     => 'COMPILE_ERROR',
            E_COMPILE_WARNING   => 'COMPILE_WARNING',
            E_USER_ERROR        => 'USER_ERROR',
            E_USER_WARNING      => 'USER_WARNING',
            E_USER_NOTICE       => 'USER_NOTICE',
            E_STRICT            => 'STRICT',
            E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
            E_DEPRECATED        => 'DEPRECATED',
            E_USER_DEPRECATED   => 'USER_DEPRECATED',
        ];
        if (array_key_exists($error, $errors)) {
            return $errors[$error];
        }

        return $error;
    }

    public function register(): void
    {
        // говорим php отслеживать все возможные ошибки
        ini_set('display_errors', 'on');
        error_reporting(E_ALL | E_STRICT);

        set_error_handler([$this, 'errorHandler']);
        register_shutdown_function([$this, 'fatalErrorHandler']);
    }

    public function errorHandler($errno, $errstr, $file, $line): bool
    {
        $this->showError($errno, $errstr, $file, $line);
        return true; // возвращаем true, чтоб управление обработкой ошибок НЕ было передано встроенному обработчику
    }

    public function fatalErrorHandler(): void
    {
        // если в буфере находим фатальную ошибку,
        if ($error = error_get_last() and $error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR)) {
            ob_end_clean(); // сбросить буфер, завершить работу буфера

            $this->showError($error['type'], $error['message'], $error['file'], $error['line'], 500);
        }

        // в противном случае, ничего не делаем, оставляем работу скрипта на усмотрение встроенного обработчика.
    }

    private function showError($errno, $errstr, $file, $line, $status = 500): void
    {
        header("HTTP/1.1 $status");

        $errorData = [
            'code' => $errno,
            'name' => $this->getErrorName($errno),
            'file' => $file,
            'line' => $line,
            'description' => $errstr,
            'status' => $status,
        ];

        if (function_exists('debug')) {
            debug($errorData, 'ERROR');
        } else {
            echo '<pre>' . print_r($errorData, 1) . '</pre>';
        }
    }

    public function mapErrorCode(int $code): string
    {
        $error = null;
        switch ($code) {
            case E_PARSE:
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                $error = 'Fatal Error';
                break;
            case E_WARNING:
            case E_USER_WARNING:
            case E_COMPILE_WARNING:
            case E_RECOVERABLE_ERROR:
                $error = 'Warning';
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                $error = 'Notice';
                break;
            case E_STRICT:
                $error = 'Strict';
                break;
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $error = 'Deprecated';
                break;
            default:
                break;
        }
        return $error;
    }
}
