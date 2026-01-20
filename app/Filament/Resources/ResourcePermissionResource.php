<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResourcePermissionResource\Pages;
use App\Models\ResourcePermission;
use App\Services\PermissionService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class ResourcePermissionResource extends Resource
{
    protected static ?string $model = ResourcePermission::class;

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Permission Settings';

    protected static ?string $modelLabel = 'Permission Setting';

    protected static ?string $pluralModelLabel = 'Permission Settings';

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('resource')
                    ->label('Resource')
                    ->options(self::resourceOptions())
                    ->required(),
                Select::make('role_name')
                    ->label('Role')
                    ->options(self::roleOptions())
                    ->searchable()
                    ->required(),
                Toggle::make('can_view')
                    ->label('Can View'),
                Toggle::make('can_create')
                    ->label('Can Create'),
                Toggle::make('can_edit')
                    ->label('Can Edit'),
                Toggle::make('can_delete')
                    ->label('Can Delete'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('resource')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('role_name')
                    ->label('Role')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('can_view')
                    ->label('View')
                    ->boolean(),
                IconColumn::make('can_create')
                    ->label('Create')
                    ->boolean(),
                IconColumn::make('can_edit')
                    ->label('Edit')
                    ->boolean(),
                IconColumn::make('can_delete')
                    ->label('Delete')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('resource')
                    ->options(self::resourceOptions()),
                SelectFilter::make('role_name')
                    ->label('Role')
                    ->options(self::roleOptions()),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->after(fn () => PermissionService::invalidateCache()),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->after(fn () => PermissionService::invalidateCache()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResourcePermissions::route('/'),
            'create' => Pages\CreateResourcePermission::route('/create'),
            'edit' => Pages\EditResourcePermission::route('/{record}/edit'),
        ];
    }

    private static function resourceOptions(): array
    {
        return [
            'companies' => 'companies',
            'company_users' => 'company_users',
            'platform_users' => 'platform_users',
            'jobs' => 'jobs',
            'company_categories' => 'company_categories',
        ];
    }

    private static function roleOptions(): array
    {
        return Role::query()
            ->where('name', 'like', 'platform.%')
            ->orderBy('name')
            ->pluck('name', 'name')
            ->all();
    }
}
