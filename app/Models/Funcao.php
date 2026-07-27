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
     * relacionamento com users
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_funcao', 'funcao_id', 'user_id')->withPivot('programa_id')->withTimestamps();
    }

    /**
     * relacionamento com programas
     */
    public function programas()
    {
        return $this->belongsToMany('App\Models\Programa', 'user_funcao', 'funcao_id', 'programa_id')->withPivot('user_id')->withTimestamps();
    }
}
