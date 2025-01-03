<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\IntegrationTwilio\Jobs\CheckSmsOutboundDeliverableStatus;
use AdvisingApp\IntegrationTwilio\Settings\TwilioSettings;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use Tests\Unit\ClientMock;
use Twilio\Rest\Api\V2010;
use Twilio\Rest\Api\V2010\Account\MessageContext;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;
use Twilio\Rest\MessagingBase;

it('will update the status of an outbound deliverable accordingly', function (string $externalStatus) {
    $settings = app()->make(TwilioSettings::class);

    $settings->account_sid = 'abc123';
    $settings->auth_token = 'abc123';
    $settings->from_number = '+11231231234';

    $settings->save();

    // Given that we have an outbound deliverable with a non terminal status
    $outboundDeliverable = OutboundDeliverable::factory()->create([
        'channel' => 'sms',
        'external_status' => 'queued',
        'external_reference_id' => 'abc123',
    ]);

    $originalStatus = $outboundDeliverable->delivery_status;

    $clientMock = mock(ClientMock::class)
        ->shouldAllowMockingProtectedMethods();

    $mockMessageContext = mock(MessageContext::class);

    $clientMock->shouldReceive('messages')
        ->with('abc123')
        ->andReturn($mockMessageContext);

    $mockMessageContext->shouldReceive('fetch')->andReturn(
        new MessageInstance(
            new V2010(new MessagingBase(new Client(username: $settings->account_sid, password: $settings->auth_token))),
            [
                'sid' => 'abc123',
                'status' => $externalStatus,
                'from' => '+11231231234',
                'to' => '+11231231234',
                'body' => 'test',
                'num_segments' => 1,
            ],
            'abc123'
        )
    );

    app()->bind(Client::class, fn () => $clientMock);

    // And we reach out to Twilio to check on the status of the message because we may have missed a webhook
    CheckSmsOutboundDeliverableStatus::dispatchSync($outboundDeliverable);

    $outboundDeliverable->refresh();

    // Our delivery status should be updated based on the status we received from Twilio
    if ($externalStatus === 'delivered') {
        expect($outboundDeliverable->delivery_status)->toBe(NotificationDeliveryStatus::Successful);
    } elseif ($externalStatus === 'undelivered' || $externalStatus === 'failed') {
        expect($outboundDeliverable->delivery_status)->toBe(NotificationDeliveryStatus::Failed);
    } else {
        expect($outboundDeliverable->delivery_status)->toBe($originalStatus);
    }
})->with([
    'queued',
    'sent',
    'delivered',
    'undelivered',
    'failed',
]);
