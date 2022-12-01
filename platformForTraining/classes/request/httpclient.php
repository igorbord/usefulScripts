<?

namespace Classes\Request;

/**
 * Класс для работы с запросами через curl
 */
class HttpClient
{
    /** @var string */
    private $last_error = '';

    /** @var int */
    private $connect_timeout = 30;

    /** @var int */
    private $timeout = 10;

    /**
     * Контент последнего ответа
     * @var string
     */
    private $last_content = '';

    /** @var bool */
    private $debug = false;

    public function __construct()
    {
    }

    public function request(string $method, string $url, array $body = [], array $options = []): array
    {
        $response = false;
        if ($method == 'GET') {
            $response = $this->get($url, $body, $options);
        }
        if ($method == 'POST') {
            $response = $this->post($url, $body, $options);
        }

        $response = json_decode($response, true);
        if ($response === null) {
            return false;
        }

        return $response;
    }

    private function get(string $url, array  $body = [], array $options = [])
    {
        $url = $this->builderUrl($url, $body);
        $options[CURLOPT_HTTPGET] = true;
        return $this->doRequest($url, $options);
    }

    private function post(string $url, array $body = [], array $options = [])
    {
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = $body;
        return $this->doRequest($url, $options);
    }

    private function doRequest(string $url, array $options)
    {
        $ch = curl_init();
        $options = $options + [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => $this->connect_timeout,
            CURLOPT_TIMEOUT => $this->timeout,
        ];
        curl_setopt_array($ch, $options);

        if ($this->debug) curl_setopt($ch, CURLOPT_VERBOSE, true);

        $response = curl_exec($ch);
        $errno = curl_errno($ch);

        if ($errno) $this->last_error = 'Error curl code: ' . $errno;

        if ($this->debug) {
            $arrDebug = [
                'curl_setopt' => [
                    'CURLOPT_URL' => $options[CURLOPT_URL],
                    'CURLOPT_HTTPGET' => $options[CURLOPT_HTTPGET],
                    'CURLOPT_POST' => $options[CURLOPT_POST],
                    'CURLOPT_POSTFIELDS' => $options[CURLOPT_POSTFIELDS],
                    'CURLOPT_RETURNTRANSFER' => $options[CURLOPT_RETURNTRANSFER],
                    'CURLOPT_SSL_VERIFYHOST' => $options[CURLOPT_SSL_VERIFYHOST],
                    'CURLOPT_SSL_VERIFYPEER' => $options[CURLOPT_SSL_VERIFYPEER],
                    'CURLOPT_CONNECTTIMEOUT' => $options[CURLOPT_CONNECTTIMEOUT],
                    'CURLOPT_TIMEOUT' => $options[CURLOPT_TIMEOUT],
                ],
                'getInfo' => curl_getinfo($ch),
            ];
            if ($errno) $arrDebug['errorCode'] = $this->last_error;

            if (function_exists('debug')) {
                debug($arrDebug, get_class($this) . ' debug:');
            } else {
                echo '<pre>' . get_class($this) . ' debug: ' . print_r($arrDebug, 1) . '</pre>';
            }
        }

        if ($errno) return false;

        $this->last_content = $response;
        return $this->last_content;
    }

    private function builderUrl(string $url, array $body): string
    {
        if (!empty($body)) {
            $query = self::parametersToQueryString($body);

            $ps = strpos($url, '?');
            if ($ps === false) {
                $url .= $query;
            } else {
                $url = substr($url, 0, $ps) . $query;
            }
        }
        return $url;
    }

    public static function parametersToQueryString(array $parameters): string
    {
        $query = '';
        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $query .= (isset($query[0]) ? '&' : '?') . $key . '[]' . '=' . urlencode($item);
                }
            } else
                $query .= (isset($query[0]) ? '&' : '?') . urlencode($key) . '=' . urlencode($value);
        }

        return $query;
    }

    public function getLastError(): string
    {
        return $this->last_error;
    }

    public function getLastContent(): string
    {
        return $this->last_content;
    }

    public function getConnectTimeout(): int
    {
        return $this->connect_timeout;
    }

    public function setConnectTimeout(int $connect_timeout): void
    {
        $this->connect_timeout = $connect_timeout;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }
}
