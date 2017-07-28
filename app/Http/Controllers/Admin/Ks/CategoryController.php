<?php

namespace App\Http\Controllers\Admin\Ks;

use App\Http\Controllers\Admin\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * 品类设置
 * Class CategoryController
 * @package App\Http\Controllers\Admin\Ks
 */
class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $where_str = $request->where_str;
        $where = array();

        $where[]=['parent_id','=',0];
        if (isset($where_str)) {
            $where[] = ['cat_name', 'like', '%' . $where_str . '%'];

        }

        //条件
        $infos=DB::table('cfg_category')->select(['cat_name','cat_id'])->where($where)->paginate($this->page_size);

        return view('admin.ks.category.index',['infos'=>$infos,'page_size' => $this->page_size, 'page_sizes' => $this->page_sizes,'where_str' => $where_str]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        //
        return view('admin.ks.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $cat_name=$request->cat_name;
        $pid=$request->pid;
        $where=array('cat_name'=>$cat_name);
        if(isset($pid)){
            $where['parent_id']=$pid;
        }else{
            $pid=0;
        }

        $count= DB::table('cfg_category')->where($where)->count();
        if(!empty($count)){
            return response()->json(['msg'=>'存在相同品类名称']);
        }
        $insert=[
            'cat_name'=>$cat_name,
            'parent_id'=>$pid,
            'createtime'=>date('Y-m-d H:i:s',time())
        ];


       if( DB::table('cfg_category')->insert($insert)){
           return response()->json(['msg'=>1]);
       }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $info = DB::table('cfg_category')->where('cat_id',$id)->first();
        $info->url=route('admin.ks.category.update',$id);
        return response()->json($info);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cat_name=$request->cat_name;
        $where=array();
        $where[]=['cat_name','=',$cat_name];
        $where[]=['cat_id','!=',$id];
        $count= DB::table('cfg_category')->where($where)->count();
        if (!empty($count)) {
            return response()->json(['msg'=>'存在相同品类名称']);
        }

        DB::table('cfg_category')->where('cat_id',$id)->update(['cat_name' => $cat_name]);
        return response()->json(['msg'=>1]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        //确认删除子分类
        if(isset($request->flag)){
            //当前分类下，子分类
            $infos=DB::table('cfg_category')->where('parent_id',$id)->select('cat_id')->get()->toArray();
            $ids=array();
            foreach ($infos as $info){
                $ids[]=$info->cat_id;
            }
            DB::table('cfg_category')->whereIn('parent_id', $ids)->delete();//三级分类
            DB::table('cfg_category')->whereIn('cat_id', $ids)->delete();//二级分类
            DB::table('cfg_category')->where('cat_id', $id)->delete();//一级分类

            return response()->json(['msg' => 1]);
        }
        $count=DB::table('cfg_category')->where('parent_id',$id)->count();
        if(!empty($count)){
            return response()->json(['msg'=>'该分类下有子分类，是否一起删除?']);
        }

        DB::table('cfg_category')->where('cat_id', $id)->delete();
        return response()->json(['msg' => 1]);


    }
    function batch_destroy(Request $request){
        $ids = $request->ids;

        //确认删除子分类
        if(isset($request->flag)){
            //当前分类下，子分类
            $infos=DB::table('cfg_category')->whereIn('parent_id',$ids)->select('cat_id')->get()->toArray();
            $idss=array();
            foreach ($infos as $info){
                $idss[]=$info->cat_id;
            }
            DB::table('cfg_category')->whereIn('parent_id', $idss)->delete();//子分类的子分类
            DB::table('cfg_category')->whereIn('cat_id', $idss)->delete();//子分类
            DB::table('cfg_category')->whereIn('cat_id', $ids)->delete();//当前分类

            return response()->json(['msg' => 1]);
        }
        $count=DB::table('cfg_category')->whereIn('parent_id',$ids)->count();
        if(!empty($count)){
            return response()->json(['msg'=>'该分类下有子分类，是否一起删除?']);
        }

        DB::table('cfg_category')->whereIn('cat_id', $ids)->delete();
        return response()->json(['msg' => 1]);

    }

    /**
     * 展示子分类
     */
    function showSub(Request $request,$id){


        $where_str = $request->where_str;
        $where = array();

        $where[]=['parent_id','=',$id];
        if (isset($where_str)) {
            $where[] = ['cat_name', 'like', '%' . $where_str . '%'];

        }

        //条件
        $infos=DB::table('cfg_category')->select(['cat_name','cat_id','cat_icon'])->where($where)->paginate($this->page_size);

        return view('admin.ks.category.index',['infos'=>$infos,'page_size' => $this->page_size, 'page_sizes' => $this->page_sizes,'where_str' => $where_str,'level'=>$request->level,'pid'=>$id]);

    }
}