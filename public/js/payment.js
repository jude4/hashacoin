function buildModalHeader(title) {
    let $modalHeader = $('<div class="modal-header"></div>')
    let $closeBtn = $('<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><button')
    let $modalTitleContainer = $('<h5 class="modal-title"></h5>')
    if (title !== 'undefined') {
        $modalTitleContainer.prepend(title)
    }
    $modalHeader.append($modalTitleContainer, $closeBtn)
    return $modalHeader;
}

function buildModalFooter() {
    return $('<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></div>');
}

function buildModalBody(data) {
    let $modalBodyContainer = $('<div class="modal-body"></div>')
    if (data !== 'undefined') {
        $modalBodyContainer.html(data)
    }
    return $modalBodyContainer
}

function buildDataModal(data, type) {
    let $wrapper = $('<div id="myModal" class="fmodal"></div>')
    let $dialogDiv = $('<div class="fmodal-content"></div>')
    if (type == "message") {
        var message = data.message;
    } else {
        let ss = [];
        validate = data.validate;
        for (const key in validate) {
            ss = validate[key];
        }
        var message = ss;
    }
    $dialogDiv.append('<span class="close">&times;</span><p>' + message + '</p>')
    $wrapper.append($dialogDiv)
    $('body').append($wrapper)
    document.getElementById("myModal").style.display = "block";
    document.getElementsByClassName("close")[0].onclick = function () {
        document.getElementById("myModal").style.display = "none";
    }
}
window.onclick = function (event) {
    if (event.target == document.getElementById("myModal")) {
        document.getElementById("myModal").style.display = "none";
    }
}

function HabeshacoinCheckout(payment) {
    var url = window.location.href+"api/js_transfer";
    const data = {
        "tx_ref": payment.tx_ref,
        "amount": payment.amount,
        "currency": payment.currency,
        "callback_url": payment.callback_url,
        "return_url": payment.return_url,
        "customer": {
            "email": payment.customer.email,
            
            "first_name": payment.customer.first_name,
            "last_name": payment.customer.last_name,
        },
        "customization": {
            "title": payment.customization.title,
            "description": payment.customization.description,
            "logo": payment.customization.logo
        },
        "meta": payment.meta
    };
    fetch(url, {
        method: "POST",
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' , 'Authorization': ' Bearer '+payment.public_key },
        body: JSON.stringify(data)
    }).then(response => response.json())
        .then(data => {
            if(data.data!=null){
                window.location.href=data.data.checkout_url;
            }else{
                document.getElementById("wrapper").innerHTML = data.message;
            }
            console.log('Success:', data);
        })
        .catch((error) => {
            console.error('Error:', error);
        });

}