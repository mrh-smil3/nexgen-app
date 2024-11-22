<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
use App\Models\Package;
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


class PackageResource extends Resource
{
    
    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'iconoir-packages';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->nullable(),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                Forms\Components\Select::make('duration')
                    ->options([
                        '1 month' => '1 Bulan',
                        '3 months' => '3 Bulan',
                        '6 months' => '6 Bulan',
                        '12 months' => '12 Bulan',
                    ])
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
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
    // Batasi seluruh akses resource berdasarkan role
    // public static function canViewAny(): bool
    // {
    //     return auth()->user()->hasAnyRole([
    //         'super-admin'
    //     ]);
    // }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        
        // Izinkan super admin melihat semua
        if ($user->hasRole('super-admin')) {
            return true;
        }
        
        // Izinkan user melihat user mereka sendiri
        return Gate::allows('view-own-user');
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
        return $query->where('user_id', $user->id);
    }
}

