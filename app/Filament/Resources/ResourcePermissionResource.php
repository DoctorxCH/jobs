<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResourcePermissionResource\Pages;
use App\Models\ResourcePermission;
use App\Services\PermissionService;
use Filament\Facades\Filament;
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
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class ResourcePermissionResource extends Resource
{
    protected static ?string $model = ResourcePermission::class;

    protected static ?string $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'Permission Settings';
    protected static ?string $modelLabel = 'Permission Setting';
    protected static ?string $pluralModelLabel = 'Permission Settings';
    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

    public static function getPermissionKey(): string
    {
        return 'resource_permissions';
    }

    public static function canViewAny(): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'view');
    }

    public static function canCreate(): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'create');
    }

    public static function canEdit($record): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'edit');
    }

    public static function canDelete($record): bool
    {
        return PermissionService::can(static::getPermissionKey(), 'delete');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('resource')
                    ->label('Resource')
                    ->options(self::resourceOptions())
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(),

                Select::make('role_name')
                    ->label('Role')
                    ->options(self::roleOptions())
                    ->searchable()
                    ->preload()
                    ->required()
                    ->rule(function (callable $get, ?ResourcePermission $record) {
                        return Rule::unique('resource_permissions', 'role_name')
                            ->where(fn ($q) => $q->where('resource', $get('resource')))
                            ->ignore($record?->id);
                    })
                    ->validationMessages([
                        'unique' => 'Diese Kombination aus Resource und Role existiert bereits.',
                    ]),

                Toggle::make('can_view')->label('Can View'),
                Toggle::make('can_create')->label('Can Create'),
                Toggle::make('can_edit')->label('Can Edit'),
                Toggle::make('can_delete')->label('Can Delete'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('resource')->sortable()->searchable(),
                TextColumn::make('role_name')->label('Role')->sortable()->searchable(),
                IconColumn::make('can_view')->label('View')->boolean(),
                IconColumn::make('can_create')->label('Create')->boolean(),
                IconColumn::make('can_edit')->label('Edit')->boolean(),
                IconColumn::make('can_delete')->label('Delete')->boolean(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('resource')->options(self::resourceOptions()),
                SelectFilter::make('role_name')->label('Role')->options(self::roleOptions()),
            ])
            ->actions([
                EditAction::make()
                    ->after(fn () => PermissionService::invalidateCache()),
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

    /**
     * Dynamisch: alle registrierten Filament Resources aus dem aktuellen Panel.
     * Key = Resource::getPermissionKey() falls vorhanden, sonst Resource::getSlug().
     */
    private static function resourceOptions(): array
    {
        $keys = [];

        try {
            $panel = Filament::getCurrentPanel();
            $resources = $panel?->getResources() ?? [];

            foreach ($resources as $resourceClass) {
                if (! is_string($resourceClass) || ! class_exists($resourceClass)) {
                    continue;
                }

                // bevorzugt: expliziter PermissionKey
                if (method_exists($resourceClass, 'getPermissionKey')) {
                    $key = (string) $resourceClass::getPermissionKey();
                } else {
                    // fallback: slug (z.B. "platform-users" -> "platform_users")
                    $slug = method_exists($resourceClass, 'getSlug')
                        ? (string) $resourceClass::getSlug()
                        : class_basename($resourceClass);

                    $key = Str::of($slug)->replace('-', '_')->toString();
                }

                $key = trim($key);
                if ($key !== '') {
                    $keys[] = $key;
                }
            }
        } catch (\Throwable $e) {
            // Wenn Filament Context nicht verfügbar ist, nicht crashen.
            // Dann nur Fallback keys anbieten.
        }

        // Pflicht: sich selbst steuerbar machen
        $keys[] = 'resource_permissions';

        // Optional: zusätzliche Keys ohne eigene Resource (falls du willst)
        $extra = [
            // 'company_invitations',
            // 'taxonomy_terms',
        ];
        $keys = array_values(array_unique(array_merge($keys, $extra)));
        sort($keys);

        return array_combine($keys, $keys);
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
