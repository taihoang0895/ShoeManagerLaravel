<div class="menu_item" id="marketing_menu_item_product">
    Sản Phẩm
</div>
<div class="menu_item" id="marketing_menu_item_marketing">
    Marketing
</div>
<div class="menu_item" id="marketing_menu_item_inventory_report">
    Tồn Kho
</div>
@if (Auth::user()->isLeader())
    <div class="menu_item" id="marketing_menu_item_marketing_source">
        Nguồn Marketing
    </div>
    <div class="menu_item" id="marketing_menu_item_bank_account">
        Thẻ Tín Dụng
    </div>
@endif
<div class="menu_item" id="marketing_menu_item_report">
    Báo Cáo
</div>
<style>
    #marketing_menu_item_product:hover {
        cursor: pointer;
    }

    #marketing_menu_item_marketing:hover {
        cursor: pointer;
    }

    #marketing_menu_item_report:hover {
        cursor: pointer;
    }

    #marketing_menu_item_marketing_source:hover {
        cursor: pointer;
    }

    #marketing_menu_item_bank_account:hover {
        cursor: pointer;
    }

    #marketing_menu_item_inventory_report:hover {
        cursor: pointer;
    }
</style>
<script>
    $(document).ready(function () {
        $('#marketing_menu_item_product').click(function () {
            location.href = "/marketing/products/";
        });
        $('#marketing_menu_item_marketing').click(function () {
            location.href = "/marketing/marketing-products/";
        });
        $('#marketing_menu_item_report').click(function () {
            location.href = "/marketing/revenue-report/";
        });
        $('#marketing_menu_item_inventory_report').click(function () {
            location.href = "/marketing/inventory-report/";
        });
        @if (Auth::user()->isLeader())
        $('#marketing_menu_item_marketing_source').click(function () {
            location.href = "/marketing/marketing-sources/";
        });
        $('#marketing_menu_item_bank_account').click(function () {
            location.href = "/marketing/bank-accounts/";
        });
        @endif

    });

</script>
