jQuery(document).ready(($) => {
    $("#jc_add_wishlist").click((e) => {
        $.post(
            jccswishlist_data.admin_ajax
            , jccswishlist_data.post_data
            , (response) => {
                alert(response);
            });
    });
});