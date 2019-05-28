<?php

class HttpLite {

    private const GET = 1;
    private const POST = 2;
    private const PUT = 3;


    private $verbose = false;
    private $headers = [];
    private $ignoreSSL = false;
    private $url = '';
    private $method = self::GET;
    private $payload = '';
    private $payloadArray = [];
    private $response;

    public function setHeader($key, $value) {
        $this->headers[] = "$key: $value";
        return $this;
    }

    public function ignoreSSL() {
        $this->ignoreSSL = true;
        return $this;
    }

    public function verbose() {
        $this->verbose = true;
        return $this;
    }

    public function url($url) {
        $this->url = $url;
        return $this;
    }

    public function bodyArray ($payloadArray) {
        $this->payloadArray = $payloadArray;
        return $this;
    }

    public function jsonBody() {
        $this->headers[] = 'Content-Type: application/json';
        $this->payload = json_encode($this->payloadArray);
        $this->headers[] = 'Content-Length: ' . strlen($this->payload);
        return $this;
    }

    public function postMethod() {
        $this->method = self::POST;
        return $this;
    }

    public function putMethod() {
        $this->method = self::PUT;
        return $this;
    }

    public function send() {
        $ch = curl_init();

        if ($this->hasHeaders()) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        }

        $options = [
            CURLOPT_URL            => $this->url,
            CURLOPT_HEADER         => $this->hasHeaders(),
            CURLOPT_VERBOSE        => $this->verbose,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => !$this->ignoreSSL,
            CURLOPT_POST => $this->method === HTTP_METH_POST,
            CURLOPT_PUT => $this->method === HTTP_METH_PUT,
            CURLOPT_POSTFIELDS => $this->payload,
        ];

        curl_setopt_array($ch, $options);

        $this->response = curl_exec($ch) ?? null;

        return $this;
    }

    private function hasHeaders () {
        return !empty($this->headers);
    }
}