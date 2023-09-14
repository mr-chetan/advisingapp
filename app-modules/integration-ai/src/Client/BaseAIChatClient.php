<?php

namespace Assist\IntegrationAI\Client;

use Closure;
use OpenAI\Client;
use OpenAI\Testing\ClientFake;
use OpenAI\Responses\StreamResponse;
use Assist\IntegrationAI\Settings\AISettings;
use OpenAI\Responses\Chat\CreateStreamedResponse;
use Assist\IntegrationAI\Events\AIPromptInitiated;
use Assist\IntegrationAI\DataTransferObjects\AIPrompt;
use Assist\IntegrationAI\Client\Contracts\AIChatClient;
use Assist\IntegrationAI\Client\Concerns\InitializesClient;
use Assist\IntegrationAI\Exceptions\ContentFilterException;
use Assist\IntegrationAI\DataTransferObjects\DynamicContext;
use Assist\IntegrationAI\Exceptions\TokensExceededException;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\Chat;

abstract class BaseAIChatClient implements AIChatClient
{
    use InitializesClient;

    protected string $baseEndpoint;

    protected string $apiKey;

    protected string $apiVersion;

    protected string $deployment;

    protected ?string $dynamicContext = null;

    protected ?string $systemContext = null;

    protected Client|ClientFake $client;

    public function __construct(
    ) {
        $this->initializeClient();
    }

    public function ask(Chat $chat, ?Closure $callback): string
    {
        if (is_null($this->systemContext)) {
            $this->setSystemContext();
        }

        $this->dispatchPromptInitiatedEvent($chat);

        /** @var StreamResponse $stream */
        $stream = $this->client->chat()->createStreamed([
            'messages' => $this->formatMessagesFromChat($chat),
        ]);

        return $this->generateStreamedResponse($stream, $callback);
    }

    public function provideDynamicContext(DynamicContext $context): self
    {
        $this->setDynamicContext($context->context);

        return $this;
    }

    protected function generateStreamedResponse(StreamResponse $stream, Closure $callback): string
    {
        $fullResponse = '';

        foreach ($stream as $response) {
            $streamedContent = $this->shouldSendResponse($response);

            if (! is_null($streamedContent)) {
                $callback($streamedContent);

                $fullResponse .= $streamedContent;
            }
        }

        return $fullResponse;
    }

    protected function setSystemContext(): void
    {
        $this->systemContext = resolve(AISettings::class)->prompt_context;
    }

    protected function setDynamicContext(string $context): void
    {
        $this->dynamicContext = $context;
    }

    protected function shouldSendResponse(CreateStreamedResponse $response): ?string
    {
        if ($response->choices[0]) {
            $this->examineFinishReason($response);
        }

        return $response->choices[0]?->delta?->content ?: null;
    }

    protected function examineFinishReason(CreateStreamedResponse $response): void
    {
        match ($response->choices[0]->finishReason) {
            'length' => throw new TokensExceededException('Your response was not successfully generated due to the max_tokens parameter or token limit being exceeded.'),
            'content_filter' => throw new ContentFilterException('Your response was not successfully generated due to a flag from our content filters.'),
            default => null,
        };
    }

    protected function formatMessagesFromChat(Chat $chat): array
    {
        return [
            ['role' => 'system', 'content' => $this->addContextToMessages()],
            ...collect($chat->messages)->map(function (array $message) {
                return [
                    'role' => $message['from'],
                    'content' => $message['message'],
                ];
            }),
        ];
    }

    protected function addContextToMessages(): string
    {
        return $this->systemContext . ' ' . $this->dynamicContext;
    }

    protected function dispatchPromptInitiatedEvent(Chat $chat): void
    {
        AIPromptInitiated::dispatch(AIPrompt::from([
            'user' => auth()->user(),
            'request' => request(),
            'timestamp' => now(),
            'message' => $chat->messages->last()->message,
            'metadata' => [
                'systemContext' => $this->systemContext,
            ],
        ]));
    }
}
