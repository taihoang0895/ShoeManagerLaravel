<link rel="stylesheet" href={{asset('css/list_notifications.css') }}>
<script src={{asset('js/list_notifications.js') }}></script>
<meta name="csrf-token" content="{{ Session::token() }}">
<div id="dialog_list_notifications">
    <div id="dialog_list_notifications_content">
        <div class="remind_title">Thông Báo</div>
        <span id="btn_filter">Lọc Chưa Đọc</span>
        <span id="btn_mark_all">Đánh Dấu Đọc Tất Cả</span>
        @if(count($list_notification_rows) > 0)
            <div id="list_notifications">
                <form method="post">
                    @csrf
                    <table style=" clear: left;">
                        @foreach($list_notification_rows as $notification_row)
                            <tr class="remind_item">
                                <td class="content">
                                    <label>{{$notification_row->content}}</label>
                                </td>
                                <td class="btn_mark">
                                    @if($notification_row->unread )
                                        <input type="checkbox" class="cb_mark" value="{{$notification_row->id}}"
                                               id="cb_mark_{{$notification_row->id}}"
                                               style="width:15px;height:15px;" data-toggle="tooltip"
                                               data-placement="top"
                                               title="đánh dấu đã đọc">
                                    @else
                                        <input type="checkbox" value="{{$notification_row->id}}" class="cb_mark"
                                               id="cb_mark_{{$notification_row->id}}"
                                               style="width:15px;height:15px;" checked data-toggle="tooltip"
                                               data-placement="top"
                                               title="đánh dấu chưa đọc">
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                    </table>
                </form>
            </div>
        @else
            <div id="list_remind_empty">
                <img src={{asset('images/ic_list_remind_empty.svg' ) }}>
                <div class="message">Không có thông báo nào</div>
            </div>
        @endif

    </div>

</div>

<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });


</script>
