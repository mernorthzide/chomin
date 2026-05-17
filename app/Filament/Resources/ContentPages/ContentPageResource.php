<?php

namespace App\Filament\Resources\ContentPages;

use App\Filament\Resources\ContentPages\Pages\CreateContentPage;
use App\Filament\Resources\ContentPages\Pages\EditContentPage;
use App\Filament\Resources\ContentPages\Pages\ListContentPages;
use App\Models\ContentPage;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ContentPageResource extends Resource
{
    protected static ?string $model = ContentPage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'เนื้อหา';

    protected static ?string $navigationLabel = 'หน้าเนื้อหา';

    protected static ?string $modelLabel = 'หน้าเนื้อหา';

    protected static ?string $pluralModelLabel = 'หน้าเนื้อหา';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('โครงหน้า')
                ->schema([
                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug((string) $state))),
                    Select::make('template')
                        ->label('Template')
                        ->options([
                            'default' => 'Default',
                            'legal' => 'Legal / Trust',
                            'policy' => 'Policy',
                            'size-guide' => 'Size guide',
                            'member' => 'Member',
                            'gift-cards' => 'Gift cards',
                            'form' => 'Form page',
                        ])
                        ->default('default')
                        ->required(),
                    Toggle::make('is_published')
                        ->label('เผยแพร่')
                        ->default(true),
                    TextInput::make('sort_order')
                        ->label('ลำดับ')
                        ->numeric()
                        ->default(0),
                ])
                ->columns(2),
            Section::make('ภาษาและ SEO')
                ->schema([
                    Repeater::make('translations')
                        ->label('Translations')
                        ->relationship()
                        ->schema([
                            Select::make('locale')
                                ->label('ภาษา')
                                ->options(config('chomin.locales.labels'))
                                ->required(),
                            TextInput::make('title')
                                ->label('หัวข้อ')
                                ->required()
                                ->maxLength(255),
                            Textarea::make('excerpt')
                                ->label('คำโปรย')
                                ->columnSpanFull(),
                            RichEditor::make('body')
                                ->label('เนื้อหา')
                                ->columnSpanFull(),
                            TextInput::make('seo_title')
                                ->label('SEO title')
                                ->maxLength(255),
                            Textarea::make('seo_description')
                                ->label('SEO description')
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->defaultItems(2)
                        ->itemLabel(fn (array $state): ?string => ($state['locale'] ?? null) ? strtoupper($state['locale']).' - '.($state['title'] ?? '') : null)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slug')->label('Slug')->searchable()->sortable(),
                TextColumn::make('template')->label('Template')->badge(),
                IconColumn::make('is_published')->label('เผยแพร่')->boolean(),
                TextColumn::make('sort_order')->label('ลำดับ')->sortable(),
                TextColumn::make('updated_at')->label('อัปเดต')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('sort_order')
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
            'index' => ListContentPages::route('/'),
            'create' => CreateContentPage::route('/create'),
            'edit' => EditContentPage::route('/{record}/edit'),
        ];
    }
}
