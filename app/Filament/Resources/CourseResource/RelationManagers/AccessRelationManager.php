<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccessRelationManager extends RelationManager
{
    protected static string $relationship = 'access';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Learner')
                    ->options(fn () => User::query()->where('role', 'learner')->pluck('email', 'id'))
                    ->searchable()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('granted_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make('grantAccess')
                    ->label('Grant access')
                    ->modalHeading('Grant access to an existing learner')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['granted_at'] = now();

                        return $data;
                    }),
                Tables\Actions\Action::make('createLearner')
                    ->label('Create learner')
                    ->icon('heroicon-o-user-plus')
                    ->modalHeading('Create a learner and grant access')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique('users', 'email')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label('Temporary password')
                            ->password()
                            ->revealable()
                            ->default(fn () => Str::password(12))
                            ->required()
                            ->minLength(8),
                    ])
                    ->action(function (array $data): void {
                        $user = User::create([
                            'name' => $data['name'],
                            'email' => $data['email'],
                            'password' => Hash::make($data['password']),
                            'role' => 'learner',
                        ]);

                        $this->getRelationship()->create([
                            'user_id' => $user->id,
                            'granted_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Learner created')
                            ->body("{$user->email} — temporary password: {$data['password']}")
                            ->persistent()
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->label('Revoke'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Revoke selected'),
                ]),
            ]);
    }
}
