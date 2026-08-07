<?php

namespace App\Http\Controllers;

use App\Models\Funcao;
use App\Models\Programa;
use App\Models\User;
use App\Models\Vinculo;
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
    public function edit(?Vinculo $vinculo = null)
    {
        Gate::authorize('funcoes.update');

        \UspTheme::activeUrl('funcoes');
        return view('funcoes.edit', $this->monta_compact($vinculo));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vinculo $vinculo)
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
        return view('funcoes.edit', $this->monta_compact($vinculo));
    }

    private function monta_compact(Vinculo $vinculo = null)
    {
        if (is_null($vinculo))
            $vinculo = Vinculo::first();
        $vinculos = Vinculo::listarVinculos();

        $funcao_docentes = Funcao::where('nome', 'Docentes do Programa')->whereHas('vinculos', function ($query) use ($vinculo) { $query->where('vinculos.id', $vinculo->id); })->first();
        $funcao_secretarios_programa = Funcao::where('nome', 'Secretários(as) do Programa')->whereHas('vinculos', function ($query) use ($vinculo) { $query->where('vinculos.id', $vinculo->id); })->first();
        $funcao_coordenadores_programa = Funcao::where('nome', 'Coordenadores(as) do Programa')->whereHas('vinculos', function ($query) use ($vinculo) { $query->where('vinculos.id', $vinculo->id); })->first();
        $funcao_funcionarios_setor = Funcao::where('grupo', 'Funcionários(as) do Setor')->whereHas('vinculos', function ($query) use ($vinculo) { $query->where('vinculos.id', $vinculo->id); })->first();
        $funcao_coordenadores_setor = Funcao::where('grupo', 'Coordenadores(as) do Setor')->whereHas('vinculos', function ($query) use ($vinculo) { $query->where('vinculos.id', $vinculo->id); })->first();

        $programas_docentes = $funcao_docentes ? Programa::with(['users' => function ($query) use ($funcao_docentes) {
            $query->where('user_funcao.funcao_id', $funcao_docentes->id)
                ->orderBy('user_funcao.programa_id')
                ->orderBy('users.name');
        }])->get() : collect();
        $programas_secretarios = $funcao_secretarios_programa ? Programa::with(['users' => function ($query) use ($funcao_secretarios_programa) {
            $query->where('user_funcao.funcao_id', $funcao_secretarios_programa->id)
                ->orderBy('user_funcao.programa_id')
                ->orderBy('users.name');
        }])->get() : collect();
        $programas_coordenadores = $funcao_coordenadores_programa ? Programa::with(['users' => function ($query) use ($funcao_coordenadores_programa) {
            $query->where('user_funcao.funcao_id', $funcao_coordenadores_programa->id)
                ->orderBy('user_funcao.programa_id')
                ->orderBy('users.name');
        }])->get() : collect();
        $funcionarios_setor_users = $funcao_funcionarios_setor ?
            $funcao_funcionarios_setor->users()
                ->select('users.name', 'users.codpes')
                ->orderBy('users.name')
                ->get()
                ->map(function ($user) { return (object) [ 'name' => $user->name, 'codpes' => $user->codpes ]; })->values()->toArray() :
            [];
        $funcionarios_setor_nome = $funcao_funcionarios_setor ? $funcao_funcionarios_setor->nome : '';
        $coordenadores_setor_users = $funcao_coordenadores_setor ?
            $funcao_coordenadores_setor->users()
                ->select('users.name', 'users.codpes')
                ->orderBy('users.name')
                ->get()
                ->map(function ($user) { return (object) [ 'name' => $user->name, 'codpes' => $user->codpes ]; })->values()->toArray() :
            [];
        $coordenadores_setor_nome = $funcao_coordenadores_setor ? $funcao_coordenadores_setor->nome : '';

        return compact('vinculo', 'vinculos', 'programas_docentes', 'programas_secretarios', 'programas_coordenadores', 'funcionarios_setor_users', 'funcionarios_setor_nome', 'coordenadores_setor_users', 'coordenadores_setor_nome');
    }
}
