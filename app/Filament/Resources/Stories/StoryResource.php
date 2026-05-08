<?php

namespace App\Filament\Resources\Stories;

use App\Filament\Resources\Stories\Pages\CreateStory;
use App\Filament\Resources\Stories\Pages\EditStory;
use App\Filament\Resources\Stories\Pages\ListStories;
use App\Models\Story;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class StoryResource extends Resource
{
    protected static ?string $model = Story::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static string|\UnitEnum|null $navigationGroup = 'เนื้อหา';

    protected static ?string $navigationLabel = 'Stories / Journal';

    protected static ?string $modelLabel = 'Story';

    protected static ?string $pluralModelLabel = 'Stories';

    protected static ?int $navigationSort = 12;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('เผยแพร่')
                ->schema([
                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->rules(['alpha_dash'])
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, \Filament\Forms\Set $set) => $set('slug', Str::slug((string) $state))),
                    FileUpload::make('cover_image')
                        ->label('Cover image')
                        ->image()
                        ->directory('stories')
                        ->disk('public')
                        ->visibility('public'),
                    DateTimePicker::make('published_at')->label('วันที่เผยแพร่'),
                    Toggle::make('is_published')->label('เผยแพร่')->default(true),
                    TextInput::make('sort_order')->label('ลำดับ')->numeric()->default(0),
                ])
                ->columns(2),
            Section::make('เนื้อหา')
                ->schema([
                    Repeater::make('translations')
                        ->relationship()
                        ->schema([
                            Select::make('locale')->label('ภาษา')->options(config('chomin.locales.labels'))->required(),
                            TextInput::make('title')->label('หัวข้อ')->required()->maxLength(255),
                            Textarea::make('excerpt')->label('คำโปรย')->columnSpanFull(),
                            RichEditor::make('body')->label('บทความ')->columnSpanFull(),
                            TextInput::make('seo_title')->label('SEO title'),
                            Textarea::make('seo_description')->label('SEO description')->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->defaultItems(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')->label('Cover')->disk('public'),
                TextColumn::make('slug')->label('Slug')->searchable()->sortable(),
                IconColumn::make('is_published')->label('เผยแพร่')->boolean(),
                TextColumn::make('published_at')->label('เผยแพร่เมื่อ')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('sort_order')->label('ลำดับ')->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStories::route('/'),
            'create' => CreateStory::route('/create'),
            'edit' => EditStory::route('/{record}/edit'),
        ];
    }
}
