

document.addEventListener("DOMContentLoaded", function() {
    var field_qty = document.getElementById('inventory_qty');
    var field_mqty = document.getElementById('inventory_min_qty');
    var field_ucmqty = document.getElementById('inventory_use_config_min_qty');

    if (field_qty) {
        field_qty.disabled = true;
        field_qty.classList.add("disabled");
    }

    if (field_mqty) {
        field_mqty.disabled = true;
        field_mqty.classList.add("disabled");
    }

    if (field_ucmqty) {
        field_ucmqty.disabled = true;
    }
});


function caja_setAvailableLocation(url) {

    var pLocation = document.getElementById('p_alocation');
    var pQty = document.getElementById('p_qty');
    var pSku = document.getElementById('p_sku');
    var pBinType = document.getElementById('p_bin_type');
    var message = document.getElementById('l-messages');

    message.innerHTML = '';
    message.hide(500);
    url = url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true');

    new Ajax.Request(url, {
        parameters: {
            method: 'POST',
            product_location: pLocation.value,
            product_qty: pQty.value,
            product_sku: pSku.value,
            product_bin_type: pBinType.value
        },
        onSuccess: function(transport) {
            try {
                if (transport.responseText.isJSON()) {
                    var response = transport.responseText.evalJSON();
                    if (response.error) {
                        message.innerHTML = transport.responseText;
                        message.show(500);
                    }
                } else {
                    message.innerHTML = transport.responseText;
                    message.show(500);
                }
            }
            catch (e) {
                console.log('Error');
            }
        },
        onComplete: function() {}
    });
}


function caja_getZoneStatus(url) {

    var cells = document.getElementsByClassName('inner-block');
    var zLoader = document.getElementById('cboard-loading-mask');
    var message = document.getElementById('zones-message');
    url = url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true');

    new Ajax.Request(url, {
        parameters: {
            method: 'GET'
        },
        loaderArea: false,
        onCreate: function() {
            zLoader.show();
        },
        onSuccess: function(transport) {
            try {
                if (transport.responseText.isJSON()) {
                    var response = transport.responseText.evalJSON();
                    if (response.error) {
                        message.innerHTML = transport.responseText;
                        message.show(500);
                    } else {
                        for (var i = 0; i < cells.length; i++) {
                            cells[i].innerHTML = '';
                            cells[i].style.backgroundColor = 'transparent';
                        }
                        response.map(function(value, index){
                            $(value.cellId).style.backgroundColor = value.statusColor;
                            $(value.cellId).innerHTML = value.body;
                        });
                    }
                }
            }
            catch (e) {
                console.log('Error');
            }
        },
        onComplete: function() {
            zLoader.hide();
        }
    });
}


function caja_completeOrderLine(url) {

    var oQty = document.getElementById('caja_piked_qty');
    var oLine = document.getElementById('caja_orderline');
    var pButton = document.getElementById('popup-submit');
    var pLoader = document.getElementById('popup-loader');
    var message = document.getElementById('popup-message');
    var zone = document.getElementById(document.getElementById('caja_cell_id').value);
    var actionButton = zone.getElementsByClassName('zone-action-button');
    url = url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true');

    pButton.disabled = true;
    pButton.classList.add('disabled');
    message.innerHTML = '';
    message.hide(500);

    new Ajax.Request(url, {
        parameters: {
            method: 'POST',
            orderline_qty: oQty.value,
            orderline_id: oLine.value
        },
        loaderArea: false,
        onCreate: function() {
            pLoader.show();
        },
        onSuccess: function(transport) {
            try {
                if (transport.responseText.isJSON()) {
                    var response = transport.responseText.evalJSON();
                    if (response.error) {
                        message.innerHTML = '<h3 style="color: #ff0000">'+ response.error.externalErrorMessage +'</h3>';
                        message.show(500);
                    }
                } else {
                    actionButton[0].parentElement.removeChild(actionButton[0]);
                    message.innerHTML = '<h3 style="color: #006400">'+ transport.responseText +'</h3>';
                    message.show(500);
                    setTimeout(caja_popupHide, 700);
                }
            }
            catch (e) {
                console.log('Error');
            }
        },
        onComplete: function() {
            setTimeout(function(){
                pButton.disabled = false;
                pButton.classList.remove('disabled');
            }, 700);
            pLoader.hide();
        }
    });
}


function caja_popupShow(orderLine, qty, zone) {
    caja_popupClear();
    document.getElementById('caja_orderline').value = orderLine;
    document.getElementById('caja_piked_qty').value = qty;
    document.getElementById('caja_cell_id').value = zone;
    document.getElementById('popup-qty').style.display = 'block';
}


function caja_popupHide() {
    caja_popupClear();
    document.getElementById('popup-qty').style.display = 'none';
}


function caja_popupClear() {
    var message = document.getElementById('popup-message');
    document.getElementById('caja_piked_qty').value = '';
    document.getElementById('caja_orderline').value = '';
    document.getElementById('caja_cell_id').value = '';

    message.innerHTML = '';
    message.hide();
}