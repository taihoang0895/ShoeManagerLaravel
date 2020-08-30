<div class="menu_item" id="sale_menu_item_customers">
    Khách Hàng
</div>
<div class="menu_item" id="sale_menu_item_products">
    Sản Phẩm
</div>
@if (Auth::user()->isLeader())
<div class="menu_item" id="sale_menu_item_order_fail_reasons">
    Lý Do Lỗi
</div>
@endif
<div class="menu_item" id="sale_menu_item_orders">
    Hóa Đơn
</div>
@if (Auth::user()->isLeader())
<div class="menu_item" id="sale_menu_item_order_deliver" style="display: none">
    Đăng Đơn
</div>
<div class="menu_item" id="sale_menu_item_order_state_manager"  style="display: none">
    Đơn Hàng
</div>
@endif
<div class="menu_item" id="sale_menu_item_schedules">
    Nhắc Nhở
</div>
<div class="menu_item" id="sale_menu_item_discounts">
    Khuyến mại
</div>
@if (Auth::user()->isLeader())
<div class="menu_item" id="sale_menu_item_order_history">
    Lịch Sử
</div>
@endif
<style>
    #sale_menu_item_customers:hover{
    cursor: pointer;
    }

    #sale_menu_item_products:hover{
        cursor: pointer;
    }
    #sale_menu_item_orders:hover{
        cursor: pointer;
    }
    #sale_menu_item_schedules:hover{
        cursor: pointer;
    }
    #sale_menu_item_order_fail_reasons:hover{
        cursor: pointer;
    }
    #sale_menu_item_discounts:hover{
        cursor: pointer;
    }
    #sale_menu_item_order_history:hover{
        cursor: pointer;
    }
    #sale_menu_item_order_deliver:hover{
        cursor: pointer;
    }
    #sale_menu_item_order_state_manager:hover{
        cursor: pointer;
    }
</style>
<script>
    $(document).ready(function () {
    $('#sale_menu_item_customers').click(function(){
            location.href = "/sale/customers/";
    });
     $('#sale_menu_item_products').click(function(){
        location.href = "/sale/products/";
    });
     $('#sale_menu_item_orders').click(function(){
         location.href = "/sale/orders/";
    });
     $('#sale_menu_item_schedules').click(function(){
         location.href = "/sale/schedules/";
    })
        @if (Auth::user()->isLeader())
        $('#sale_menu_item_order_fail_reasons').click(function(){
             location.href = "/sale/order-fail-reasons/";
        });
        $('#sale_menu_item_order_history').click(function(){
             location.href = "/sale/exporting-product-history/";
        });
        $('#sale_menu_item_order_deliver').click(function(){
             location.href = "/sale/order-deliver/";
        });
        $('#sale_menu_item_order_state_manager').click(function(){
             location.href = "/sale/order-state-manager/";
        });
    @endif
    $('#sale_menu_item_discounts').click(function(){
         location.href = "/sale/discounts/";
    })

});

</script>
