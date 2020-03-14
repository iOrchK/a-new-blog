$(document).ready(() => {
    $('.jc-user-item').click((event) => {
        let id = event.target.id;
        id = id.replace("jc-user-", "");
        
        $.ajax({
            beforeSend: function (qXHR, settings) {
                UIkit.modal.dialog('<div class="uk-width-1-1 uk-text-center" uk-spinner></div>');
            },
            type: 'GET',
            url: 'https://jsonplaceholder.typicode.com/users/' + id,
            success: (response) => {
                const body = `
                    <div class="uk-margin">
                        <div class="uk-width-1-1 uk-text-small">
                            <h3>User Detail</h3>
                            <p>
                                <b>Id:</b> ${response.id} <br>
                                <b>Name:</b> ${response.name} <br>
                                <b>User Name:</b> ${response.username} <br>
                                <b>E-mail:</b> ${response.email} <br>
                                <b>Address:</b> ${response.address.street}, ${response.address.suite}, ${response.address.city} <br>
                                <b>Phone:</b> ${response.phone} <br>
                                <b>Website:</b> ${response.website} <br>
                                <b>Company:</b> ${response.company.name} <br>
                            </p>
                        </div>
                    </div>
                `;

                UIkit.modal.dialog(body);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
                UIkit.modal.dialog("Error: " + textStatus + " - " + errorThrown); 
            }
        });
    });

});