<?php

namespace App\Filament\Resources;

use stdClass;
use Filament\Forms;
use Filament\Tables;
use App\Models\Result;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Monitoring;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\Section as SectionInfoLIst;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MonitoringResource\Pages;
use App\Filament\Resources\MonitoringResource\RelationManagers;

class MonitoringResource extends Resource
{
    protected static ?string $model = Monitoring::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Monitoring';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('type_monitor')
                            ->options(['HTTP(s)' => 'HTTP(s)']),
                        TextInput::make('name')
                            ->required()
                            ->placeholder('Nama Lengkap')
                            ->live()
                            ->maxValue(255)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),
                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('url')
                            ->required(),
                        TextInput::make('schedule')
                            ->required(),
                        TextInput::make('tries'),
                        TextInput::make('email'),
                        RichEditor::make('description'),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $userId = Auth::user()->id;
                $query->where('user_id', $userId);
            })
            ->columns([
                TextColumn::make('No')->state(
                    static function (HasTable $livewire, stdClass $rowLoop): string {
                        return (string) (
                            $rowLoop->iteration +
                            ($livewire->getTableRecordsPerPage() * (
                                $livewire->getTablePage() - 1
                            ))
                        );
                    }
                ),
                TextColumn::make('type_monitor'),
                TextColumn::make('name'),
                TextColumn::make('url'),
                TextColumn::make('schedule'),
                TextColumn::make('tries'),
                TextColumn::make('results.status_code')
                    ->label('Status Code'),
                TextColumn::make('email'),
                TextColumn::make('results.response_time')
                    ->label('Response Time (s)'),
                TextColumn::make('avg_response_time')
                    ->label('Avg Response Time (s)'),
                TextColumn::make('description')
                    ->markdown(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation(),
                    Tables\Actions\Action::make('Pause')
                        ->icon('heroicon-o-pause-circle'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                SectionInfoLIst::make('Info Monitroing')
                    ->schema([
                        TextEntry::make('avg_response_time'),
                        TextEntry::make('type_monitor'),
                        TextEntry::make('name'),
                        TextEntry::make('url')
                            ->label('URL'),
                        TextEntry::make('schedule')
                            ->label('Repetition in second'),
                        TextEntry::make('tries'),
                    ])->columns(2)
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonitorings::route('/'),
            'create' => Pages\CreateMonitoring::route('/create'),
            'view' => Pages\ViewMonitoring::route('/{record}'),
            'edit' => Pages\EditMonitoring::route('/{record}/edit'),
        ];
    }
}
