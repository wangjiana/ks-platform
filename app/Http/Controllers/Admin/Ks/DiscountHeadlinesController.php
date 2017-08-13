<?php

namespace App\Http\Controllers\Admin\Ks;

use App\Http\Controllers\Admin\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * 优惠头条
 * Class DiscountHeadlinesController
 * @package App\Http\Controllers\Admin\Ks
 */
class DiscountHeadlinesController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


       $where_link=array();
        //分类
        $cate= isset($request->cate)?$request->cate:-1;
        $str_where='';
        if ($cate!=-1){
            if($cate==1){
                $cate1=DB::table('cfg_preferential_cate')->pluck('id')->toArray();

                $cate2=DB::table('headline_cate')->whereNotIn('cid',$cate1)->pluck('hid')->toArray();

                if (!empty($cate2)){
                    $temp=implode(',',$cate2);
                    $str_where.=" and b.cid in($temp)";
                }else{
                    $str_where.=" and b.cid in(1)";
                }

            }else{
                $str_where.=" and b.cid=$cate";
            }

            $where_link['cate']=$cate;

        }
        //状态
        $status= isset($request->status)?$request->status:-1;
        if($status!=-1){
            $str_where.=" and a.enabled=$status";
            $where_link['status']=$status;
        }
        //标题
        $title=$request->title;
       if(isset($title)){
           $str_where.=" and a.title like '%$title%'";
           $where_link['title']=$title;
       }
        $cates=DB::select("SELECT * FROM cfg_preferential_cate");

        $sql="(SELECT a.hid, a.createtime ,a.title,(SELECT catename FROM cfg_preferential_cate WHERE id=b.cid ) catename,a.view_count,a.optimize_count,(SELECT COUNT(*) FROM headline_attr WHERE hid=a.hid AND enabled=1 and attr_type='good') num,a.enabled,a.is_top FROM `headline_info` AS a LEFT JOIN headline_cate AS b ON a.hid=b.hid where 1=1 $str_where ) as d";
        $infos=DB::table(DB::raw($sql))->paginate($this->page_size);
        return view('admin.ks.dh.index', ['infos' => $infos, 'page_size' => $this->page_size, 'page_sizes' => $this->page_sizes, 'where_link' => $where_link,'cates'=>$cates,'cate'=>$cate,'status'=>$status,'title'=>$title]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $cates=DB::table('cfg_preferential_cate')->get();
        return view('admin.ks.dh.create',compact('cates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $title=$request->title;
        $is_top=$request->is_top;
        $cate=$request->cate;
        $has_good=$request->has_good;
        $display_type=$request->display_type;
        $intro=$request->intro;
        $insert=[
            'title'=>$title,
            'is_top'=>$is_top,
            'has_good'=>$has_good,
            'display_type'=>$display_type,
            'intro'=>$intro,
            'createtime'=>date('Y-m-d H:i:s',time())
        ];
        $id=DB::table('headline_info')->insertGetId($insert);
        DB::table('headline_cate')->insert([
            'hid'=>$id,
            'cid'=>$cate,
            'enabled'=>1
        ]);
        return redirect()->back()->with('success', '添加成功');


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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
