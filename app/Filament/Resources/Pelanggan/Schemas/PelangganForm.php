<?php

namespace App\Filament\Resources\Pelanggan\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PelangganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pelanggan')
                    ->columns(2)
                    ->schema([
                        TextInput::make('kode')
                            ->label('Kode Pelanggan')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('CUST001'),
                            
                        TextInput::make('nama')
                            ->label('Nama Pelanggan')
                            ->required()
                            ->maxLength(255),
                            
                        TextInput::make('telepon')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(20),
                            
                        TextInput::make('kontak_person')
                            ->label('Kontak Person')
                            ->maxLength(255),
                            
                        Toggle::make('aktif')
                            ->label('Status Aktif')
                            ->default(true)
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Alamat Pelanggan')
                    ->description('Tambahkan satu atau lebih alamat untuk pelanggan ini')
                    ->schema([
                        Repeater::make('alamat')
                            ->relationship()
                            ->schema([
                                TextInput::make('label')
                                    ->label('Label Alamat')
                                    ->placeholder('Kantor Pusat, Gudang A, dll')
                                    ->maxLength(255),
                                    
                                Textarea::make('alamat_lengkap')
                                    ->label('Alamat Lengkap')
                                    ->required()
                                    ->rows(3)
                                    ->columnSpanFull(),
                                    
                                TextInput::make('kota')
                                    ->label('Kota')
                                    ->maxLength(100),
                                    
                                TextInput::make('provinsi')
                                    ->label('Provinsi')
                                    ->maxLength(100),
                                    
                                TextInput::make('kode_pos')
                                    ->label('Kode Pos')
                                    ->maxLength(10),
                                    
                                TextInput::make('kontak_person')
                                    ->label('Kontak Person')
                                    ->maxLength(255),
                                    
                                TextInput::make('telepon')
                                    ->label('Telepon')
                                    ->tel()
                                    ->maxLength(20),
                                    
                                Toggle::make('is_default')
                                    ->label('Alamat Utama')
                                    ->default(false)
                                    ->helperText('Centang jika ini alamat utama/default'),
                                    
                                Toggle::make('aktif')
                                    ->label('Status Aktif')
                                    ->default(true),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => 
                                $state['label'] ?? $state['kota'] ?? 'Alamat Baru'
                            )
                            ->collapsible()
                            ->defaultItems(1)
                            ->addActionLabel('Tambah Alamat')
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}