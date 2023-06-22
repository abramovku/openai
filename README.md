# openai

#how to use:

```php
<?php

require_once __DIR__ . '/OpenAI/Exception.php';
require_once __DIR__ . '/OpenAI/Client.php';
require_once __DIR__ . '/OpenAI/Service.php';

try {
    $service = new \Abramovku\OpenAI\Service(token);
    $result = $service->sendPrompt('sony walkman');
    //do something with result
} catch (\Abramovku\OpenAI\Exception $e) {
    //do something
    throw $e;
}
```