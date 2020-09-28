<link rel="stylesheet" href={{ asset('css/sale/sale_detail_customer.css' ) }}>

<div id="detail_customer_dialog">
    <div id="detail_customer_dialog_content">
        <div class="title">Thông Tin Khách Hàng</div>
        <table width="90%">
            <tr class="customer_field_row">
                <td class="lbl_name" style="padding-top:0px;">Tên</td>
                <td class="value" style="padding-top:0px;">
                    {{$customer->name}}
                </td>
            </tr>
            <tr class="customer_field_row">
                <td class="lbl_name">Số điện thoại</td>
                <td class="value">
                {{$customer->phone_number}}
            </tr>
            <tr class="customer_field_row">
                <td class="lbl_name">Số điện thoại công khai</td>
                <td class="value">
                    @if($customer->is_public_phone_number)
                        <input type="checkbox" value="" style="width:20px;height:20px;" checked disabled>
                    @else
                        <input type="checkbox" value="" style="width:20px;height:20px;" disabled>
                    @endif
                </td>
            </tr>
            <tr class="customer_field_row">
                <td class="lbl_name">Ngày sinh</td>
                <td class="value">
                    {{$customer->birthday_str}}
                </td>
            </tr>
            <tr class="customer_field_row">
                <td class="lbl_name">Quận/Huyện</td>
                <td class="value">
                    {{$customer->district_name}}
                </td>
            </tr>

            <tr class="customer_field_row">
                <td class="lbl_name">Tỉnh/Thành Phố</td>
                <td class="value">
                    {{$customer->province_name}}
                </td>
            </tr>
            <tr class="customer_field_row">
                <td class="lbl_name">Đường/Phố</td>
                <td class="value">
                    {{$customer->street_name}}
                </td>
            </tr>
            <tr class="customer_field_row">
                <td class="lbl_name">Địa chỉ</td>
                <td class="value">
                    {{$customer->address}}
                </td>
            </tr>
            <tr class="customer_field_row">
                <td class="lbl_name">Trạng Thái</td>
                <td class="value">
                    {{$customer->state_name}}
                </td>
            </tr>
            <tr class="customer_field_row">
                <td class="lbl_name">Landing Page</td>
                <td class="value">
                    {{$customer->landing_page_name}}
                </td>
            </tr>
        </table>

        <table id="list_marketing_product">
            <tr>
                @foreach ($customer->list_marketing_code as $marketing_code)
                    <td>
                        <div class="item">{{$marketing_code}}</div>
                    </td>
                @endforeach

            </tr>
        </table>
        <div id="lbl_marketing_source">Marketing Source
        </div>
        <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
            <tr>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-secondary btn_cancel" id="detail_customer_btn_cancel">Ẩn
                    </button>
                </td>
            </tr>
        </table>
    </div>
</div>
<script>
    $('#detail_customer_btn_cancel').click(function () {
        $('#detail_customer_dialog').css('display', 'none');
    });
</script>
