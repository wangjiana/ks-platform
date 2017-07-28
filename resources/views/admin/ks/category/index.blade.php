@extends('admin.layouts.default')
@section('t1','品类')
@section('t2','设置')
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <!--box-header-->
                    <div class="box-header">
                        <div class="row">
                            <form class="form-inline" action="@if(isset($level)){{route('admin.ks.category.showSub',$pid)}}@else {{route('admin.ks.category.index')}}@endif">
                                @if(isset($level))
                                    <input type="hidden" name="level" value="{{$level}}">
                                    @endif
                                <div class="col-lg-1 col-xs-3">
                                    <select name="page_size" class="form-control">
                                        @foreach($page_sizes as $k=> $v)
                                            <option @if($page_size==$k) selected @endif value="{{$k}}">{{$v}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-6 col-xs-10">
                                    <div class="input-group">
                                        <input value="{{$where_str}}" name="where_str" type="text" class="form-control"
                                               placeholder="品类名称">
                                        <span class="input-group-btn">
                                    <button class="btn btn-default" type="submit">查询</button>
                                    </span>
                                    </div>

                                </div>
                                @if(Auth::user()->can('admin.ks.category.create'))
                                    <div class="col-lg-2 col-xs-2 pull-right">
                                        <a href="javascript:ce('{{route('admin.ks.category.create')}}',1)" class="btn btn-primary">新增</a>
                                        @if(isset($level))
                                                <a href="{{route('admin.ks.category.index')}}" class="btn btn-primary">返回上级</a>
                                         @endif
                                    </div>
                                @endif

                            </form>

                            <form id="layer_ce" style="display: none" class="box-header form-horizontal" method="post">
                                {{csrf_field()}}
                                <div class="box-body">
                                    <div class="form-group">
                                        @if(isset($level))
                                        <input type="hidden" name="pid" value="{{$pid}}">
                                        @endif
                                        <label for="cat_name" class="col-sm-3 control-label">品类名称</label>

                                        <div class="col-sm-8">
                                            <input value="" name="cat_name" type="text" class="form-control" id="cat_name" placeholder="品类名称" required>
                                        </div>
                                        @if(isset($level)&&$level==2)
                                        <label for="" class="col-sm-3 control-label">上传图片</label>
                                        <div class="col-sm-8">
                                            <img src="">
                                        </div>
                                        @endif
                                    </div>

                                </div>
                                <div class="box-footer  ">
                                    <a href="" class="btn btn-default">返回</a>
                                    <a href="javascript:layer_ce_ajax()" class="btn btn-primary pull-right">保存</a>
                                </div>
                            </form>

                        </div>
                    </div>
                    <!--box-header-->
                    <!--box-body-->
                    <form id="ids">
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-hover">
                                <tr>
                                    <th></th>
                                    <th>序号</th>
                                    @if(isset($level)&&$level==2)
                                        <th>图片</th>
                                    @endif
                                    <th>
                                        @if(isset($level)&&$level==2)
                                            二级品类
                                            @elseif(isset($level)&&$level==3)
                                            三级品类
                                            @else
                                            一级品类
                                        @endif

                                    </th>

                                    <th>操作</th>
                                </tr>
                                @foreach($infos as $info)
                                    <tr>
                                        <th><input class="minimal" name="ids[]" type="checkbox"
                                                   value="{{$info->cat_id}}"></th>
                                        <td>{{$info->cat_id}}</td>
                                        @if(isset($level)&&$level==2)
                                            <td>{{$info->cat_icon}}</td>
                                        @endif
                                        <td>{{$info->cat_name}}</td>
                                        <td>
                                            {{--{{route('admin.ks.category.edit',$info->uid)}}--}}
                                            <a class=" op_edit"  href="javascript:ce('{{route('admin.ks.category.edit',$info->cat_id)}}',2)"
                                               style="margin-right: 10px;display: none">
                                                <i class="fa fa-pencil-square-o " aria-hidden="true">修改</i></a>
                                            @if(!(isset($level)&&$level==3))
                                            <a class=" op_showSub"  href=" @if(isset($level)&&$level==2){{route('admin.ks.category.showSub',[$info->cat_id,'level'=>3])}}@else{{route('admin.ks.category.showSub',[$info->cat_id,'level'=>2])}}@endif"
                                               style="margin-right: 10px;display: none">
                                                <i class="fa fa-pencil-square-o " aria-hidden="true">编辑子分类</i></a>
                                            @endif

                                            <a style="display: none"  class=" op_destroy"  href="javascript:del('{{route('admin.ks.category.destroy',$info->cat_id)}}')">
                                                <i class="fa  fa-trash-o " aria-hidden="true">删除</i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </form>
                    <!--box-body-->
                    <!--box-footer-->
                    <div class="box-footer ">
                        @if(Auth::user()->can('admin.ks.category.batch_destroy'))
                            <div class="btn-group">
                                <button onclick="selectAll()" type="button" class="btn btn-default">全选</button>
                                <button onclick="reverse()" type="button" class="btn btn-default">反选</button>
                                <a href="javascript:batch_destroy()" class="btn btn-danger">批量删除</a>
                            </div>
                        @endif
                        <div style="float: right">
                            @if(isset($level))
                                {{$infos->appends(['where_str' => $where_str,'page_size'=>$page_size,'level'=>$level])->links()}}
                                @else
                                {{$infos->appends(['where_str' => $where_str,'page_size'=>$page_size])->links()}}
                                @endif

                        </div>
                    </div>
                    <!--box-footer-->
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
    <link rel="stylesheet" href="/adminlte/plugins/iCheck/all.css">
@endsection

@section('js')
    <script src="/plugins/layer/layer.js"></script>
    <script src="/adminlte/plugins/iCheck/icheck.min.js"></script>
    <script>
        $('input[type="checkbox"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
    </script>
    <script>
        //有修改权限，显示修改
        @if(Auth::user()->can('admin.ks.category.edit'))
            $(".op_edit").show();
        @endif
        //有删除权限，显示删除
        @if(Auth::user()->can('admin.ks.category.destroy'))
            $(".op_destroy").show();
        @endif
        //子分类
        @if(Auth::user()->can('admin.ks.category.showSub'))
            $(".op_showSub").show();
        @endif
        //批量删除
        function batch_destroy() {
            $cbs = $('table input[type="checkbox"]:checked');
            if ($cbs.length > 0) {
                layer.confirm('确认删除？', {
                    btn: ['确认', '取消']
                },function () {
                    var url='{{route("admin.ks.category.batch_destroy")}}';
                    $.ajax({
                        url: url,
                        type: 'post',
                        data: $("#ids").serialize(),
                        success: function (data) {
                            if (data.msg == 1) {
                                layer.alert('删除成功');
                                location.reload();
                            } else {
                                layer.confirm(data.msg,{
                                    btn: ['确认', '取消']
                                },function () {
                                    url=url+'?flag=true';
                                    $.ajax({
                                        url:url,
                                        type: 'post',
                                        data: $("#ids").serialize(),
                                        success:function (data) {
                                            if (data.msg == 1) {
                                                layer.alert('删除成功');
                                                location.reload();
                                            }else {
                                                layer.alert('删除失败');
                                            }
                                        }
                                    })
                                });
                            }

                        }
                    });
                });

            } else {layer.alert('请选中要删除的列');}}
        //全选
        function selectAll() {
            $('input[type="checkbox"].minimal').iCheck('check')
        }
        //反选
        function reverse() {
            $('input[type="checkbox"].minimal').each(function () {
                if ($(this).is(":checked")) {
                    $(this).iCheck('uncheck');
                } else {
                    $(this).iCheck('check');
                }});}

    </script>
    <script>
        function ce(url,flag) {
            if(flag==1){
                $('#layer_ce').attr('url','{{route('admin.ks.category.store')}}');
                layer.open({
                    title:'品类',
                    type: 1,
                    skin: 'layui-layer-rim', //加上边框
                    area: ['400px',''], //宽高
                    content:$('#layer_ce')
                });
            }
            if(flag==2){
                $.ajax({
                    type:'GET',
                    url:url,
                    success:function (data) {
                        $('#layer_ce').append('{{method_field('PUT')}}');
                        $('#layer_ce').attr('url',data.url);
                        $("#layer_ce input[name='cat_name']").val(data.cat_name);

                        layer.open({
                            title:'品类',
                            type: 1,
                            skin: 'layui-layer-rim', //加上边框
                            area: ['400px',''], //宽高
                            content:$('#layer_ce')
                        });
                    }
                })
            }
        }
        //新增
        function layer_ce_ajax() {
            var url=$('#layer_ce').attr('url');
            $.ajax({
                type:'post',
                url:url,
                data:$('#layer_ce').serialize(),
                success:function (result) {
                    layer.closeAll();
                    if(result.msg==1){
                        layer.alert('操作成功');
                        location.reload();
                    }else{
                        layer.alert(result.msg);
                    }
                }
            });
        }
        //删除
        function del(url) {
            layer.confirm('确认删除？', {
                btn: ['确认', '取消']
            }, function () {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    success: function (data) {
                        if (data.msg == 1) {
                            layer.alert('删除成功');
                            location.reload();
                        } else {
                            layer.confirm(data.msg,{
                                btn: ['确认', '取消']
                            },function () {
                                $.ajax({
                                    url:url,
                                    type: 'DELETE',
                                    data:{'flag':true},
                                    success:function (data) {
                                        if (data.msg == 1) {
                                            layer.alert('删除成功');
                                            location.reload();
                                        }else {
                                            layer.alert('删除失败');
                                        }
                                    }
                                })
                            });
                        }
                    }
                });
            });
        }

    </script>

@endsection