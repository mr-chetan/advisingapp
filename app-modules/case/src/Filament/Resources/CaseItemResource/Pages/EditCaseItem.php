<?php

namespace Assist\Case\Filament\Resources\CaseItemResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use App\Models\Institution;
use Assist\Case\Models\CaseItemType;
use Filament\Forms\Components\Select;
use Assist\Case\Models\CaseItemStatus;
use Filament\Forms\Components\Textarea;
use Assist\Case\Models\CaseItemPriority;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Assist\Case\Filament\Resources\CaseItemResource;

class EditCaseItem extends EditRecord
{
    protected static string $resource = CaseItemResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)->schema([
            TextInput::make('id')
                ->disabled(),
            TextInput::make('casenumber')
                ->label('Case #')
                ->disabled(),
            Select::make('institution')
                ->relationship('institution', 'name')
                ->label('Institution')
                ->required()
                ->exists((new Institution())->getTable(), 'id'),
            Select::make('state')
                ->relationship('state', 'name')
                ->preload()
                ->label('State')
                ->required()
                ->exists((new CaseItemStatus())->getTable(), 'id'),
            Select::make('priority')
                ->relationship(
                    name: 'priority',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query) => $query->orderBy('order'),
                )
                ->label('Priority')
                ->required()
                ->exists((new CaseItemPriority())->getTable(), 'id'),
            Select::make('type')
                ->relationship('type', 'name')
                ->preload()
                ->label('Type')
                ->required()
                ->exists((new CaseItemType())->getTable(), 'id'),
            Textarea::make('close_details')
                ->label('Close Details/Description')
                ->nullable()
                ->string(),
            Textarea::make('res_details')
                ->label('Internal Case Details')
                ->nullable()
                ->string(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
