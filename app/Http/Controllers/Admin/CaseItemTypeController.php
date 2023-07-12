<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\CaseItemType;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class CaseItemTypeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('case_item_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.case-item-type.index');
    }

    public function create()
    {
        abort_if(Gate::denies('case_item_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.case-item-type.create');
    }

    public function edit(CaseItemType $caseItemType)
    {
        abort_if(Gate::denies('case_item_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.case-item-type.edit', compact('caseItemType'));
    }

    public function show(CaseItemType $caseItemType)
    {
        abort_if(Gate::denies('case_item_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.case-item-type.show', compact('caseItemType'));
    }
}
