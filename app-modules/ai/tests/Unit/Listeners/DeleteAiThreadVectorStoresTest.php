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

use Mockery\MockInterface;
use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Events\AiThreadForceDeleting;
use AdvisingApp\Ai\Listeners\DeleteAiThreadVectorStores;
use AdvisingApp\IntegrationOpenAi\DataTransferObjects\Threads\ThreadsDataTransferObject;

it('deletes vector stores for a thread', function () {
    $aiThread = AiThread::factory()
        ->for(
            factory: AiAssistant::factory()->state(['model' => AiModel::OpenAiGpt4o]),
            relationship: 'assistant',
        )
        ->create();

    /** @phpstan-ignore-next-line */
    $this->mock(
        $aiThread->assistant->model->getService()::class,
        fn (MockInterface $mock) => $mock
            ->shouldReceive('supportsMessageFileUploads')->once()->andReturn(true)
            ->shouldReceive('retrieveThread')->once()->andReturn(ThreadsDataTransferObject::from([
                'id' => 1,
                'vectorStoreIds' => [1, 2, 3],
            ]))
            ->shouldReceive('deleteVectorStore')->times(3),
    );

    (new DeleteAiThreadVectorStores())->handle(new AiThreadForceDeleting($aiThread));
});
