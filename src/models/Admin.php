<?php
namespace MyApp\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model {

    use SoftDeletes;
    protected $table = 'CompteAdmin';
    protected $fillable = ['login', 'mdp', 'super'];
    public $timestamps = true;

} 