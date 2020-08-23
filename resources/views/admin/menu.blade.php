<div class="menu_item" id="admin_menu_item_products">
    Sản Phẩm
</div>
<div class="menu_item" id="admin_menu_item_users">
    Nhân Viên
</div>
<div class="menu_item" id="admin_menu_item_discounts">
    Khuyến Mại
</div>
<div class="menu_item" id="admin_menu_item_campaigns">
    Chiến Dịch
</div>
<div class="menu_item" id="admin_menu_item_landing_page">
    Landing Page
</div>
<div class="menu_item" id="admin_menu_item_config">
    Cấu Hình
</div>
<style>
    #admin_menu_item_products:hover{
        cursor: pointer;
    }
    #admin_menu_item_discounts:hover{
        cursor: pointer;
    }
    #admin_menu_item_users:hover{
        cursor: pointer;
    }
    #admin_menu_item_campaigns:hover{
        cursor: pointer;
    }
    #admin_menu_item_config:hover{
        cursor: pointer;
    }
    #admin_menu_item_landing_page:hover{
        cursor: pointer;
    }
</style>
<script>
    $(document).ready(function () {
        $('#admin_menu_item_products').click(function(){
            location.href = "/admin/products/";
        });
        $('#admin_menu_item_discounts').click(function(){
            location.href = "/admin/discounts/";
        });
        $('#admin_menu_item_users').click(function(){
            location.href = "/admin/users/";
        });
        $('#admin_menu_item_campaigns').click(function(){
            location.href = "/admin/campaign_names/";
        });
        $('#admin_menu_item_landing_page').click(function(){
            location.href = "/admin/landing-pages/";
        });
        $('#admin_menu_item_config').click(function(){
            location.href = "/admin/config/";
        });
    });

</script>
