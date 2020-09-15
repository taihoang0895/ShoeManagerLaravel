<link rel="stylesheet" href={{ asset('css/marketing/marketing_edit_marketing_products.css' ) }}>
<script src={{ asset('js/marketing/marketing_edit_marketing_products.js' ) }}></script>
<meta name="csrf-token" content="{{ Session::token() }}">
<form method="post">
    @csrf
    <div id="edit_marketing_product_dialog">
        <input type="hidden" id="edit_marketing_product_id" value="{{$marketing_product_id}}">
        <input type="hidden" id="edit_marketing_product_source_id" value="{{$marketing_product_source_id}}">

        <div id="edit_marketing_product_dialog_content">

            <div class="title" style="margin-top:15px;margin-bottom:30px;">Nhập Thông Tin Marketing</div>
            <table width="90%">
                <tr class="marketing_product_field_row">
                    <td class="name_col">Mã Marketing</td>
                    <td class="value_col">
                        @if( $marketing_product_id == -1 )
                            <input class="form-control" type="text" placeholder="Nhập mã marketing"
                                   id="edit_marketing_product_marketing_code"
                                   value="{{$marketing_code}}"></td>

                        @else
                            <input class="form-control" type="text" placeholder="Nhập mã marketing"
                                   id="edit_marketing_product_marketing_code"
                                   value="{{$marketing_code}}" disabled></td>
                    @endif
                    </td>
                </tr>

                <tr class="marketing_product_field_row">
                    <td class="name_col">Mã Sản Phẩm</td>
                    <td class="value_col">
                        @if( $marketing_product_id == -1 )
                            @include("autocomplete", ["autocomplete_id"=>"edit_marketing_product_product_code", "autocomplete_placeholder"=>"Nhập mã sản phẩm",
                                                      "autocomplete_value"=>"", "autocomplete_data"=>$list_product_codes])


                        @else
                            <input class="form-control" type="text" placeholder="Nhập mã sản phẩm"
                                   id="edit_marketing_product_product_code"
                                   value="{{$marketing_product_code}}" disabled></td>
                    @endif
                    </td>
                </tr>
                <tr class="marketing_product_field_row">
                    <td class="name_col">Nguồn</td>
                    <td class="value_col">
                        <div class="dropdown" id="edit_marketing_product_product_source">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                    id="edit_marketing_product_product_source_text"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{$marketing_source}}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                @foreach ($list_marketing_sources as $marketing_source)

                                    <a class="dropdown-item"><input type="hidden" value="{{$marketing_source->id}}"
                                                                    class="marketing_source_id">{{$marketing_source->name}}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="marketing_product_field_row">
                    <td class="name_col">Ngày Tạo</td>
                    <td class="value_col">
                        <div class="input-group date" id="edit_marketing_product_created" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input"
                                   data-target="#edit_marketing_product_created"
                                   placeholder="dd/mm/yyyy" id="edit_marketing_product_created_text"
                                   value="{{$marketing_product_created}}"/>
                            <div class="input-group-append" data-target="#edit_marketing_product_created"
                                 data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            <div id="list_campaigns">
                <table width="100%" class="tbl_list_campaigns" id="tbl_list_campaigns">

                    <tr class="tbl_detail_campaign_header">
                        <th class="campaign_name">
                            Tên Chiến Dịch
                        </th>
                        <th class="bank_account_number">
                            Số Thẻ
                        </th>
                        <th class="budget">
                            Ngân Sách
                        </th>
                        <th class="comment">
                            Tổng Comment
                        </th>
                        <th class="button">

                        </th>
                    </tr>
                    <tr class="tbl_detail_campaign_item" id="row_additional_detail_campaign">

                        <td style="text-align:center;">
                            <input type="hidden" id="detail_campaign_additional_campaign_name_selected_id"
                                   value="{{$detail_campaign_additional_campaign_name_selected_id}}">
                            <div class="dropdown" id="detail_campaign_additional_campaign_name">
                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                        id="detail_campaign_additional_campaign_name_text"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    ___
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="max-height:200px;overflow-y: auto;">
                                    @foreach ($list_campaign_names as $campaign_name)
                                        <a class="dropdown-item"><input type="hidden"
                                                                        value="{{$campaign_name->id}}">{{$campaign_name->name}}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <input type="hidden" id="detail_campaign_additional_bank_account_selected_id"
                                   value="{{$detail_campaign_additional_bank_account_selected_id}}">
                            <div class="dropdown" id="detail_campaign_additional_bank_account">
                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                        id="detail_campaign_additional_bank_account_text"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    ___
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="max-height:200px;overflow-y: auto;">
                                    @foreach ($list_bank_accounts as $bank_account)
                                        <a class="dropdown-item"><input type="hidden"
                                                                        value="{{$bank_account->id}}">{{$bank_account->name}}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                        <td>
                            <input class="form-control" type="number" placeholder="Nhập ngân sách" min="0" value="0"
                                   style="width:70%;margin: 0 auto;" id="detail_campaign_additional_budget">
                        </td>
                        <td>
                            <input class="form-control" type="number" placeholder="Nhập số comment"
                                   style="width:70%;margin: 0 auto;" id="detail_campaign_additional_comment" min="1"
                                   value="1">
                        </td>

                        <td style="text-align:center;">
                            <button type="button" class="btn btn-success detail_campaign_btn_add"
                                    id="detail_campaign_btn_add">
                                Thêm
                            </button>
                        </td>
                    </tr>

                </table>
            </div>
            <table width="50%" style="margin-left:auto;margin-right:auto;margin-top : 15px;margin-bottom:15px">
                <tr>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_ok" id="edit_marketing_product_btn_ok">Lưu
                        </button>
                    </td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-secondary btn_cancel"
                                id="edit_marketing_product_btn_cancel">Hủy
                        </button>
                    </td>
                </tr>
            </table>

        </div>

    </div>

</form>


<script>

    $(function () {

        $("#edit_marketing_product_created").datetimepicker({
            format: 'DD/MM/YYYY',
        });
        @foreach ($list_campaigns as $campaign)
        var row_index = $('.tbl_detail_campaign_item').length;
        $('#row_additional_detail_campaign').after(genRow(
            '{{$campaign->campaign_name_id}}',
            '{{$campaign->bank_account_id}}',
            '{{$campaign->budget}}',
            '{{$campaign->total_comment}}',
            row_index));

        $('.detail_campaign_updating_bank_account a').click(updatingRowBankAccountSelected);
        $('.detail_campaign_updating_campaign_name a').click(updatingRowCampaignNameSelected);

        $('.detail_campaign_btn_update').first().click(handleUpdateBtnClicked);
        $('.detail_campaign_btn_delete').first().click(handleDeleteBtnClicked);
        $('#detail_campaign_additional_bank_account_number').val("");
        $('#detail_campaign_additional_budget').val("");
        $('#detail_campaign_additional_comment').val("1");
        @endforeach


    });


</script>
