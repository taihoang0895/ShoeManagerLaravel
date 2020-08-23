<div class="menu_item" id="storekeeper_menu_item_importing_products">
    Nhập Hàng
</div>
<div class="menu_item" id="storekeeper_menu_item_returning_products">
    Hàng Hoàn
</div>
<div class="menu_item" id="storekeeper_menu_item_failed_products">
    Hàng Lỗi
</div>
<div class="menu_item" id="storekeeper_menu_item_inventory_report">
    Tồn Kho
</div>
<div class="menu_item" id="storekeeper_menu_item_product_report">
    Báo Cáo
</div>
@if (Auth::user()->isLeader())
    <div class="menu_item" id="storekeeper_menu_item_history">
        Lịch Sử
    </div>
@endif
<style>
    #storekeeper_menu_item_importing_products:hover {
        cursor: pointer;
    }

    #storekeeper_menu_item_returning_products:hover {
        cursor: pointer;
    }

    #storekeeper_menu_item_failed_products:hover {
        cursor: pointer;
    }

    #storekeeper_menu_item_inventory_report:hover {
        cursor: pointer;
    }

    #storekeeper_menu_item_importing_product_report:hover {
        cursor: pointer;
    }

    #storekeeper_menu_item_exporting_product_report:hover {
        cursor: pointer;
    }

    #storekeeper_menu_item_returning_product_report:hover {
        cursor: pointer;
    }

    #storekeeper_menu_item_product_report:hover {
        cursor: pointer;
    }

    #storekeeper_menu_item_history:hover {
        cursor: pointer;
    }
</style>
<script>
    $(document).ready(function () {
        $('#storekeeper_menu_item_importing_products').click(function () {
            location.href = "/storekeeper/importing-products/";
        });
        $('#storekeeper_menu_item_returning_products').click(function () {
            location.href = "/storekeeper/returning-products/";
        });
        $('#storekeeper_menu_item_failed_products').click(function () {
            location.href = "/storekeeper/failed-products/";
        });
        $('#storekeeper_menu_item_inventory_report').click(function () {
            location.href = "/storekeeper/inventory-report/";
        });

        $('#storekeeper_menu_item_product_report').click(function () {
            location.href = "/storekeeper/importing-product-report/";
        });
        @if (Auth::user()->isLeader())
        $('#storekeeper_menu_item_history').click(function () {
            location.href = "/storekeeper/importing-product-history/";
        });
        @endif

    });

</script>
