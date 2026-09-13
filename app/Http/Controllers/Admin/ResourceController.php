<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Generic tenant-scoped CRUD. Subclasses declare config(); views are shared
// (resources/views/resource/index.blade.php). All queries go through the model's
// Tenantable global scope, so a school only ever sees/edits its own rows.
abstract class ResourceController extends Controller
{
    abstract protected function config(): array;

    protected function cfg(): array
    {
        return array_merge([
            'canCreate' => true,
            'canEdit' => true,
            'canDelete' => true,
            'orderBy' => 'created_at',
            'orderDir' => 'desc',
        ], $this->config());
    }

    public function index(Request $request)
    {
        $c = $this->cfg();
        $model = $c['model'];
        $rows = $model::query()->orderBy($c['orderBy'], $c['orderDir'])->get();
        $editing = null;
        if ($request->filled('edit')) {
            $editing = $model::find($request->query('edit'));
        }
        return view('resource.index', array_merge($c, [
            'rows' => $rows,
            'editing' => $editing,
        ]));
    }

    protected function validated(Request $request, array $c): array
    {
        $rules = [];
        foreach ($c['fields'] as $f) {
            $rule = ($f['required'] ?? false) ? ['required'] : ['nullable'];
            if (($f['type'] ?? 'text') === 'number') $rule[] = 'numeric';
            $rules[$f['name']] = $rule;
        }
        $data = $request->validate($rules);
        // keep only known fields
        return collect($c['fields'])->pluck('name')->mapWithKeys(
            fn ($n) => [$n => $data[$n] ?? $request->input($n)]
        )->filter(fn ($v) => $v !== null)->all();
    }

    public function store(Request $request)
    {
        $c = $this->cfg();
        $model = $c['model'];
        $model::create($this->validated($request, $c));
        return redirect()->route($c['route'])->with('ok', $c['singular'] . ' created.');
    }

    public function update(Request $request, string $id)
    {
        $c = $this->cfg();
        $model = $c['model'];
        $row = $model::findOrFail($id);
        $row->update($this->validated($request, $c));
        return redirect()->route($c['route'])->with('ok', $c['singular'] . ' updated.');
    }

    public function destroy(string $id)
    {
        $c = $this->cfg();
        $model = $c['model'];
        $model::findOrFail($id)->delete();
        return redirect()->route($c['route'])->with('ok', $c['singular'] . ' deleted.');
    }
}
