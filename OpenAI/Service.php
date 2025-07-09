<?php

namespace Abramovku\OpenAI;

class Service
{
    private $client;

    public function __construct(string $token, array $proxy = [])
    {
        if (!empty($this->token)) {
            throw new Exception('Provide Open AI token');
        }
        $this->client = new Client($token, $proxy);
    }

    public function sendPrompt(string $prompt, ?string $smartTv = null, ?string $model = null): string
    {
        $result = $this->client->post('chat/completions',
            $this->preparePrompt($prompt, null, null, $smartTv, $model ));

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

	private function preparePrompt(
		string $prompt,
        ?string $prefix = null,
        ?string $suffix = null,
        ?string $smartTv = null,
        ?string $model = null

	): array
	{
        if (empty($model)) {
            $model = "gpt-3.5-turbo";
        }
		$prompt = $this->sanitizeText($prompt);
		if ($prefix === null) {
			$prefix = "Подготовь мне описание на русском языке для товара в интернет магазине: ";
		}

		if ($suffix === null) {
			$suffix = "";
		}

		if ($smartTv === 'true'){
			$smartText = '. Укажи о наличии функции Smart TV';
		} elseif ($smartTv === 'false') {
			$smartText = '. Не указывай информацию о наличии Smart TV';
		} else {
            $smartText = "";
        }

		return [
			"model" => $model,
			"messages" => [
				[
					"role" => "user",
					"content" => $prefix . $prompt . $suffix . $smartText,
				]
			]
		];
	}

	public function sendPromptWithPrefix(string $prompt, string $prefix, string $suffix, ?string $model = null): string
	{
		$result = $this->client->post('chat/completions',
            $this->preparePrompt($prompt, $prefix, $suffix, null, $model)
        );

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
}