<?php

namespace App\Http\Controllers\Admin\Ks;

use App\Http\Controllers\Admin\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * 商品管理
 * Class GoodsController
 * @package App\Http\Controllers\Admin\Ks
 */
class GoodsController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $str_where='';
        $where_link['page_size']=$this->page_size;
        //
        $where_str = $request->where_str;
        if (!empty($where_str)) {
            $str_where.=" and c.company like '%$where_str%' or a.goods_smallname like '%$where_str%' or a.goods_name like '%$where_str%'";
            $where_link['where_str']=$where_str;
        }
        //品牌
        $brand= isset($request->brand)?$request->brand:-1;
        if($brand!=-1){
            $str_where.=" and a.bid=$brand";

            $where_link['brand']=$brand;

        }
        //区域
        $area= isset($request->area)?$request->area:-1;
        if ($area!=-1){
            $str_where.=" and c.provice='$area'";
            $where_link['area']=$area;
        }
        //商品标签
        $label= isset($request->label)?$request->label:-1;
        if ($label!=-1){
            switch ($label) {
                case 1:
                    $str_where.=" and a.is_hot=1";
                    break;
                case 2:
                    $str_where.=" and a.is_new=1";
                    break;
                case 3:
                    $str_where.=" and a.is_cuxiao=1";
                    break;
            }
            $where_link['label']=$label;

        }


        //区域数组
        $provices_arr=DB::select("SELECT DISTINCT provice FROM `user` WHERE uid in (SELECT uid FROM merchant WHERE sr_id IN (SELECT DISTINCT sr_id FROM `goods` ))");
        $provices=array();
        foreach ($provices_arr as $provice){
            $provices[]=$provice->provice;
        }
        //品牌数组
        $brands=DB::select("SELECT bid,zybrand FROM cfg_brand");




        $sql = "(SELECT a.goods_id,c.provice,c.company,d.zybrand,a.goods_smallname,a.goods_name,f.cat_name,a.sell_count,a.is_hot,a.is_new,is_cuxiao FROM `goods` AS a
LEFT JOIN merchant AS b ON a.sr_id=b.sr_id
LEFT JOIN `user` AS c ON c.uid=b.uid
LEFT JOIN cfg_brand AS d ON a.bid=d.bid
LEFT JOIN goods_category_rela AS e ON a.goods_id=e.good_id
LEFT JOIN cfg_category AS f ON e.cat_id=f.cat_id where 1=1 $str_where) as g";
        $infos = DB::table(DB::raw($sql))->paginate($this->page_size);



        return view('admin.ks.goods.index', ['infos' => $infos, 'page_size' => $this->page_size, 'page_sizes' => $this->page_sizes, 'where_str' => $where_str,'where_link' => $where_link,'provices'=>$provices,'brands'=>$brands,'area'=>$area,'brand'=>$brand,'label'=>$label]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        return view('admin.ks.goods.create');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
