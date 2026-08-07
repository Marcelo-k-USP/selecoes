<?php

namespace App\Http\Controllers;

use App\Http\Requests\VinculoRequest;
use App\Models\Vinculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class VinculoController extends Controller
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
        Gate::authorize('vinculos.viewAny');

        \UspTheme::activeUrl('vinculos');
        if (!$request->ajax())
            return view('vinculos.tree', $this->monta_compact_index());
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
        Gate::authorize('vinculos.view', Vinculo::where('id', $id)->first());

        \UspTheme::activeUrl('vinculos');
        if ($request->ajax())
            return Vinculo::find((int) $id);    // preenche os dados do form de edição de um vínculo
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\VinculoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VinculoRequest $request)
    {
        Gate::authorize('vinculos.create');

        $validator = Validator::make($request->all(), VinculoRequest::rules, VinculoRequest::messages);
        if ($validator->fails())
            return back()->withErrors($validator)->withInput();

        $request->merge(['exige_categoria' => $request->has('exige_categoria')]);    // acerta o valor do campo "exige_categoria" (pois, se o usuário deixou false, o campo não vem no $request e, se o usuário deixou true, ele vem mas com valor null)
        $request->merge(['permite_programa' => $request->has('permite_programa')]);    // acerta o valor do campo "permite_programa" (pois, se o usuário deixou false, o campo não vem no $request e, se o usuário deixou true, ele vem mas com valor null)
        $request->merge(['permite_taxa' => $request->has('permite_taxa')]);    // acerta o valor do campo "permite_taxa" (pois, se o usuário deixou false, o campo não vem no $request e, se o usuário deixou true, ele vem mas com valor null)
        $request->merge(['permite_nivel' => $request->has('permite_nivel')]);    // acerta o valor do campo "permite_nivel" (pois, se o usuário deixou false, o campo não vem no $request e, se o usuário deixou true, ele vem mas com valor null)
        $request->merge(['permite_linhapesquisa' => $request->has('permite_linhapesquisa')]);    // acerta o valor do campo "permite_linhapesquisa" (pois, se o usuário deixou false, o campo não vem no $request e, se o usuário deixou true, ele vem mas com valor null)
        $request->merge(['permite_disciplinas' => $request->has('permite_disciplinas')]);    // acerta o valor do campo "permite_disciplinas" (pois, se o usuário deixou false, o campo não vem no $request e, se o usuário deixou true, ele vem mas com valor null)
        $request->merge(['exige_orientador' => $request->has('exige_orientador')]);    // acerta o valor do campo "exige_orientador" (pois, se o usuário deixou false, o campo não vem no $request e, se o usuário deixou true, ele vem mas com valor null)

        $vinculo = Vinculo::create($request->all());

        $request->session()->flash('alert-success', 'Dados adicionados com sucesso');
        \UspTheme::activeUrl('vinculos');
        return redirect()->route('vinculos.index')->with($this->monta_compact_index());    // se fosse return view, um eventual F5 do usuário duplicaria o registro... POSTs devem ser com redirect
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\VinculoRequest  $request
     * @param  string                             $id
     * @return \Illuminate\Http\Response
     */
    public function update(VinculoRequest $request, string $id)
    {
        Gate::authorize('vinculos.update');

        $validator = Validator::make($request->all(), VinculoRequest::rules, VinculoRequest::messages);
        if ($validator->fails())
            return back()->withErrors($validator)->withInput();

        $request->merge(['exige_categoria' => $request->has('exige_categoria')]);    // acerta o valor do campo "exige_categoria" (pois, se o usuário deixou false, o campo não vem no $request e, se o usuário deixou true, ele vem mas com valor null)
        $request->merge(['permite_programa' => $request->has('permite_programa')]);    // acerta o valor do campo "permite_programa" (pois, se o usuário deixou false, o campo não vem no $request e, se o usuário deixou true, ele vem mas com valor null)
        $request->merge(['permite_taxa' => $request->has('permite_taxa')]);    // acerta o valor do campo "permite_taxa" (pois, se o usuário deixou false, o campo não vem no $request e, se o usuário deixou true, ele vem mas com valor null)
        $request->merge(['permite_nivel' => $request->has('permite_nivel')]);    // acerta o valor do campo "permite_nivel" (pois, se o usuário deixou false, o campo não vem no $request e, se o usuário deixou true, ele vem mas com valor null)
        $request->merge(['permite_linhapesquisa' => $request->has('permite_linhapesquisa')]);    // acerta o valor do campo "permite_linhapesquisa" (pois, se o usuário deixou false, o campo não vem no $request e, se o usuário deixou true, ele vem mas com valor null)
        $request->merge(['permite_disciplinas' => $request->has('permite_disciplinas')]);    // acerta o valor do campo "permite_disciplinas" (pois, se o usuário deixou false, o campo não vem no $request e, se o usuário deixou true, ele vem mas com valor null)
        $request->merge(['exige_orientador' => $request->has('exige_orientador')]);    // acerta o valor do campo "exige_orientador" (pois, se o usuário deixou false, o campo não vem no $request e, se o usuário deixou true, ele vem mas com valor null)

        $vinculo = Vinculo::find((int) $id);
        $vinculo->fill($request->all());
        $vinculo->save();

        $request->session()->flash('alert-success', 'Dados salvos com sucesso');
        \UspTheme::activeUrl('vinculos');
        return view('vinculos.tree', $this->monta_compact_index());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\VinculoRequest  $request
     * @param  string                             $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(VinculoRequest $request, string $id)
    {
        Gate::authorize('vinculos.delete');

        $vinculo = Vinculo::find((int) $id);
        if ($vinculo->selecoes()->exists())
            $request->session()->flash('alert-danger', 'Há seleções que fazem uso deste vínculo!');
        elseif ($vinculo->categorias()->exists())
            $request->session()->flash('alert-danger', 'Há categorias que fazem uso deste vínculo!');
        elseif ($vinculo->funcoes()->exists())
            $request->session()->flash('alert-danger', 'Há funções que fazem uso deste vínculo!');
        elseif ($vinculo->tiposarquivo()->exists())
            $request->session()->flash('alert-danger', 'Há tipos de documento que fazem uso deste vínculo!');
        else {
            $vinculo->delete();
            $request->session()->flash('alert-success', 'Dados removidos com sucesso!');
        }
        \UspTheme::activeUrl('vinculos');
        return view('vinculos.tree', $this->monta_compact_index());
    }

    private function monta_compact_index()
    {
        $vinculos = Vinculo::listarVinculos();
        $fields = Vinculo::getFields();
        $modal['url'] = 'vinculos';
        $modal['title'] = 'Editar Vínculo';
        $rules = VinculoRequest::rules;

        return compact('vinculos', 'fields', 'modal', 'rules');
    }
}
