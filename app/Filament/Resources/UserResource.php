<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
// use Filament\Resources\Api\Resource;
use Filament\Resources\Api\Schema;
use Filament\Resources\Api\Contract\ApiResourceContract;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Resources\Json\JsonResource;



class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'monoicon-users';

    // Semakin kecil angkanya, semakin di atas
    protected static ?int $navigationSort = 6;

    public static function middleware(): array
    {
        return ['auth:sanctum', 'role:super-admin'];  // Hanya Super Admin yang bisa mengakses resource
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->hiddenOn('edit'),
                Forms\Components\Select::make('roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->required()


            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\BadgeColumn::make('roles.name')
                    ->colors(['primary'])
                    ->separator(',')
            ])
            ->filters([
                Tables\Filters\Filter::make('verified')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('email_verified_at')),

            ])
            ->actions([
                Action::make('generateToken')
                    ->label('Generate Token')
                    ->action(function (User $record) {
                        // Hapus token lama
                        $record->tokens()->delete();

                        // Buat token baru dengan abilities
                        $token = $record->createToken('FilamentToken', ['*'])->plainTextToken;

                        // Log token creation
                        Log::info('Token generated', [
                            'user_id' => $record->id,
                            'roles' => $record->getRoleNames()->toArray()
                        ]);
                        Notification::make()
                            ->title('Token Generated')
                            ->body("Your API token: $token\n\nExample usage:\nAuthorization: Bearer $token")
                            ->persistent()

                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                // ->visible(fn ($record) => !$record->hasRole('super-admin')),
                Tables\Actions\DeleteAction::make(),
                // ->visible(fn ($record) => !$record->hasRole('super-admin')),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    // Pembatasan akses
    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('super-admin');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole('super-admin') && !$record->hasRole('super-admin');
    }
    public static function canEdit($record): bool
    {
        // Hanya super admin yang bisa mengedit
        return auth()->user()->hasRole('super-admin');
    }

    // Modifikasi query untuk membatasi
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();

        // Super admin melihat semua
        if ($user->hasRole('super-admin')) {
            return $query;
        }

        // User lain hanya melihat milik sendiri
        return $query->where('id', $user->id);
    }
}

class UserResourceApi extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
