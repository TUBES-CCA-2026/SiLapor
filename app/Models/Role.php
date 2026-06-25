<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id_role';

    protected $fillable = ['nama_role'];

    public static function idByName(string $namaRole): int
    {
        $role = static::firstOrCreate(['nama_role' => $namaRole]);

        return (int) $role->id_role;
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id_role', 'id_role');
    }
}
