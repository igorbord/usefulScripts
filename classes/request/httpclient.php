<?

namespace Classes\Request;

/**
 * Класс для работы с запросами через curl
 */
class HttpClient
{
    /** 
     * Код ошибки
     * @var int */
    private $errorCode = null;

    /** @var int */
    private $connectTimeout = 30;

    /** @var int */
    private $timeout = 10;

    /**
     * Контент последнего ответа
     * @var string
     */
    private $response = '';

    /** 
     * Данные запроса
     * @var array */
    private $requestInfo = [];

    /** 
     * Данные ответа на запрос
     * @var array */
    private $responseInfo = [];

    public function __construct()
    {
    }

    public function request(string $method, string $url, array $body = [], array $options = []): object
    {
        if ($method == 'GET') {
            $this->get($url, $body, $options);
        }
        if ($method == 'POST') {
            $this->post($url, $body, $options);
        }

        if (!empty($this->getErrorCode())) {
            throw new \Exception('Error on Request');
        }

        return $this;
    }

    private function get(string $url, array  $body = [], array $options = []): void
    {
        $url = $this->builderUrl($url, $body);
        $options[CURLOPT_HTTPGET] = true;
        $this->doRequest($url, $options);
    }

    private function post(string $url, array $body = [], array $options = []): void
    {
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = $body;
        $this->doRequest($url, $options);
    }

    private function doRequest(string $url, array $options): void
    {
        $ch = curl_init();
        $options = $options + [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
            CURLOPT_TIMEOUT => $this->timeout,
        ];
        curl_setopt_array($ch, $options);

        $this->setResponse(curl_exec($ch));
        $this->setErrorCode(curl_errno($ch));

        $this->setRequestInfo($options);
        $this->setResponseInfo(curl_getinfo($ch));
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

    private function setErrorCode(int $code)
    {
        $this->errorCode = $code ? $code : NULL;
        return $this;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    private function setResponse(string $response): object
    {
        $this->response = $response;
        return $this;
    }

    public function getResponse()
    {
        $response = json_decode($this->response, true);
        if (json_last_error() === JSON_ERROR_NONE)
            return $response;

        return $this->response;
    }

    public function setConnectTimeout(int $connectTimeout): object
    {
        $this->connectTimeout = $connectTimeout;
        return $this;
    }

    public function getConnectTimeout(): int
    {
        return $this->connectTimeout;
    }

    public function setTimeout(int $timeout): object
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    private function setRequestInfo(array $data)
    {
        $this->requestInfo = [
            'CURLOPT_URL' => $data[CURLOPT_URL],
            'CURLOPT_HTTPGET' => $data[CURLOPT_HTTPGET],
            'CURLOPT_POST' => $data[CURLOPT_POST],
            'CURLOPT_POSTFIELDS' => $data[CURLOPT_POSTFIELDS],
            'CURLOPT_RETURNTRANSFER' => $data[CURLOPT_RETURNTRANSFER],
            'CURLOPT_SSL_VERIFYHOST' => $data[CURLOPT_SSL_VERIFYHOST],
            'CURLOPT_SSL_VERIFYPEER' => $data[CURLOPT_SSL_VERIFYPEER],
            'CURLOPT_CONNECTTIMEOUT' => $data[CURLOPT_CONNECTTIMEOUT],
            'CURLOPT_TIMEOUT' => $data[CURLOPT_TIMEOUT],
        ];
        return $this;
    }

    public function getRequestInfo(): array
    {
        return $this->requestInfo;
    }

    private function setResponseInfo(array $data)
    {
        $this->responseInfo = $data;
        return $this;
    }

    public function getResponseInfo(): array
    {
        return $this->responseInfo;
    }
}
