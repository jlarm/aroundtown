<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\State;
use App\Filament\Resources\LocationResource\Pages;
use App\Models\Location;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

final class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $slug = 'locations';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug($state))),

                                TextInput::make('slug')
                                    ->required()
                                    ->unique(Location::class, 'slug', fn ($record) => $record),
                            ])->columns(2),
                        Section::make('Descriptions')
                            ->schema([
                                TextInput::make('short_description')
                                    ->required(),
                                RichEditor::make('description'),
                            ]),
                        Section::make('Address')
                            ->schema([
                                TextInput::make('address')
                                    ->columnSpanFull(),
                                TextInput::make('city'),
                                Select::make('state')
                                    ->options(State::class),
                                TextInput::make('zip')
                                    ->label('Zip Code'),
                            ])
                            ->columns(3),
                    ])->columnSpan(2),
                Group::make()
                    ->schema([
                        Section::make('Status')
                            ->schema([
                                ToggleButtons::make('status')
                                    ->label('Publish Location?')
                                    ->boolean()
                                    ->default(false)
                                    ->grouped(),
                            ]),
                        Section::make()
                            ->schema([
                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->mask('999-999-9999')
                                    ->tel(),
                            ]),
                        Section::make('Links')
                            ->schema([
                                TextInput::make('url')
                                    ->url()
                                    ->label('Website URL'),
                                TextInput::make('menu_url')
                                    ->url()
                                    ->label('Menu URL'),
                                TextInput::make('directions_url')
                                    ->url()
                                    ->label('Directions URL'),
                            ]),
                        Section::make('Categories')
                            ->schema([
                                Select::make('categories')
                                    ->relationship('categories', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug($state))),
                                        TextInput::make('slug')
                                            ->required(),
                                    ]),
                            ]),
                        Section::make()
                            ->schema([
                                FileUpload::make('image_path')
                                    ->label('Feature Image')
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('location-images')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('3:2')
                                    ->imageResizeTargetWidth('1920')
                                    ->imageResizeTargetHeight('1280')
                                    ->storeFileNamesIn('image_original_name'),
                            ]),
                    ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                ToggleColumn::make('status'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug'];
    }
}
