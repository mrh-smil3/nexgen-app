<?php

namespace App\Filament\Resources;
// namespace App\Filament\Resources\UserResource\Api;

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



class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'monoicon-users';

    // Semakin kecil angkanya, semakin di atas
    protected static ?int $navigationSort = 6; 

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
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
            
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => !$record->hasRole('super-admin')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => !$record->hasRole('super-admin')),
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

    // public static function canViewAny(): bool
    // {
    //     $user = auth()->user();
        
    //     // Izinkan super admin melihat semua
    //     if ($user->hasRole('super-admin')) {
    //         return true;
    //     }
        
    //     // Izinkan user melihat user mereka sendiri
    //     return Gate::allows('view-own-user');
    // }

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

// class UserApiResource extends Resource implements ApiResourceContract
// {
//     protected static ?string $model = User::class;

//     // Definisikan skema API
//     public function schema(): Schema
//     {
//         return Schema::make()
//             ->fields([
//                 Schema\Field::string('name')
//                     ->required()
//                     ->maxLength(255),
                
//                 Schema\Field::string('email')
//                     ->required()
//                     ->email()
//                     ->unique('users', 'email'),
                
//                 Schema\Field::array('roles')
//                     ->items(
//                         Schema\Field::string()
//                     )
//                     ->nullable(),
//             ])
//             ->relationships([
//                 'roles' => Schema\Relationship::belongsToMany(Role::class)
//             ])
//             ->filters([
//                 Schema\Filter::make('verified')
//                     ->field('email_verified_at')
//                     ->type('boolean')
//             ]);
//     }

//     // Atur izin akses API
//     public static function canCreate(): bool
//     {
//         $user = auth()->user();
//         return $user && $user->hasRole('super-admin');
//     }

//     public static function canUpdate(): bool
//     {
//         $user = auth()->user();
//         return $user && $user->hasRole('super-admin');
//     }

//     public static function canDelete(): bool
//     {
//         $user = auth()->user();
//         return $user && $user->hasRole('super-admin');
//     }

//     // Modifikasi query untuk pembatasan
//     public function query(Request $request): Builder
//     {
//         $query = parent::query($request);
        
//         $authUser = auth()->user();
        
//         // Super admin melihat semua
//         if ($authUser->hasRole('super-admin')) {
//             return $query;
//         }
        
//         // User lain hanya melihat milik sendiri
//         return $query->where('id', $authUser->id);
//     }

//     // Kustomisasi endpoint
//     public static function getEndpoint(): string
//     {
//         return 'users';
//     }

//     // Metode tambahan untuk transformasi data
//     public function transform(Model $model): array
//     {
//         $data = parent::transform($model);
        
//         // Tambahkan transformasi khusus
//         $data['roles'] = $model->roles->pluck('name');
        
//         // Sembunyikan field sensitif
//         unset($data['password']);
        
//         return $data;
//     }

//     // Validasi kustom saat create/update
//     public function validateCreate(array $data): array
//     {
//         return Validator::make($data, [
//             'name' => 'required|string|max:255',
//             'email' => 'required|email|unique:users,email',
//             'password' => 'required|min:8',
//             'roles' => 'sometimes|array|exists:roles,name'
//         ])->validate();
//     }

//     public function validateUpdate(Model $model, array $data): array
//     {
//         return Validator::make($data, [
//             'name' => 'sometimes|string|max:255',
//             'email' => 'sometimes|email|unique:users,email,' . $model->id,
//             'roles' => 'sometimes|array|exists:roles,name'
//         ])->validate();
//     }
// }
