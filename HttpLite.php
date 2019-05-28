<?php

/**
 * Class HttpLite
 * @author Leonhard Radonic
 * @email leonhard.radonic@gmail.com
 * @linkedin https://www.linkedin.com/in/leonhard-radonic-832228ba/
 */

class HttpLite {

    private const GET = 1;
    private const POST = 2;
    private const PUT = 3;

    private $verbose = false;
    private $headers = [];
    private $ignoreSSL = false;
    private $url = '';
    private $method = self::GET;
    private $payloadAsString = '';
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

    public function setJsonBody($payloadArray) {
        $this->headers[] = 'Content-Type: application/json';
        $this->payloadAsString = json_encode($payloadArray);
        $this->headers[] = 'Content-Length: ' . strlen($this->payloadAsString);
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

    public function getResponseAsJson() {
        $response = json_decode($this->response, true);

        if ($response === NULL) {
            return null;
        }

        return $response;
    }

    public function send() {
        if (empty($this->url)) {
            throw new ErrorException("No URL set");
        }

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
            CURLOPT_POSTFIELDS => $this->payloadAsString,
        ];

        curl_setopt_array($ch, $options);

        $this->response = curl_exec($ch) ?? null;

        curl_close($ch);

        return $this;
    }

    private function hasHeaders () {
        return !empty($this->headers);
    }
}