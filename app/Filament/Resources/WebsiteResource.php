<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebsiteResource\Pages;
use App\Filament\Resources\WebsiteResource\RelationManagers;
use App\Models\Website;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WebsiteResource extends Resource
{
    protected static ?string $model = Website::class;

    protected static ?string $navigationIcon = 'gmdi-web-o';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->reactive() // Menandakan bahwa ini akan memicu perubahan pada field lain
                    ->afterStateUpdated(function (callable $set) {
                        // Reset subscription_id ketika user_id berubah
                        $set('subscription_id', null);
                    }),
                Forms\Components\Select::make('subscription_id')
                    ->relationship('subscription', 'transaction_id', modifyQueryUsing: function ($query, callable $get) {
                        // Ambil user_id yang dipilih
                        $userId = $get('user_id');
            
                        // Filter subscription berdasarkan user_id yang dipilih
                        return $query
                            ->where('user_id', $userId) // Pastikan untuk menyesuaikan dengan kolom yang sesuai
                            ->whereNotNull('transaction_id'); // Hanya ambil yang memiliki transaction_id
                    })
                    ->label('PR Number')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->disabled(fn (callable $get) => !$get('user_id')) // Nonaktifkan jika user_id belum dipilih
                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                        $record ? $record->transaction_id . '' . $record->other_attribute : 'No Transaction ID'
                    ),
                Forms\Components\TextInput::make('domain_name')
                    ->required()
                    ->unique(Website::class, 'domain_name')
                    ->placeholder('example.com')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subscription.transaction_id')
                    ->label('PR Number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('domain_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subscription.package.name')
                    ->label('Package')
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
            'index' => Pages\ListWebsites::route('/'),
            'create' => Pages\CreateWebsite::route('/create'),
            'edit' => Pages\EditWebsite::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        $user = auth()->user();
        
        // Super admin melihat semua
        if ($user->hasRole('super-admin')) {
            return $query;
        }
        
        // User lain hanya melihat payment milik sendiri
        return $query->where('user_id', $user->id);
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

}
