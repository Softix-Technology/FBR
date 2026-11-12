<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies'; // (optional if using default)
    protected $primaryKey = 'cid';  // your primary key

    public $timestamps = true;     // disable if you don't use created_at / updated_at

    protected $fillable = ['cid', 'cname', 'logo'];
}