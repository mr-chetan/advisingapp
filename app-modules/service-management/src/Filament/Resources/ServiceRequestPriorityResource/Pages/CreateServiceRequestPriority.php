<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource;

class CreateServiceRequestPriority extends CreateRecord
{
    protected static string $resource = ServiceRequestPriorityResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->string(),
                TextInput::make('order')
                    ->label('Priority Order')
                    ->required()
                    ->integer()
                    ->numeric(),
            ]);
    }
}
