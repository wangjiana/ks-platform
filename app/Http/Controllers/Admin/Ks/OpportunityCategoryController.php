<?php

namespace App\Http\Controllers\Admin\Ks;

use App\Http\Controllers\Admin\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * 合作机会分类
 * Class OpportunityCategoryController
 * @package App\Http\Controllers\Admin\Ks
 */
class OpportunityCategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //条件
        $infos = DB::table('cfg_coop_cate')->paginate(10);

        return view('admin.ks.oc.index', ['infos' => $infos, 'page_size' => 10]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.ks.oc.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $catename = $request->catename;
        $count=DB::table('cfg_coop_cate')->where('catename',$catename)->count();
        if(!empty($count)){
            return redirect()->back()->with('success', '合作机会分类不允许重名');
        }
        DB::table('cfg_coop_cate')->insert(['catename' => $catename]);

        return redirect()->back()->with('success', '添加成功');


    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return view('admin.ks.user_info.create');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $info = DB::table('cfg_coop_cate')->where('id', $id)->first();

        return view('admin.ks.oc.create', compact('info'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $catename = $request->catename;
        $count=DB::table('cfg_coop_cate')->where('catename',$catename)->whereNotIn('id',[$id])->count();
        if(!empty($count)){
            return redirect()->back()->with('success', '合作机会分类不允许重名');
        }
        DB::table('cfg_coop_cate')->where('id', $id)->update([
            'catename' => $catename
        ]);
        return redirect()->back()->with('success', '更新成功');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //合作机会分类，合作机会关联
        $count=DB::table('cooperation_opportunity_cate')->where('cid', $id)->count();
        if(!empty($count)){
            return response()->json(['msg' => -1,'info'=>'无法删除，已被合作机会使用']);
        }
        DB::table('cfg_coop_cate')->where('id', $id)->delete();
        return response()->json([
            'msg' => 1
        ]);
    }

    function batch_destroy(Request $request)
    {
        $ids = $request->ids;
        DB::table('cfg_coop_cate')->whereIn('id', $ids)->delete();
        return response()->json([
            'msg' => 1
        ]);


    }

}
