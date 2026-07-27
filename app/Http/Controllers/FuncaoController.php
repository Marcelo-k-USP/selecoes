<?php

namespace App\Http\Controllers;

use App\Models\Funcao;
use App\Models\Programa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class FuncaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        Gate::authorize('funcoes.update');

        \UspTheme::activeUrl('funcoes');
        return view('funcoes.edit', $this->monta_compact());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if ($add = $request->add_codpes) {

            // transaction para não ter problema de inconsistência do DB
            DB::transaction(function () use ($request, $add) {

                $user = User::findOrCreateFromReplicado($add);
                if ($user)
                    $user->associarProgramaFuncao($request->programa, $request->funcao);
            });
        }

        if ($rem = $request->rem_codpes) {
            $user = User::where('codpes', $rem)->first();
            if ($user)
                $user->desassociarProgramaFuncao($request->programa, $request->funcao);
        }

        $request->session()->flash('alert-success', 'Dados editados com sucesso');
        \UspTheme::activeUrl('funcoes');
        return view('funcoes.edit', $this->monta_compact());
    }

    private function monta_compact()
    {
        $funcao_docentes = Funcao::where('nome', 'Docentes do Programa')->first();
        $funcao_secretarios_programa = Funcao::where('nome', 'Secretários(as) do Programa')->first();
        $funcao_coordenadores_programa = Funcao::where('nome', 'Coordenadores(as) do Programa')->first();
        $funcao_servico_posgraduacao = Funcao::where('nome', 'Serviço de Pós-Graduação')->first();
        $funcao_coordenadores_posgraduacao = Funcao::where('nome', 'Coordenadores(as) da Pós-Graduação')->first();

        $programas_docentes = Programa::with(['users' => function ($query) use ($funcao_docentes) {
            if ($funcao_docentes)
                $query->where('user_funcao.funcao_id', $funcao_docentes->id)
                    ->orderBy('user_funcao.programa_id')
                    ->orderBy('users.name');
        }])->get();
        $programas_secretarios = Programa::with(['users' => function ($query) use ($funcao_secretarios_programa) {
            if ($funcao_secretarios_programa)
                $query->where('user_funcao.funcao_id', $funcao_secretarios_programa->id)
                    ->orderBy('user_funcao.programa_id')
                    ->orderBy('users.name');
        }])->get();
        $programas_coordenadores = Programa::with(['users' => function ($query) use ($funcao_coordenadores_programa) {
            if ($funcao_coordenadores_programa)
                $query->where('user_funcao.funcao_id', $funcao_coordenadores_programa->id)
                    ->orderBy('user_funcao.programa_id')
                    ->orderBy('users.name');
        }])->get();
        $posgraduacao_servico_users = $funcao_servico_posgraduacao ?
            $funcao_servico_posgraduacao->users()
                ->select('users.name', 'users.codpes')
                ->orderBy('users.name')
                ->get()
                ->map(function ($user) { return (object) [ 'name' => $user->name, 'codpes' => $user->codpes ]; })->values()->toArray() :
            [];
        $posgraduacao_coordenadores_users = $funcao_coordenadores_posgraduacao ?
            $funcao_coordenadores_posgraduacao->users()
                ->select('users.name', 'users.codpes')
                ->orderBy('users.name')
                ->get()
                ->map(function ($user) { return (object) [ 'name' => $user->name, 'codpes' => $user->codpes ]; })->values()->toArray() :
            [];

        return compact('programas_docentes', 'programas_secretarios', 'programas_coordenadores', 'posgraduacao_servico_users', 'posgraduacao_coordenadores_users');
    }
}
