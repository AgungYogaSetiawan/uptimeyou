<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Result;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Monitoring;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
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
                        TextInput::make('amount_send_notification'),
                        Select::make('status_code')
                            ->options([
                                '200-299' => '200-299',
                                '300-399' => '300-399',
                                '400-499' => '400-499',
                                '500-599' => '500-599'
                            ]),
                        Select::make('notification')
                            ->options([
                                'email' => 'email',
                                'discord' => 'discord'
                            ]),
                        RichEditor::make('description')
                            ->columnSpan('full'),
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
                TextColumn::make('type_monitor'),
                TextColumn::make('name'),
                TextColumn::make('url'),
                TextColumn::make('schedule'),
                TextColumn::make('tries'),
                TextColumn::make('amount_send_notification'),
                TextColumn::make('status_code'),
                TextColumn::make('notification'),
                TextColumn::make('description')
                    ->markdown(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->form([
                            TextInput::make('monitorings.response_time')
                        ]),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'edit' => Pages\EditMonitoring::route('/{record}/edit'),
        ];
    }
}
