<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class HasMinOperator extends Operator
{
    public function getName(): string
    {
        return 'hasMin';
    }

    public function getLabel(): string
    {
        return $this->isInverse() ? 'Has less than' : 'Has minimum';
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('count')
                ->integer()
                ->required()
                ->minValue(1),
        ];
    }

    public function getSummary(): string
    {
        return $this->isInverse() ? "Has less than {$this->getSettings()['count']} {$this->getConstraint()->getAttributeLabel()}" : "Has minimum {$this->getSettings()['count']} {$this->getConstraint()->getAttributeLabel()}";
    }

    public function applyToBaseQuery(Builder $query): Builder
    {
        return $query->has($this->getConstraint()->getRelationshipName(), $this->isInverse() ? '<' : '>=', intval($this->getSettings()['count']));
    }
}