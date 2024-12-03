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

namespace AdvisingApp\CaseManagement\Filament\Resources\CaseFormResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\CaseManagement\Models\CaseForm;
use AdvisingApp\Form\Actions\GenerateSubmissibleEmbedCode;
use AdvisingApp\CaseManagement\Filament\Resources\CaseFormResource;
use AdvisingApp\CaseManagement\Filament\Resources\CaseFormResource\Pages\Concerns\HasSharedFormConfiguration;

class EditCaseForm extends EditRecord
{
    use HasSharedFormConfiguration;

    protected static string $resource = CaseFormResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->fields());
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')
                ->url(fn (CaseForm $caseForm) => route('case-forms.show', ['caseForm' => $caseForm]))
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->openUrlInNewTab(),
            Action::make('embed_snippet')
                ->label('Embed Snippet')
                ->infolist(
                    [
                        TextEntry::make('snippet')
                            ->label('Click to Copy')
                            ->state(function (CaseForm $caseForm) {
                                $code = resolve(GenerateSubmissibleEmbedCode::class)->handle($caseForm);

                                $state = <<<EOD
                                ```
                                {$code}
                                ```
                                EOD;

                                return str($state)->markdown()->toHtmlString();
                            })
                            ->copyable()
                            ->copyableState(fn (CaseForm $caseForm) => resolve(GenerateSubmissibleEmbedCode::class)->handle($caseForm))
                            ->copyMessage('Copied!')
                            ->copyMessageDuration(1500)
                            ->extraAttributes(['class' => 'embed-code-snippet']),
                    ]
                )
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->hidden(fn (CaseForm $caseForm) => ! $caseForm->embed_enabled),
            DeleteAction::make(),
        ];
    }
}