<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssignmentResource\Pages;
use App\Filament\Resources\AssignmentResource\RelationManagers;
use App\Models\Assignment;
use App\Models\User;
use App\Models\Test;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssignmentResource extends Resource
{
    protected static string $resource = AssignmentResource::class;

    protected static ?string $model = Assignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Asignaciones';
    protected static ?string $navigationLabel = 'Asignaciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Docente')
                    ->options(function () {
                        // Filtrar usuarios con el rol 'docente'
                        // Asegúrate de adaptar esto a tu sistema de roles
                        return User::whereHas('roles', function ($query): void {
                            $query->where('name', 'docente');
                        })->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('test_id')
                    ->label('Test')
                    ->options(Test::pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en_progreso' => 'En Progreso',
                        'completado' => 'Completado',
                    ])
                    ->default('pendiente')
                    ->required(),

                Forms\Components\DateTimePicker::make('due_date')
                    ->label('Fecha límite')
                    ->required(),

                Forms\Components\TextInput::make('score')
                    ->label('Calificación')
                    ->numeric()
                    ->visibleOn('edit'),

                Forms\Components\DateTimePicker::make('completed_at')
                    ->label('Completado el')
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Docente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('test.name')
                    ->label('Test')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pendiente',
                        'primary' => 'en_progreso',
                        'success' => 'completado',
                    ]),
                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('score')
                    ->label('Calificación')
                    ->visible(fn($livewire) => $livewire instanceof Pages\ListAssignments),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en_progreso' => 'En Progreso',
                        'completado' => 'Completado',
                    ]),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Docente')
                    ->options(function () {
                        return User::whereHas('roles', function ($query) {
                            $query->where('name', 'docente');
                        })->pluck('name', 'id');
                    })

            ])


            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListAssignments::route('/'),
            'create' => Pages\CreateAssignment::route('/create'),
            'edit' => Pages\EditAssignment::route('/{record}/edit'),
        ];
    }
}
