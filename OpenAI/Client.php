<?php

namespace Abramovku\OpenAI;

class Client
{
    const API_URL = 'https://api.openai.com/v1/';

    private $token;
    private $curlHandle;
	private $isProxy;
	private $proxy;
	private $proxyAuth;

    public function __construct(string $token, array $proxy = [])
    {
        $this->token = $token;
        $this->curlHandle = curl_init();
        if (!$this->curlHandle) {
            throw new Exception('Failed to initialize cURL');
        }
	    $this->isProxy = !empty($proxy['ip']) && !empty($proxy['port']) && !empty($proxy['user']
			    && !empty($proxy['pass']));
	    $this->proxy = $proxy['ip'] . ":" . $proxy['port'];
	    $this->proxyAuth = $proxy['user'] . ":" . $proxy['pass'];
    }

    public function post(string $url, array $data = []): array
    {
        return $this->send(self::API_URL . $url, 'POST', $data);
    }

    private function send(string $url, string $method, array $data = []): array
    {
        $this->setCurlOptions($url, $method, $data);

        $result = curl_exec($this->curlHandle);

        if (curl_errno($this->curlHandle)) {
            throw new Exception('CURL Error - ' . curl_error($this->curlHandle));
        }

        curl_close($this->curlHandle);
        return json_decode($result, true) ?? [];
    }

    private function setCurlOptions (string $url, string $method, array $data = []): void
    {
        curl_setopt($this->curlHandle, CURLOPT_URL, $url);
        curl_setopt($this->curlHandle, CURLOPT_TIMEOUT, 35);
        curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json'
        ]);

		if($this->isProxy) {
			curl_setopt($this->curlHandle, CURLOPT_PROXY, $this->proxy);
			curl_setopt($this->curlHandle, CURLOPT_PROXYUSERPWD, $this->proxyAuth);
		}
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curlHandle, CURLOPT_HTTP_VERSION, '1.1');
        curl_setopt($this->curlHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($this->curlHandle,CURLOPT_CUSTOMREQUEST, $method);

        if ($method == 'POST') {
            curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
}