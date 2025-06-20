<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
// --- TAMBAHKAN USE STATEMENT INI ---
use Filament\Tables\Actions\Action;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
// --- AKHIR USE STATEMENT ---

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Pengaturan';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Nama')->required()->maxLength(255),
                Forms\Components\TextInput::make('username')->label('Username')->required()->alphaDash()->unique(ignoreRecord: true)->maxLength(255),
                Forms\Components\TextInput::make('email')->email()->required()->maxLength(255)->unique(ignoreRecord: true),
                Forms\Components\Select::make('role')->options(['admin' => 'Admin','user' => 'User',])->required()->default('user'),
                Forms\Components\TextInput::make('password')->label('Password Baru')->password()->required(fn (string $context): bool => $context === 'create')->rule(Password::defaults())->dehydrated(fn ($state) => filled($state))->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                Forms\Components\TextInput::make('password_confirmation')->label('Konfirmasi Password')->password()->required(fn (string $context): bool => $context === 'create')->same('password')->dehydrated(false)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('username')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('role')->badge()->color(fn (string $state): string => match ($state) {'admin' => 'danger','user' => 'success',}),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal Daftar')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('importUsers')
                    ->label('Impor Pengguna dari CSV')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->form([
                        Forms\Components\FileUpload::make('attachment')
                            ->label('Upload File CSV')
                            ->required()
                            ->acceptedFileTypes(['text/csv'])
                            ->disk('local') // <-- INI ADALAH KUNCI PERBAIKANNYA
                    ])
                    ->action(function (array $data) {
                        try {
                            Excel::import(new UsersImport, $data['attachment']);
                            Notification::make()
                                ->title('Pengguna Berhasil Diimpor')
                                ->success()
                                ->send();
                        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                            $failures = $e->failures();
                            $errorMessages = [];
                            foreach ($failures as $failure) {
                                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
                            }
                            Notification::make()
                                ->title('Terjadi Kesalahan Validasi Saat Impor')
                                ->body(implode("\n", $errorMessages))
                                ->danger()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Terjadi Kesalahan Umum')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }    
}