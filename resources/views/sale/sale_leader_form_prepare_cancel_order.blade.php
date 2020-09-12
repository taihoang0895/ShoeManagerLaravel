<link rel="stylesheet" href={{ asset('css/sale/sale_leader_form_prepare_cancel_order.css') }}>
<script src={{ asset('js/sale/sale_leader_form_prepare_cancel_order.js') }}></script>
<meta name="csrf-token" content="{{ Session::token() }}">
<form method="post">
    @csrf
    <div id="cancel_order_dialog">
        <div id="cancel_order_dialog_content">
            <div class="title">Danh Sách Hủy Hóa Đơn</div>
            <table class="tbl_cancel_order" id="tbl_cancel_order">

                <tr class="tbl_header_item">
                    <td class="col_mark" id="col_mark_header" width="70px">
                    </td>
                    <td class="order_code" id="order_code_header">MHD</td>
                    <td class="ghtk_code" id="ghtk_code_header">Mã GHTK</td>
                    <td class="created" id="created_header">Ngày Lập</td>
                    <td class="order_state" id="order_state_header">Trạng Thái Trước</td>
                    <td class="new_order_state" id="new_order_state_header">Trạng Thái Sau</td>
                </tr>
                @foreach ($list_orders as $order)
                    <tr class="tbl_item cancel_order_row" id="cancel_order_row_{{$order->id}}">
                        <input type="hidden" value="{{$order->id}}" class="order_id">
                        <td class="col_mark">
                            <div class="loader"></div>
                            <img class="img_failed" src={{ asset('images/ic_failed.png'  ) }}>
                            <img class="img_success" src={{ asset('images/ic_success.png') }}>
                            <input type="hidden" class="order_id" value="{{$order->id}}">
                        </td>
                        <td class="order_code">{{$order->code}}</td>
                        <td class="ghtk_code">{{$order->ghtk_label}}</td>
                        <td class="created">{{$order->created_str}}</td>
                        <td class="order_state">{{$order->order_state_name}}</td>
                        <td class="new_order_state"></td>
                    </tr>
                @endforeach
            </table>
            <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
                <tr>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_ok" id="order_synchronizer_btn_ok">Bắt Đầu
                        </button>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_cancel" id="order_synchronizer_btn_cancel">Ẩn
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>


