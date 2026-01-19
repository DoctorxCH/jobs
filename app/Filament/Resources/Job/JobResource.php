<?php

namespace App\Filament\Resources\Job;

use App\Filament\Resources\Job\JobResource\Pages;
use App\Filament\Resources\Job\Schemas\JobForm;
use App\Filament\Resources\Job\Tables\JobTable;
use App\Models\Job;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static ?string $navigationLabel = 'Jobs';
    protected static ?string $modelLabel = 'Job';
    protected static ?string $pluralModelLabel = 'Jobs';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Schema $schema): Schema
    {
        return JobForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return JobTable::schema($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobs::route('/'),
            'create' => Pages\CreateJob::route('/create'),
            'edit' => Pages\EditJob::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        if (! $user) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        if ($user->company_id) {
            return parent::getEloquentQuery()->where('company_id', $user->company_id);
        }

        return parent::getEloquentQuery()->whereRaw('1 = 0');
    }
}
