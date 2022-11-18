@extends('user.merchant.plugin.menu')

@section('content')

@endsection
@section('scripts')
<script src="{{ asset('js/partials.js') }}"></script>
<script>
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
        let $wrapper = $('<div class="modal fade" id="first-modal"' + ' data-backdrop="static" data-keyboard="false" tabindex="-1"></div>')
        let $dialogDiv = $('<div class="modal-dialog"></div>')
        let $contentDiv = $('<div class="modal-content"></div>')
        if (type == "message") {
            $contentDiv.append(buildModalHeader('Error occured'), buildModalBody('<p>' + data.message + '</p>'), buildModalFooter())
        } else {
            let ss = [];
            validate = data.validate;
            for (const key in validate) {
                ss = validate[key];
            }
            $contentDiv.append(buildModalHeader('Error occured'), buildModalBody('<p>' + ss + '</p>'), buildModalFooter())
        }
        $dialogDiv.append($contentDiv)
        $('body').append($wrapper.append($dialogDiv))
        $('#first-modal').modal('show')
    }

    function SparkleCheckout(payment) {
        var url = "http://localhost:8888/sparkle/stripe/api/js_transfer";
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
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': ' Bearer ' + payment.public_key
                },
                body: JSON.stringify(data)
            }).then(response => response.json())
            .then(data => {
                if (data.data != null) {
                    window.location.href = data.data.checkout_url;
                } else {
                    document.getElementById("wrapper").innerHTML = data.message;
                }
                console.log('Success:', data);
            })
            .catch((error) => {
                console.error('Error:', error);
            });

    }
</script>
@endsection