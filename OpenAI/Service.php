<?php

namespace Abramovku\OpenAI;

class Service
{
    private $client;

    public function __construct(string $token)
    {
        if (!empty($this->token)) {
            throw new Exception('Provide Open AI token');
        }
        $this->client = new Client($token);
    }

    public function sendPrompt(string $prompt): string
    {
        $result = $this->client->post('chat/completions', $this->preparePrompt($prompt));

        if (empty($result)) {
            throw new Exception('Wrong response from OpenAI');
        }

        if (!empty($result['error'])) {
            throw new Exception($result['error']['message'] . $result['error']['code']);
        }

        if (empty($result['choices'][0]['message']['content'])) {
            throw new Exception('Empty response from OpenAI');
        }

        return $this->sanitizeText($result['choices'][0]['message']['content']);
    }

    private function sanitizeText(string $text): string
    {
        $text = trim(htmlspecialchars($text));
        $text = str_replace(["&quot;", "&#039;"], ["&#34;", "&#39;"], $text);

        return html_entity_decode($text);
    }

    private function preparePrompt(string $prompt, ?string $prefix = null ): array
    {
        $prompt = $this->sanitizeText($prompt);
        if ($prefix === null) {
            $prefix = "Подготовь мне описание на русском языке для товара в интернет магазине: ";
        }

        return [
            "model" => "gpt-3.5-turbo",
            "messages" => [
                [
                    "role" => "user",
                    "content" => $prefix . $prompt
                ]
            ]
        ];
    }


}