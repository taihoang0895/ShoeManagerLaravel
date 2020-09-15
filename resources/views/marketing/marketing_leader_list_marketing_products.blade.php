@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/marketing/marketing_main.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/marketing/marketing_leader_list_marketing_products.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css') }}>


    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href={{ asset('main/css/extra/tempusdominus-bootstrap-4.css' ) }}>
    <script src={{ asset('js/extra/tempusdominus-moment.js') }}></script>
    <script src={{ asset('js/extra/tempusdominus-bootstrap-4.js' ) }}></script>
    <script src={{ asset('js/marketing/marketing_leader_list_marketing_products.js' ) }}></script>
    <link rel="stylesheet" type="text/css" href="{{asset('jqueryui/jquery-ui.min.css')}}">
    <script src="{{asset('jqueryui/jquery-ui.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript" charset="UTF-8" src={{ asset("jqueryui/bootstrap-datepicker.vi.js") }}></script>
    <meta name="csrf-token" content="{{ Session::token() }}">
@endsection
@section('content')
    @include("confirm_dialog", ["confirm_dialog_id"=>"confirm_dialog_delete_marketing_product", "confirm_dialog_btn_positive_id"=>"marketing_product_delete_dialog_btn_ok","confirm_dialog_btn_negative_id"=>"marketing_product_delete_dialog_btn_cancel", "confirm_dialog_message"=>"Bạn có chắc chắn muốn xóa không?"])
    @csrf
    <div class="title">Danh Sách Sản Phẩm Marketing</div>
    <table id="list_marketing_product_filter">
        <input type="hidden" id="filter_marketing_source_id_selected" value="{{$marketing_source_id}}">
        <input type="hidden" id="filter_member_id_selected" value="{{$filter_member_id}}">
        <tr>
            <td>
                @include("autocomplete", ["autocomplete_id"=>"search_product_code", "autocomplete_placeholder"=>"Nhập mã sản phẩm",
                                          "autocomplete_value"=>$search_product_code, "autocomplete_data"=>$list_product_codes])
            </td>
            <td class="filter_start_time">
                <div class="input-group date" id="marketing_product_filter_start_time" data-target-input="nearest">
                    <label style="margin-top:6px;">Từ ngày&nbsp;&nbsp;</label>
                    <input type="text" class="form-control datetimepicker-input"
                           data-target="#marketing_product_filter_start_time"
                           placeholder="dd/mm/yyyy" id="marketing_product_filter_start_time_text"
                           value="{{$start_time_str}}"/>
                    <div class="input-group-append" data-target="#marketing_product_filter_start_time"
                         data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>

            </td>
            <td class="filter_end_time">
                <div class="input-group date" id="marketing_product_filter_end_time" data-target-input="nearest">
                    <label style="margin-top:6px;">&nbsp;&nbsp;&nbsp;Đến ngày&nbsp;&nbsp;</label>
                    <input type="text" class="form-control datetimepicker-input"
                           data-target="#marketing_product_filter_end_time"
                           placeholder="dd/mm/yyyy" id="marketing_product_filter_end_time_text"
                           value="{{$end_time_str}}"/>
                    <div class="input-group-append" data-target="#marketing_product_filter_end_time"
                         data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </td>
            <td class="filter_by_marketing_product_state">
                <div class="dropdown" id="filter_marketing_source_dropdown_state">
                    <button class="btn btn-secondary dropdown-toggle" type="button"
                            id="filter_marketing_source_dropdown_state_text"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @if($marketing_source_id == -1)
                            Chọn nguồn
                        @else
                            {{$filter_marketing_source_str}}
                        @endif
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item"><input type="hidden" value="-1">_______</a>
                        @foreach($list_marketing_sources as $marketing_source)
                            <a class="dropdown-item"><input type="hidden"
                                                            value="{{$marketing_source->id}}">{{$marketing_source->name}}
                            </a>
                        @endforeach
                    </div>
                </div>

            </td>

            <td class="filter_by_member">
                <div class="dropdown" id="filter_by_member">
                    <button class="btn btn-secondary dropdown-toggle" type="button"
                            id="filter_by_member_text"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @if($filter_member_id == -1)
                            Chọn Người Tạo
                        @else
                            {{$filter_member_str}}
                        @endif
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item"><input type="hidden" value="-1">_______</a>
                        @foreach($list_members as $member)
                            <a class="dropdown-item"><input type="hidden"
                                                            value="{{$member->id}}">{{$member->alias_name}}</a>
                        @endforeach
                    </div>
                </div>

            </td>

            <td class="btn_filter">
                <button type="button" class="btn btn-warning btn_filter" id="marketing_product_btn_filter">Lọc</button>
            </td>
        </tr>
    </table>

    <table width="40%" style="margin-left:auto;margin-right:auto;margin-top : 15px;">
        <tr>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_add item" id="list_marketing_products_btn_add">Thêm
                </button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_update item" id="list_marketing_products_btn_update">
                    Sửa
                </button>
            </td>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_delete item" id="list_marketing_products_btn_delete">
                    Xóa
                </button>
            </td>
        </tr>
    </table>

    <table class="tbl">
        <tr class="tbl_header_item">
            <td class="marketing_code">Mã</td>
            <td class="marketing_product_code">MSP</td>
            <td class="marketing_product_created">Ngày</td>
            <td class="marketing_product_source">Nguồn</td>
            <td class="marketing_product_cmt_cost">Giá CMT</td>
            <td class="marketing_product_price">Giá Bán</td>
            <td class="marketing_product_bill_cost">Cost Đơn</td>

            <td class="detail"></td>
        </tr>
        @foreach ($list_marketing_products as $marketing_product)

            <tr class="tbl_item marketing_product_row" id="marketing_product_row_{{$marketing_product->id}}">
                <td class="marketing_code">
                    {{$marketing_product->code}}
                </td>
                <td class="marketing_product_code">
                    {{$marketing_product->product_code}}
                </td>
                <td class="marketing_product_created">
                    {{$marketing_product->createdStr()}}
                </td>
                <td class="marketing_product_source">
                    {{$marketing_product->sourceName()}}
                </td>
                <td class="marketing_product_cmt_cost"
                    style="background-color:{{$marketing_product->commentCostColor()}};">
                    {{$marketing_product->commentCostStr()}}&nbsp;&#8363;
                </td>
                <td class="marketing_product_price">
                    {{$marketing_product->priceStr()}}&nbsp;&#8363;
                </td>
                <td class="marketing_product_bill_cost"
                    style="background-color:{{$marketing_product->billCostColor()}};">
                    {{$marketing_product->billCostStr()}}&nbsp;&#8363;


                </td>
                <td class="show_detail_markting_product" style="text-align:center"><input type="hidden"
                                                                                          value="{{$marketing_product->id}}">Xem
                    chi tiết
                </td>
            </tr>
        @endforeach
    </table>
    @if (count($list_marketing_products) == 0)
        <div class="empty">Danh sách sản phẩm rỗng</div>
    @endif
    <table width="10%" style="margin-left:auto;margin-right:auto;margin-top : 15px; margin-bottom:30px;">
        <tr>
            <td>
                {{$list_marketing_products->withQueryString()->links()}}
            </td>

        </tr>
    </table>

    <div id="dialog_edit_marketing_product"></div>

    <script>

        $(document).ready(function () {
            //$.fn.datetimepicker.defaults.language = 'nl';
            $("#marketing_product_filter_start_time").datetimepicker({
                format: 'DD/MM/YYYY',
            });
            $("#marketing_product_filter_end_time").datetimepicker({
                format: 'DD/MM/YYYY',
            });
            document.title = 'Marketing';
            $('#marketing_menu_item_marketing').addClass('selected');
        })
        ;
    </script>
@endsection

@section('menu')
    @include( "marketing.menu")
@endsection
