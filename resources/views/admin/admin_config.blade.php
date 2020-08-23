@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css') }}>
    <link rel="stylesheet" href={{ asset('css/admin/admin_main.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/admin/admin_config.css'  ) }}>
    <script src={{ asset('js/admin/admin_config.js' ) }}></script>
    <meta name="csrf-token" content="{{ Session::token() }}">
@endsection

@section('content')
    @csrf
    <div class="title">Cấu Hình</div>


    <table id="tbl_config">
        <tr class="config_row">
            <td class="lbl_name" style="padding-top:0px;">Ngưỡng Bill Cost</td>
            <td class="value" style="padding-top:0px;"><input class="form-control" type="number"
                                                              id="bill_cost_threshold"
                                                              value="{{$config->threshold_bill_cost_green}}">
            </td>
        </tr>
        <tr class="config_row">
            <td class="lbl_name">Ngưỡng Comment Cost</td>
            <td class="value"><input class="form-control" type="number"
                                     id="comment_cost_threshold" value="{{$config->threshold_comment_cost_green}}"></td>
        </tr>
    </table>
    <table width="40%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
        <tr>
            <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn_ok" id="config_btn_save">Lưu</button>
            </td>
        </tr>
    </table>
    <script type="text/javascript">
        $(document).ready(function () {
            document.title = 'Cấu Hình';
            $('#admin_menu_item_config').addClass('selected');
        });


    </script>
@endsection

@section('menu')
    @include( "admin.menu")
@endsection
