<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcao extends Model
{
    use HasFactory;

    # funções não segue convenção do laravel para nomes de tabela
    protected $table = 'funcoes';

    protected $fillable = [
        'nome',
        'grupo',
        'peso',
    ];

    /**
     * uma função se relaciona com n programas
     */
    public function programas()
    {
        return $this->belongsToMany('App\Models\Programa', 'user_funcao', 'funcao_id', 'programa_id')->withPivot('user_id')->withTimestamps();
    }

    /**
     * uma função se relaciona com n users
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_funcao', 'funcao_id', 'user_id')->withPivot('programa_id')->withTimestamps();
    }
}
