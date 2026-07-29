<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinhaPesquisaRequest;
use App\Models\LinhaPesquisa;
use App\Models\Nivel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class LinhaPesquisaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request   $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Gate::authorize('linhaspesquisa.viewAny');

        \UspTheme::activeUrl('linhaspesquisa');
        if (!$request->ajax())
            return view('linhaspesquisa.tree', $this->monta_compact_index());
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request   $request
     * @param  string                     $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $id)
    {
        Gate::authorize('linhaspesquisa.view', LinhaPesquisa::where('id', $id)->first());

        \UspTheme::activeUrl('linhaspesquisa');
        if ($request->ajax())
            return LinhaPesquisa::find((int) $id);    // preenche os dados do form de edição de uma linha de pesquisa/tema
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\LinhaPesquisaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LinhaPesquisaRequest $request)
    {
        Gate::authorize('linhaspesquisa.create');

        $validator = Validator::make($request->all(), LinhaPesquisaRequest::rules, LinhaPesquisaRequest::messages);
        if ($validator->fails())
            return back()->withErrors($validator)->withInput();

        // transaction para não ter problema de inconsistência do DB
        $linhapesquisa = DB::transaction(function () use ($request) {
            $linhapesquisa = LinhaPesquisa::create($request->all());
            foreach (Nivel::all() as $nivel)    // adiciona relações desta linha de pesquisa/tema com todos os níveis
                $linhapesquisa->niveis()->attach($nivel);
            return $linhapesquisa;
        });

        $request->session()->flash('alert-success', 'Linha de pesquisa/tema cadastrado com sucesso');
        \UspTheme::activeUrl('linhaspesquisa');
        return redirect()->route('linhaspesquisa.index')->with($this->monta_compact_index());    // se fosse return view, um eventual F5 do usuário duplicaria o registro... POSTs devem ser com redirect
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\LinhaPesquisaRequest  $request
     * @param  string                                   $id
     * @return \Illuminate\Http\Response
     */
    public function update(LinhaPesquisaRequest $request, string $id)
    {
        $linhapesquisa = LinhaPesquisa::find((int) $id);
        Gate::authorize('linhaspesquisa.update', $linhapesquisa);

        $validator = Validator::make($request->all(), LinhaPesquisaRequest::rules, LinhaPesquisaRequest::messages);
        if ($validator->fails())
            return back()->withErrors($validator)->withInput();

        $linhapesquisa->nome = $request->nome;
        $linhapesquisa->programa_id = $request->programa_id;
        $linhapesquisa->save();

        $request->session()->flash('alert-success', 'Linha de pesquisa/tema alterado com sucesso');
        \UspTheme::activeUrl('linhaspesquisa');
        return view('linhaspesquisa.tree', $this->monta_compact_index());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\LinhaPesquisaRequest  $request
     * @param  string                                   $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(LinhaPesquisaRequest $request, string $id)
    {
        $linhapesquisa = LinhaPesquisa::find((int) $id);
        Gate::authorize('linhaspesquisa.delete', $linhapesquisa);

        if ($linhapesquisa->selecoes->isNotEmpty())
            $request->session()->flash('alert-danger', 'Há seleções para esta linha de pesquisa/tema!');
        else {
            // transaction para não ter problema de inconsistência do DB
            DB::transaction(function () use ($linhapesquisa) {
                if ($linhapesquisa->niveis()->exists())
                    $linhapesquisa->niveis()->detach();    // remove todas as relações com níveis desta linha de pesquisa/tema
                $linhapesquisa->delete();
            });

            $request->session()->flash('alert-success', 'Dados removidos com sucesso!');
        }
        \UspTheme::activeUrl('linhaspesquisa');
        return view('linhaspesquisa.tree', $this->monta_compact_index());
    }

    private function monta_compact_index()
    {
        $linhaspesquisa = LinhaPesquisa::listarLinhasPesquisa();
        $fields = LinhaPesquisa::getFields();
        $modal['url'] = 'linhaspesquisa';
        $modal['title'] = 'Editar Linha de Pesquisa/Tema';
        $rules = LinhaPesquisaRequest::rules;

        return compact('linhaspesquisa', 'fields', 'modal', 'rules');
    }
}
