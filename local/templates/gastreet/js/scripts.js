$(function() {
    $.ajax({
        url: "/ajax/cart.php",
        data: { "type": "get_count", "site_id": BX.message("SITE_ID") },
        success: function(data) {
            if(data.success){
            	$("#sidebar_cart_link").append(`<span>${data["info"]["BASKET_COUNT"]}</span>`);
            }
        }
    });
});