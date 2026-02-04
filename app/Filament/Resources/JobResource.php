<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobResource\Pages;
use App\Models\City;
use App\Models\Country;
use App\Models\Job;
use App\Models\Region;
use App\Services\PermissionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static ?string $navigationGroup = 'Jobs';
    protected static ?string $navigationLabel = 'Jobs';
    protected static ?string $modelLabel = 'Job';
    protected static ?string $pluralModelLabel = 'Jobs';
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?int $navigationSort = 10;

    public static function getPermissionKey(): string
    {
        return 'jobs';
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
        return $form->schema([
            Forms\Components\Section::make('Basics')
                ->schema([
                    Forms\Components\Select::make('company_id')
                        ->relationship('company', 'legal_name')
                        ->searchable()
                        ->required(),

                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Textarea::make('description')
                        ->required()
                        ->rows(6),

                    Forms\Components\Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'published' => 'Published',
                            'archived' => 'Archived',
                        ])
                        ->required(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Position & Workload')
                ->schema([
                    Forms\Components\Select::make('sknace_position_id')
                        ->relationship('sknacePosition', 'title')
                        ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->code} - {$record->title}")
                        ->required()
                        ->searchable(),

                    Forms\Components\Select::make('employment_type')
                        ->options([
                            'full_time' => 'Full time',
                            'part_time' => 'Part time',
                            'contract' => 'Contract',
                            'freelance' => 'Freelance',
                            'internship' => 'Internship',
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('workload_min')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->required(),

                    Forms\Components\TextInput::make('workload_max')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->required(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Location')
                ->schema([
                    Forms\Components\Select::make('country_id')
                        ->label('Country')
                        ->options(fn () => Country::query()->orderBy('name')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->preload()
                        ->nullable()      // not required
                        ->reactive()
                        ->afterStateUpdated(function (callable $set) {
                            $set('region_id', null);
                            $set('city_id', null);
                        }),

                    Forms\Components\Select::make('region_id')
                        ->label('Region')
                        ->options(function (callable $get) {
                            $countryId = $get('country_id');
                            if (! $countryId) {
                                return [];
                            }

                            return Region::query()
                                ->where('country_id', $countryId)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->searchable()
                        ->preload()
                        ->nullable()      // not required
                        ->reactive()
                        ->afterStateUpdated(function (callable $set) {
                            $set('city_id', null);
                        })
                        ->disabled(fn (callable $get) => blank($get('country_id'))),

                    Forms\Components\Select::make('city_id')
                        ->label('City')
                        ->options(function (callable $get) {
                            $regionId = $get('region_id');
                            if (! $regionId) {
                                return [];
                            }

                            return City::query()
                                ->where('region_id', $regionId)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->searchable()
                        ->preload()
                        ->nullable() // not required
                        ->disabled(fn (callable $get) => blank($get('region_id'))),

                    Forms\Components\Toggle::make('is_remote'),
                    Forms\Components\Toggle::make('is_hybrid'),
                    Forms\Components\Toggle::make('travel_required'),
                ])
                ->columns(3),

            Forms\Components\Section::make('Timing & Salary')
                ->schema([
                    Forms\Components\DatePicker::make('available_from'),
                    Forms\Components\DatePicker::make('application_deadline'),
                    Forms\Components\TextInput::make('salary_min_gross_month')->numeric(),
                    Forms\Components\TextInput::make('salary_max_gross_month')->numeric(),
                    Forms\Components\TextInput::make('salary_currency')->maxLength(3),
                    Forms\Components\TextInput::make('salary_note')->maxLength(255),
                    Forms\Components\Select::make('salary_months')->options(['12' => '12', '13' => '13']),
                ])
                ->columns(3),

            Forms\Components\Section::make('Requirements')
                ->schema([
                    Forms\Components\Select::make('education_level_id')
                        ->relationship('educationLevel', 'label')
                        ->searchable(),

                    Forms\Components\Select::make('education_field_id')
                        ->relationship('educationField', 'label')
                        ->searchable(),

                    Forms\Components\TextInput::make('min_years_experience')->numeric(),
                    Forms\Components\Toggle::make('is_for_graduates'),
                    Forms\Components\Toggle::make('is_for_disabled'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Recruiting')
                ->schema([
                    Forms\Components\TextInput::make('open_positions')->numeric()->default(1),
                    Forms\Components\Textarea::make('candidate_note')->rows(3),
                    Forms\Components\TextInput::make('employer_reference')->maxLength(80),
                    Forms\Components\TextInput::make('hr_email')->email(),
                    Forms\Components\TextInput::make('hr_phone')->maxLength(50),
                ])
                ->columns(2),

            Forms\Components\Section::make('Relations')
                ->schema([
                    Forms\Components\Select::make('benefits')
                        ->relationship('benefits', 'label')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->options(function () {
                            return \App\Models\Benefit::all()
                                ->groupBy(fn($b) => str_contains($b->label, '/') ? trim(explode('/', $b->label)[0]) : __('main.others'))
                                ->map(fn($items) => $items->pluck('label', 'id')->map(function($label) {
                                    return str_contains($label, '/') ? trim(explode('/', $label, 2)[1]) : $label;
                                }))
                                ->toArray();
                        }),

                    Forms\Components\Select::make('drivingLicenseCategories')
                        ->relationship('drivingLicenseCategories', 'label')
                        ->multiple()
                        ->searchable(),

                    Forms\Components\Repeater::make('jobLanguages')
                        ->relationship()
                        ->schema([
                            Forms\Components\TextInput::make('language_code')->required()->maxLength(2),
                            Forms\Components\Select::make('level')
                                ->options([
                                    'A1' => 'A1', 'A2' => 'A2', 'B1' => 'B1', 'B2' => 'B2',
                                    'C1' => 'C1', 'C2' => 'C2', 'native' => 'Native',
                                ])
                                ->required(),
                        ])
                        ->columns(2),

                    Forms\Components\Repeater::make('jobSkills')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('skill_id')
                                ->relationship('skill', 'name')
                                ->searchable()
                                ->required(),
                            Forms\Components\Select::make('level')
                                ->options([
                                    'basic' => 'Basic',
                                    'intermediate' => 'Intermediate',
                                    'advanced' => 'Advanced',
                                    'expert' => 'Expert',
                                ])
                                ->required(),
                        ])
                        ->columns(2),
                ])
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('company.legal_name')->label('Company')->sortable(),
                Tables\Columns\TextColumn::make('status')->sortable(),
                Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('expires_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'published' => 'Published',
                    'archived' => 'Archived',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(fn () => static::canEdit(null)),
                Tables\Actions\DeleteAction::make()->visible(fn () => static::canDelete(null)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(fn () => static::canDelete(null)),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobs::route('/'),
            'create' => Pages\CreateJob::route('/create'),
            'edit' => Pages\EditJob::route('/{record}/edit'),
        ];
    }
}
