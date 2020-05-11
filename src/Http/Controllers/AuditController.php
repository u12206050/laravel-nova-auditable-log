<?php

namespace Day4\AuditableLog\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Nova;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;

class AuditController
{
    public function show(Request $request, $resourceName, $resourceId)
    {
        $record = $this->loadRecord($resourceName, $resourceId);

        abort_if($request->user()->cant('audit', $record), 403, 'Unable to retrieve audits');

        $audits = $this->loadTranslationAudits($record, $record->audits());

        $audits = $audits->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate();

        return response()->json(['status' => 'OK', 'audits' => $audits, 'restore' => $request->user()->can('audit_restore', $record)]);
    }

    /**
     * @param $resourceName
     * @param $resourceId
     *
     * @return Auditable|Model
     */
    protected function loadRecord($resourceName, $resourceId)
    {
        $model = Nova::modelInstanceForKey($resourceName);

        $uses = class_uses($model);

        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', $uses)) {
            $model = $model::withTrashed();

            if (in_array('Astrotomic\Translatable\Translatable', $uses)) {
                $model = $model->with('translations');
            }
        } else if (in_array('Astrotomic\Translatable\Translatable', $uses)) {
            $model = $model::with('translations');
        }

        return $model->find($resourceId);
    }

    protected function loadTranslationAudits($record, $audits) {
        $locale = app()->getLocale();
        try {
            if ($record->hasTranslation($locale)) {
                $t = $record->translate($locale);
                $audits = $audits->orWhere(function($query) use ($t) {
                    $query->where('auditable_id', $t->id)
                        ->where('auditable_type', get_class($t));
                });
            }
        } catch (\Exception $e) {
            // ignore $e;
        }
        return $audits;
    }

    public function delete(Request $request, $resourceName, $resourceId, $auditId)
    {
        $record = $this->loadRecord($resourceName, $resourceId);
        abort_if($request->user()->cant('audit_restore', $record), 403, 'Unable to restore audits');

        $audit = Audit::findOrFail($auditId);
        $audit->delete();

        return response()->json(['status' => 'OK']);
    }

    public function restore(Request $request, $resourceName, $resourceId, $auditId)
    {
        $record = $this->loadRecord($resourceName, $resourceId);
        abort_if($request->user()->cant('audit_restore', $record), 403, 'Unable to restore audits');

        $audit = Audit::findOrFail($auditId);

        $restore = Arr::only($audit->old_values, $request->input('restore', []));

        $record->fill($restore);
        $record->save();

        return response()->json(['status' => 'OK', 'record' => $record]);
    }
}
