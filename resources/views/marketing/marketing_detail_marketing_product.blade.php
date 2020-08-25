<link rel="stylesheet" href={{ asset('css/marketing/marketing_detail_marketing_product.css')}}>
<div id="show_detail_marketing_product_dialog">
    <div id="show_detail_marketing_product_dialog_content">
        <div class="title" style="margin-top:15px;margin-bottom:30px;">Thông Tin Sản Phẩm Marketing</div>
        <table width="90%;"style="margin:auto;">
            <tr class="marketing_product_field_row">
                <td class="lbl_name_col1" style="padding-top:0px;">MSP Marketing</td>
                <td class="value_col1" style="padding-top:0px;">{{$marketing_product->code}}</td>
                <td class="lbl_name_col2" style="padding-top:0px;">Ngày</td>
                <td class="value_col2" style="padding-top:0px;">{{$marketing_product->createdStr()}}</td>
            </tr>
             <tr class="marketing_product_field_row">
                <td class="lbl_name_col1">MSP</td>
                <td class="value_col1" >{{$marketing_product->product_code}}</td>
                <td class="lbl_name_col2">Tên</td>
                <td class="value_col2" >{{$marketing_product->productName()}}</td>
            </tr>
            <tr class="marketing_product_field_row">
                <td class="lbl_name_col1">Nguồn</td>
                <td class="value_col1" >{{$marketing_product->sourceName()}}</td>
                <td class="lbl_name_col2">Giá Bán</td>
                <td class="value_col2">{{$marketing_product->priceStr()}}&nbsp;&#8363;</td>
            </tr>
            <tr class="marketing_product_field_row">
                  <td class="lbl_name_col1">Ngân Sách</td>
                <td class="value_col1" >{{$marketing_product->totalBudgetStr()}}&nbsp;&#8363;</td>
                <td class="lbl_name_col2">Giá CMT</td>
                <td class="value_col2" >{{$marketing_product->commentCostStr()}}&nbsp;&#8363;</td>

            </tr>
            <tr class="marketing_product_field_row">
                <td class="lbl_name_col1">Cost Đơn</td>
                <td class="value_col1">{{$marketing_product->billCostStr()}}&nbsp;&#8363;</td>
                <td class="lbl_name_col2">CMT</td>
                <td class="value_col2">{{$marketing_product->total_budget}}</td>
            </tr>
            <tr class="marketing_product_field_row">
                <td class="lbl_name_col1">Số Đơn</td>
                <td class="value_col1" >{{$marketing_product->totalBill()}}</td>
                <td class="lbl_name_col2">Data</td>
                <td class="value_col2">{{$marketing_product->totalPhone()}}</td>
            </tr>
        </table>
        <div id="detail_marketing_product_content">
            <table id="tbl_detail_marketing_product">
                <tr class="tbl_detail_marketing_product_header">
                     <th class="campaign_name">
                        Tên Chiến Dịch
                    </th>
                    <th class="bank_account">
                        Số Thẻ
                    </th>
                    <th class="budget">
                       Ngân Sách
                    </th>
                    <th class="total_comment">
                        Số CMT
                    </th>
                </tr>
                @foreach ($marketing_product->list_campaigns as $campaign)
                <tr class="tbl_detail_marketing_product_item">
                    <td style="text-align:center;">
                        <label>{{$campaign->campaignName()}}</label>
                    </td>
                    <td style="text-align:center;">
                        <label>{{$campaign->bankAccount()}}</label>
                    </td>
                    <td style="text-align:center;">
                        <label>{{$campaign->budgetStr()}}&nbsp;&#8363;</label>
                    </td>
                    <td style="text-align:center;">
                        <label>{{$campaign->total_comment}}</label>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
            <tr>
                <td style="text-align:center;">
                    <button type="button" class="btn btn-secondary btn_cancel" id="detail_marketing_product_btn_cancel">Ẩn</button>
                </td>
            </tr>
        </table>

    </div>

</div>

<script>
     $('#detail_marketing_product_btn_cancel').click(function(){
        $('#show_detail_marketing_product_dialog').css('display', 'none');
    });

</script>
