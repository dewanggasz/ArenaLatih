<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'name'     => $row['name'],
            'username' => $row['username'],
            'email'    => $row['email'],
            'password' => Hash::make($row['password']),
            // Secara default, peran mereka adalah 'user'
            'role'     => 'user',
        ]);
    }

    /**
     * Menambahkan aturan validasi untuk setiap baris di CSV.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            // Pastikan username dan email unik di database
            'username' => 'required|string|alpha_dash|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ];
    }
}
