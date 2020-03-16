jQuery(document).ready(($) => {
    $("#jc_add_wishlist").click((e) => {
        $.post(
            jccswishlist_data.admin_ajax
            , {
                postId: jccswishlist_data.postId,
                action: jccswishlist_data.action
            }
            , (response) => {
                if (response)
                {
                    alert(response);
                }
                $("#jc_add_wishlist_div").html("Guardado en la lista de deseos");
            });
    });
});