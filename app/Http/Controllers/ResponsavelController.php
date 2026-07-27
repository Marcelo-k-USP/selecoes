<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class ResponsavelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except([
            'show'
        ]);    // exige que o usuário esteja logado, exceto para estes métodos listados
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int                       $id
     * @param  string                    $grupo_funcao
     * @param  ?int                      $programa_id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $id, string $grupo_funcao, ?int $programa_id = null)
    {
        if ($request->ajax()) {

            $user = User::find((int) $id);
            if ($user->gerenciaProgramaGrupoFuncao($grupo_funcao, $programa_id)) {    // traz dados apenas de quem possui o dado grupo de função no dado programa

                if ($grupo_funcao != 'Secretários(as) do Programa')
                    $user->telefone = '';

                return $user;    // preenche os dados do modal de exibição de um responsável
            }
        }
    }
}
