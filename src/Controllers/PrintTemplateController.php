<?php

namespace Xx19941215\PrintTemplate\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Oh86\Http\Response\ErrorResponse;
use Oh86\Http\Response\OkResponse;
use Xx19941215\PrintTemplate\Models\PrintTemplate;

class PrintTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param \Illuminate\Http\Request $request
     * @return \Oh86\Http\Response\OkResponse|\Oh86\Http\Response\ErrorResponse
     */
    public function getList(Request $request)
    {
        $request->validate([
            'assoc_type' => 'nullable|string',
            'assoc_id' => 'nullable|integer',
            'name' => 'nullable|string|max:255',
            'with_creator' => 'nullable|boolean',
            'with_modifier' => 'nullable|boolean',
            'offset' => 'required|int',
            'limit' => 'required|int',
        ]);

        // 获取当前用户ID
        $user = Auth::guard('gw')->user();

        if (!$user) {
            return new ErrorResponse(1, '用户未登录');
        }

        $orgId = $user->getCurOrgId();

        $builder = PrintTemplate::query()->where('org_id', $orgId);
        $assocType = $request->get('assoc_type');
        $assocId = $request->get('assoc_id');
        $name = $request->get('name');
        $withCreator = $request->get('with_creator', false);
        $withModifier = $request->get('with_modifier', false);

        if ($assocType) {
            $builder->where('assoc_type', $assocType);
        }

        if ($assocId) {
            $builder->where('assoc_id', $assocId);
        }

        if ($name) {
            $builder->where('name', 'LIKE', "%" . $name . "%");
        }

        // 根据参数决定是否预加载关系
        if ($withCreator) {
            $builder->with('creator');
        }
        
        if ($withModifier) {
            $builder->with('modifier');
        }

        $total = $builder->count();
        $list = $builder->orderBy('modified_at', 'desc')
            ->offset($request->offset)
            ->limit($request->limit)
            ->select([
                'id',
                'org_id',
                'assoc_type',
                'assoc_id',
                'code',
                'name',
                'creator_id',
                'modifier_id',
                'modified_at',
                'created_at',
                'deleted_at',
                'updated_at',
            ])
            ->get();

        return new OkResponse(compact('total', 'list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Oh86\Http\Response\OkResponse|\Oh86\Http\Response\ErrorResponse
     */
    public function create(Request $request)
    {
        $request->validate([
            'assoc_type' => 'required|string',
            'assoc_id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'config' => 'nullable|array',
        ]);

        try {
            // 获取当前用户ID
            $user = Auth::guard('gw')->user();

            if (!$user) {
                return new ErrorResponse(1, '用户未登录');
            }

            $userId = $user->id;
            $orgId = $user->getCurOrgId();

            $template = new PrintTemplate();
            $template->org_id = $orgId;
            $template->assoc_type = $request->input('assoc_type');
            $template->assoc_id = $request->input('assoc_id');
            $template->name = $request->input('name');
            $template->config = $request->input('config');
            $template->creator_id = $userId;
            $template->modifier_id = $userId;
            $template->modified_at = now();
            $template->code = PrintTemplate::genCode($orgId);
            $template->save();

            return new OkResponse($template);
        } catch (\Exception $e) {
            return new ErrorResponse(1, '创建模板失败: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Request $request
     * @return \Oh86\Http\Response\OkResponse|\Oh86\Http\Response\ErrorResponse
     */
    public function getInfo(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:print_templates,id',
            'with_creator' => 'nullable|boolean',
            'with_modifier' => 'nullable|boolean',
        ]);
        $id = $request->input('id');
        $withCreator = $request->get('with_creator', false);
        $withModifier = $request->get('with_modifier', false);

        // 获取当前用户ID
        $user = Auth::guard('gw')->user();

        if (!$user) {
            return new ErrorResponse(1, '用户未登录');
        }

        $orgId = $user->getCurOrgId();


        $builder = PrintTemplate::query()->where('org_id', $orgId);
        
        // 根据参数决定是否预加载关系
        if ($withCreator) {
            $builder->with('creator');
        }
        
        if ($withModifier) {
            $builder->with('modifier');
        }

        $template = $builder->find($id);
        
        if (!$template) {
            return new ErrorResponse(1, '模板不存在');
        }

        return new OkResponse($template);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Oh86\Http\Response\OkResponse|\Oh86\Http\Response\ErrorResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:print_templates,id',
            'name' => 'required|string|max:255',
            'config' => 'nullable|array',
        ]);
        $id = $request->input('id');

        // 获取当前用户ID
        $user = Auth::guard('gw')->user();

        if (!$user) {
            return new ErrorResponse(1, '用户未登录');
        }

        $orgId = $user->getCurOrgId();

        $template = PrintTemplate::query()->where('org_id', $orgId)->find($id);
        
        if (!$template) {
            return new ErrorResponse(1, '模板不存在');
        }

        try {
            // 获取当前用户ID
            $userId = Auth::id();
            if (!$userId) {
                return new ErrorResponse(1, '用户未登录');
            }

            $template->assoc_type = $request->input('assoc_type');
            $template->assoc_id = $request->input('assoc_id');
            $template->name = $request->input('name');
            $template->config = $request->input('config');
            $template->modifier_id = $userId;
            $template->modified_at = now();
            $template->save();

            return new OkResponse($template);
        } catch (\Exception $e) {
            return new ErrorResponse(1, '更新模板失败: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @return \Oh86\Http\Response\OkResponse|\Oh86\Http\Response\ErrorResponse
     */
    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:print_templates,id',
        ]);
        $id = $request->input('id');

        // 获取当前用户ID
        $user = Auth::guard('gw')->user();

        if (!$user) {
            return new ErrorResponse(1, '用户未登录');
        }

        $orgId = $user->getCurOrgId();

        $template = PrintTemplate::query()->where('org_id', $orgId)->find($id);
        
        if (!$template) {
            return new ErrorResponse(1, '模板不存在');
        }
        
        $template->delete();

        return new OkResponse();
    }
}