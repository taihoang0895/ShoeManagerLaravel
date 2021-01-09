@extends ('base_layout')
@section('extra_head')
    <link rel="stylesheet" href={{ asset('css/admin/admin_main.css' ) }}>
    <link rel="stylesheet" href={{ asset('css/extra/bootstrap_4_2_1.css') }}>
    <script>
        function resizeIframe(obj) {
            obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
        }
    </script>
@endsection
@section('content')
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link {{$actives[0]}}" href="/admin/reports/">Tổng quan chung</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{$actives[1]}}" href="/admin/report-product-revenue/">Doanh thu mẫu</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{$actives[2]}}" href="/admin/report-order-type/">Tổng đơn</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{$actives[3]}}" href="/admin/report-order-effection/">Hiệu suất đơn</a>
        </li>
    </ul>
    <iframe src="/admin/overview-report-detail_order_state/" width="100%"  onload="resizeIframe(this)"
            frameborder="0">

    </iframe>
    <iframe src="/admin/overview-report-weekly/" width="100%"  onload="resizeIframe(this)"
            frameborder="0">

    </iframe>
    <script type="text/javascript">
        $(document).ready(function () {
            document.title = 'Báo Cáo';
            $('#admin_menu_item_report').addClass('selected');
        });
    </script>
@endsection

@section('menu')
    @include( "admin.menu")
@endsection
