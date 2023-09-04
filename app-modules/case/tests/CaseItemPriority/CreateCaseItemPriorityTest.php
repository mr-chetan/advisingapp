<?php

use App\Models\User;
use Assist\Case\Models\ServiceRequestPriority;
use Assist\Case\Filament\Resources\CaseItemPriorityResource;
use Assist\Case\Tests\RequestFactories\CreateCaseItemPriorityRequestFactory;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

test('A successful action on the CreateCaseItemPriority page', function () {
    asSuperAdmin()
        ->get(
            CaseItemPriorityResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = CreateCaseItemPriorityRequestFactory::new()->create();

    livewire(CaseItemPriorityResource\Pages\CreateCaseItemPriority::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestPriority::all());

    assertDatabaseHas(ServiceRequestPriority::class, $request);
});

test('CreateCaseItemPriority requires valid data', function ($data, $errors) {
    asSuperAdmin();

    livewire(CaseItemPriorityResource\Pages\CreateCaseItemPriority::class)
        ->fillForm(CreateCaseItemPriorityRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(ServiceRequestPriority::all());
})->with(
    [
        'name missing' => [CreateCaseItemPriorityRequestFactory::new()->without('name'), ['name' => 'required']],
        'name not a string' => [CreateCaseItemPriorityRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'order missing' => [CreateCaseItemPriorityRequestFactory::new()->without('order'), ['order' => 'required']],
        'order not a number' => [CreateCaseItemPriorityRequestFactory::new()->state(['order' => 'a']), ['order' => 'numeric']],
    ]
);

// Permission Tests

test('CreateCaseItemPriority is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            CaseItemPriorityResource::getUrl('create')
        )->assertForbidden();

    livewire(CaseItemPriorityResource\Pages\CreateCaseItemPriority::class)
        ->assertForbidden();

    $user->givePermissionTo('case_item_priority.view-any');
    $user->givePermissionTo('case_item_priority.create');

    actingAs($user)
        ->get(
            CaseItemPriorityResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateCaseItemPriorityRequestFactory::new()->create());

    livewire(CaseItemPriorityResource\Pages\CreateCaseItemPriority::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequestPriority::all());

    assertDatabaseHas(ServiceRequestPriority::class, $request->toArray());
});
