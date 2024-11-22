<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Spatie\Permission\Models\Role;




class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'gmdi-payment-o';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subscription_id')
                    ->relationship('subscription', 'transaction_id', modifyQueryUsing: function ($query) {
                        return $query->whereNotNull('transaction_id'); // Hanya ambil yang memiliki transaction_id
                    })
                    ->label('PR Number')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => 
                        $record ? $record->transaction_id . '' . $record->other_attribute : 'No Transaction ID'
                    ),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                Forms\Components\DatePicker::make('payment_date')
                    ->required(),
                Forms\Components\Select::make('payment_method')
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'credit_card' => 'Credit Card',
                        'ewallet' => 'E-Wallet',
                        'cash' => 'Cash'
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed'
                    ])
                    ->default('pending')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subscription.transaction_id')
                    ->label('PR Number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subscription.user.name')
                    ->label('User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed'
                    ])
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed'
                    ]),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'credit_card' => 'Credit Card',
                        'ewallet' => 'E-Wallet',
                        'cash' => 'Cash'
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => !$record->hasRole('super-admin')),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('download_invoice')
                    ->label('Download Invoice')
                    // ->icon('heroicon-o-document-download')
                    ->action(function (Payment $record) {
                        // Logic untuk download invoice
                        return response()->download(
                            storage_path('invoices/invoice-' . $record->id . '.pdf')
                        );
                    })
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    // // Optional: Custom Dashboard Widget
    // public static function getWidgets(): array
    // {
    //     return [
    //         PaymentSummaryWidget::class,
    //     ];
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
        
        // User lain hanya melihat payment milik sendiri
        return $query->whereHas('subscription', function($subQuery) use ($user) {
            $subQuery->where('user_id', $user->id);
        });
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
