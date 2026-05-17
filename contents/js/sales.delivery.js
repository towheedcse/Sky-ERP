RE_NUMBER = new RegExp(/^[0-9]+$/);
RE_EMAIL = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);
//onClick="if(addInquiry()){saveInquiry();return true;}else{ return false;}
var httpLoadSizeQty = getHTTPObject();
var httpLoadColorQty = getHTTPObject();
var httpSaveProduct = getHTTPObject();
var httpSaveOrder = getHTTPObject();

function getHTTPObject() {
    var xmlhttp;

    if (!xmlhttp) {
        if (window.XMLHttpRequest) {
            try {
                xmlhttp = new XMLHttpRequest();
            } catch (e) {
                xmlhttp = false;
            }

        } else if (window.ActiveXObject) {
            try {
                xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (E) {
                try {
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {
                    xmlhttp = false;
                }
            }

        }
    }
    return xmlhttp;
}

function ShowDeliveryBtn() {
    calDeliveryAmount();
    document.getElementById("btnNoNeed").style.display = "none";
    document.getElementById("btnPrint").style.display = "block";
}

function calDeliveryAmount() {
    var totalFields = document.getElementById('ttlfields').value;
    var curr_symble = document.getElementById('curr_symble').value;
    var general_discount_percent = parseFloat(document.getElementById('general_discount_percent').value);
    var general_discount_amount = parseFloat(document.getElementById('general_discount_amount').value);

    var exclusive_discount_percent = parseFloat(document.getElementById('exclusive_discount_percent').value);
    var exclusive_discount_amount = parseFloat(document.getElementById('exclusive_discount_amount').value);
    if (isNaN(exclusive_discount_percent)) {
        exclusive_discount_percent = 0;
    }
    if (isNaN(exclusive_discount_amount)) {
        exclusive_discount_amount = 0;
    }
    var additional_discount_percent = parseFloat(document.getElementById('additional_discount_percent').value);
    var additional_discount = parseFloat(document.getElementById('additional_discount').value);
    if (isNaN(additional_discount_percent)) {
        additional_discount_percent = 0;
    }
    if (isNaN(additional_discount)) {
        additional_discount = 0;
    }
    var discount = parseFloat(document.getElementById('discount').value);
    var total_sales_price = parseFloat(document.getElementById('total_sales_price').value);

    var j = 1;
    var TotalDeliveryAmount = 0;
    var TotalOrderAmount = 0;
    var TotalProductDiscount = 0;
    for (j; j <= totalFields; j++) {
        var details_id = document.getElementById('details_id' + j).value;
        var unit_price = parseFloat(document.getElementById('unit_price' + j).value);
        var discount_per_qty = parseFloat(document.getElementById('discount_per_qty' + j).value);
        var vat_per_qty = parseFloat(document.getElementById('vat_per_qty' + j).value);
        var stock_qty = parseFloat(document.getElementById('stock_qty' + j).value);
        var pending_qty = parseFloat(document.getElementById('pending_qty' + j).value);
        var discount_amount = parseFloat(document.getElementById('discount_amount' + j).value);
        var order_qty = parseFloat(document.getElementById('order_qty' + j).value);
        var delivery_qty = parseFloat(document.getElementById('delivery_qty' + j).value);
        var prv_undelivery = parseFloat(document.getElementById('prv_undelivery_qty' + j).value);
        //alert(prv_undelivery);
        if (isNaN(unit_price)) {
            unit_price = 0;
        }
        if (isNaN(discount_per_qty)) {
            discount_per_qty = 0;
        }
        if (isNaN(stock_qty)) {
            stock_qty = 0;
        }
        if (isNaN(order_qty)) {
            order_qty = 0;
        }
        if (isNaN(delivery_qty)) {
            delivery_qty = 0;
        }
        if (isNaN(prv_undelivery)) {
            prv_undelivery = 0;
        }

        if (isNaN(vat_per_qty)) {
            vat_per_qty = 0;
        }

	const totalAvailableQty = prv_undelivery + order_qty;

        if ((delivery_qty <= pending_qty) && (delivery_qty <= stock_qty)) {
            var undelivery_qty = (totalAvailableQty - delivery_qty);
            var final_order_qty = delivery_qty;

            var product_price = (final_order_qty * unit_price);

            var product_discount = ((product_price / 100) * discount_per_qty);

            var vat_amount = ((product_price / 100) * vat_per_qty);
            document.getElementById('vat_amount' + j).innerHTML = vat_amount.toFixed(2);

            var net_total = ((product_price + vat_amount) - product_discount);

            TotalOrderAmount += net_total;
            TotalDeliveryAmount += net_total;
            TotalProductDiscount += product_discount;
            document.getElementById('undelivery_qty' + j).value = undelivery_qty;
            document.getElementById('product_price' + j).innerHTML = net_total.toFixed(2) + " " + curr_symble;
        } else {
            var undelivery_qty = (totalAvailableQty - delivery_qty);
            var final_order_qty = delivery_qty;

            if (delivery_qty > stock_qty) {
                final_order_qty = stock_qty;
		document.getElementById('delivery_qty' + j).value = final_order_qty;
		alert("Available stock qty for order is "+stock_qty);
            }

            if (delivery_qty > totalAvailableQty) {
                final_order_qty = totalAvailableQty;
		document.getElementById('delivery_qty' + j).value = final_order_qty;
		alert("Available qty for order is "+totalAvailableQty);
            }
                
	    document.getElementById('undelivery_qty' + j).value = undelivery_qty;
            
            var product_price = (final_order_qty * unit_price);
            var product_discount = ((product_price / 100) * discount_per_qty);

            var vat_amount = ((product_price / 100) * vat_per_qty);
            document.getElementById('vat_amount' + j).innerHTML = vat_amount.toFixed(2);

            var net_total = ((product_price + vat_amount) - product_discount);
            document.getElementById('product_price' + j).innerHTML = net_total.toFixed(2) + " " + curr_symble;

	    TotalOrderAmount += net_total;
            TotalDeliveryAmount += net_total;
        }
    }
    var total_discount = 0;
    if (TotalOrderAmount >= 0) {
        total_discount += TotalProductDiscount;
        document.getElementById("product_discount").value = TotalProductDiscount;

        document.getElementById("order_value").innerHTML = TotalOrderAmount.toFixed(2) + " " + curr_symble;
        document.getElementById("total_amount").value = TotalOrderAmount.toFixed(2);
        if (general_discount_percent > 0) {
            var general_discount = ((TotalOrderAmount / 100) * general_discount_percent);
            document.getElementById("general_discount").innerHTML = general_discount.toFixed(2) + " " + curr_symble;
            var TotalOrderAmountAFGD = (TotalOrderAmount - general_discount);
            total_discount += general_discount;
        } else {
            var TotalOrderAmountAFGD = TotalOrderAmount;
        }

        if (exclusive_discount_percent > 0) {
            var exclusive_discount = ((TotalOrderAmountAFGD / 100) * exclusive_discount_percent);
            document.getElementById("exclusive_discount").innerHTML = exclusive_discount.toFixed(2) + " " + curr_symble;
            var TotalOrderAmountAFED = (TotalOrderAmountAFGD - exclusive_discount);
            total_discount += exclusive_discount;
        } else {
            var TotalOrderAmountAFED = (TotalOrderAmountAFGD);
        }

        if (additional_discount_percent > 0) {
            additional_discount = ((TotalOrderAmountAFED / 100) * additional_discount_percent);
            document.getElementById("additional_discount_amount").innerHTML = additional_discount.toFixed(2) + " " + curr_symble;
        } else {
            if (additional_discount > 0) {
                additional_discount = (TotalOrderAmountAFED - additional_discount);
            }
            document.getElementById("additional_discount_amount").innerHTML = additional_discount.toFixed(2) + " " + curr_symble;
        }

        var total_amount = TotalOrderAmount || 0;
        var vat_type = document.getElementById('vat_type').value; // percent | fixed
        var vat_input = parseFloat(document.getElementById('vat_percent').value) || 0;

        if (vat_input < 0) vat_input = 0;
        var total_vat_amount = 0;

        if (vat_type === 'percent') {
            // limit percentage
            if (vat_input > 100) vat_input = 100;
            document.getElementById('vat_percent').value = vat_input;
            // calculate % VAT
            total_vat_amount = (vat_input / 100) * total_amount;
        } else {
            // fixed VAT amount
            total_vat_amount = vat_input;
        }

        document.getElementById("vat_amount").innerHTML = total_vat_amount.toFixed(2);
    	document.getElementById("vatAmount").value = total_vat_amount.toFixed(2);

        var additional_cost = parseFloat(document.getElementById('additional_cost').value) || 0;
        if (isNaN(additional_cost)) {
            additional_cost = 0;
        }
        TotalOrderAmountAFED += additional_cost;

        TotalOrderAmountAFED += total_vat_amount;

	var netOrderValue = TotalOrderAmountAFED; 
        if (additional_discount > 0) {
            netOrderValue = (TotalOrderAmountAFED - additional_discount);
            total_discount += additional_discount;
        }

        document.getElementById("netOrderValue").innerHTML = netOrderValue.toFixed(2) + " " + curr_symble;
        document.getElementById("netOrderAmount").value = netOrderValue.toFixed(2);
        document.getElementById("total_sales_price").value = (netOrderValue + total_discount).toFixed(4);
        document.getElementById("discount").value = total_discount.toFixed(2);
    }

    if (TotalDeliveryAmount > 0) {
        document.getElementById("TotalDeliveryAmount").innerHTML = netOrderValue.toFixed(2) + " " + curr_symble;
        document.getElementById("total_delivery_amount").value = netOrderValue.toFixed(4);
    } else {
        document.getElementById("TotalDeliveryAmount").innerHTML = "";
        document.getElementById("total_delivery_amount").value = "";
    }
}

function UpdateDeliveryOrder() {
    var totalFields = document.getElementById('ttlfields').value;
    var curr_symble = document.getElementById('curr_symble').value;
    var voucher_no = document.getElementById('voucher_no').value;
    var general_discount_percent = parseFloat(document.getElementById('general_discount_percent').value);
    var general_discount_amount = parseFloat(document.getElementById('general_discount_amount').value);

   var vat_no = document.getElementById('vat_no').value;

   if(vat_no == ""){
	alert("Enter your VAT number, or type ‘N/A’ if not applicable.");
	return false;
   }

    var exclusive_discount_percent = parseFloat(document.getElementById('exclusive_discount_percent').value);
    var exclusive_discount_amount = parseFloat(document.getElementById('exclusive_discount_amount').value);
    if (isNaN(exclusive_discount_percent)) {
        exclusive_discount_percent = 0;
    }
    if (isNaN(exclusive_discount_amount)) {
        exclusive_discount_amount = 0;
    }
    var additional_discount_percent = parseFloat(document.getElementById('additional_discount_percent').value);
    var additional_discount = parseFloat(document.getElementById('additional_discount').value);
    if (isNaN(additional_discount_percent)) {
        additional_discount_percent = 0;
    }
    if (isNaN(additional_discount)) {
        additional_discount = 0;
    }
    var discount = parseFloat(document.getElementById('discount').value);
    var total_sales_price = parseFloat(document.getElementById('total_sales_price').value);

    var j = 1;
    var TotalDeliveryAmount = 0;
    var TotalOrderAmount = 0;
    var TotalProductDiscount = 0;
    for (j; j <= totalFields; j++) {
        var details_id = document.getElementById('details_id' + j).value;
        var unit_price = parseFloat(document.getElementById('unit_price' + j).value);
        var discount_per_qty = parseFloat(document.getElementById('discount_per_qty' + j).value);
        var vat_per_qty = parseFloat(document.getElementById('vat_per_qty' + j).value);
        var stock_qty = parseFloat(document.getElementById('stock_qty' + j).value);
        var pending_qty = parseFloat(document.getElementById('pending_qty' + j).value);
        var discount_amount = parseFloat(document.getElementById('discount_amount' + j).value);
        var order_qty = parseFloat(document.getElementById('order_qty' + j).value);
        var main_order_qty = parseFloat(document.getElementById('main_order_qty' + j).value);
        var delivery_qty = parseFloat(document.getElementById('delivery_qty' + j).value);
        var prv_undelivery = parseFloat(document.getElementById('prv_undelivery_qty' + j).value);
        var details_text = document.getElementById('details_text' + j).value;
        var gross_weight = parseFloat(document.getElementById('gross_weight' + j).value);
        var net_weight = parseFloat(document.getElementById('net_weight' + j).value);
        //alert(prv_undelivery);
        if (isNaN(unit_price)) {
            unit_price = 0;
        }
        if (isNaN(discount_per_qty)) {
            discount_per_qty = 0;
        }
        if (isNaN(stock_qty)) {
            stock_qty = 0;
        }
        if (isNaN(order_qty)) {
            order_qty = 0;
        }
        if (isNaN(delivery_qty)) {
            delivery_qty = 0;
        }
        if (isNaN(prv_undelivery)) {
            prv_undelivery = 0;
        }
        if (isNaN(gross_weight)) {
            gross_weight = 0;
        }
        if (isNaN(net_weight)) {
            net_weight = 0;
        }
        var total_undelivery_qty = ((order_qty - delivery_qty) + prv_undelivery);
	const totalAvailableQty = prv_undelivery + order_qty;

        if ((delivery_qty <= totalAvailableQty) && (delivery_qty <= stock_qty)) {
            var undelivery_qty = (totalAvailableQty - delivery_qty);
            var final_order_qty = delivery_qty;
           
            var product_price = (final_order_qty * unit_price);
            var product_discount = ((product_price / 100) * discount_per_qty);

            var vat_amount = ((product_price / 100) * vat_per_qty);

            var net_total = ((product_price+vat_amount) - product_discount);

            TotalOrderAmount += net_total;
            TotalDeliveryAmount += net_total;
            TotalProductDiscount += product_discount;
            document.getElementById('undelivery_qty' + j).value = undelivery_qty;
            document.getElementById('product_price' + j).innerHTML = net_total.toFixed(2) + " " + curr_symble;

            if (details_id > 0 && net_total >= 0 && final_order_qty >= 0) {
                UpdateUndeliveryQty(details_id, j, final_order_qty, undelivery_qty, net_total,details_text,gross_weight,net_weight,vat_amount,product_discount);
            }
        }else{
	    if(stock_qty < delivery_qty){
		delivery_qty = stock_qty;
	    }
	    if(totalAvailableQty < delivery_qty){
		delivery_qty = totalAvailableQty;
	    }
            var undelivery_qty = (totalAvailableQty - delivery_qty);
            var final_order_qty = delivery_qty;

            var product_price = (final_order_qty * unit_price);

            var product_discount = ((product_price / 100) * discount_per_qty);

	    var vat_amount = ((product_price / 100) * vat_per_qty);

            var net_total = ((product_price+vat_amount) - product_discount);

            TotalOrderAmount += net_total;
            TotalDeliveryAmount += net_total;
            TotalProductDiscount += product_discount;
            document.getElementById('undelivery_qty' + j).value = undelivery_qty;
            document.getElementById('product_price' + j).innerHTML = net_total.toFixed(2) + " " + curr_symble;

            if (details_id > 0 && net_total >= 0 && final_order_qty >= 0) {
                UpdateUndeliveryQty(details_id, j, final_order_qty, undelivery_qty, net_total,details_text,gross_weight,net_weight,vat_amount,product_discount);
            }
        }
    }

    var total_discount = 0;
    if (TotalOrderAmount > 0) {
        total_discount += TotalProductDiscount;
        document.getElementById("product_discount").value = TotalProductDiscount;

        document.getElementById("order_value").innerHTML = TotalOrderAmount.toFixed(2) + " " + curr_symble;
        document.getElementById("total_amount").value = TotalOrderAmount.toFixed(2);
        if (general_discount_percent > 0) {
            var general_discount = ((TotalOrderAmount / 100) * general_discount_percent);
            document.getElementById("general_discount").innerHTML = general_discount.toFixed(2) + " " + curr_symble;
            var TotalOrderAmountAFGD = (TotalOrderAmount - general_discount);
            total_discount += general_discount;
        } else {
            var TotalOrderAmountAFGD = TotalOrderAmount;
        }

        if (exclusive_discount_percent > 0) {
            var exclusive_discount = ((TotalOrderAmountAFGD / 100) * exclusive_discount_percent);
            document.getElementById("exclusive_discount").innerHTML = exclusive_discount.toFixed(2) + " " + curr_symble;
            var TotalOrderAmountAFED = (TotalOrderAmountAFGD - exclusive_discount);
            total_discount += exclusive_discount;
        } else {
            var TotalOrderAmountAFED = (TotalOrderAmountAFGD);
        }

        if (additional_discount_percent > 0) {
            additional_discount = ((TotalOrderAmountAFED / 100) * additional_discount_percent);
            document.getElementById("additional_discount_amount").innerHTML = additional_discount.toFixed(2) + " " + curr_symble;
        } else {
            if (additional_discount > 0) {
                additional_discount = (TotalOrderAmountAFED - additional_discount);
            }
            document.getElementById("additional_discount_amount").innerHTML = additional_discount.toFixed(2) + " " + curr_symble;
        }

        if (additional_discount > 0) {
            var netOrderValue = (TotalOrderAmountAFED - additional_discount);
            total_discount += additional_discount;
        } else {
            var netOrderValue = (TotalOrderAmountAFED);
        }
        var total_order_amount = (netOrderValue + total_discount);
        document.getElementById("netOrderValue").innerHTML = netOrderValue.toFixed(2) + " " + curr_symble;
        document.getElementById("netOrderAmount").value = netOrderValue.toFixed(2);
        document.getElementById("total_sales_price").value = total_order_amount.toFixed(4);
        document.getElementById("discount").value = total_discount.toFixed(2);

        document.getElementById("TotalDeliveryAmount").innerHTML = netOrderValue.toFixed(2) + " " + curr_symble;
        document.getElementById("total_delivery_amount").value = netOrderValue.toFixed(4);
        product_discount = TotalProductDiscount;

        var additional_cost = document.getElementById("additional_cost").value;
        var vat_type = document.getElementById("vat_type").value;
        var vat_percent = document.getElementById("vat_percent").value;
        var vat_amount = document.getElementById("vatAmount").value;

        var delivery_date = document.getElementById("delivery_date").value;
        var aging_date = document.getElementById("aging_date").value;
        var terms = document.getElementById("terms").value;
        var vat_no = document.getElementById("vat_no").value;
        var sms_text = document.getElementById("sms_text").value;
       var vehicle_no = document.getElementById("vehicle_no").value;
       var driver_name = document.getElementById("driver_name").value;
       var contact_person = document.getElementById("contact_person").value;
       var delivery_address = document.getElementById("delivery_address").value;
       var ref_voucher = document.getElementById("ref_voucher").value;

        EditOrder(voucher_no, total_order_amount, netOrderValue, product_discount, general_discount, exclusive_discount, additional_discount, total_discount, additional_cost, vat_type, vat_percent, vat_amount, delivery_date,aging_date,terms,vat_no,sms_text,vehicle_no,driver_name,contact_person,delivery_address,ref_voucher);
    }

}

function UpdateUndeliveryQty(details_id, sl, qty, undelivery_qty, total,details_text,gross_weight,net_weight,vat_amount,product_discount) {
    if (details_id != "" && undelivery_qty >= 0) {
        document.getElementById("btnUpdate").style.display = "none";
        document.getElementById("btnUpdateMsg").innerHTML = "Please wait few minite..";
        httpSaveProduct = new XMLHttpRequest();
        httpSaveProduct.open("GET", "index.php?app=sales.delivery&cmd=edit_qty&sal_detail_id=" + details_id + "&sl=" + sl + "&qty=" + qty + "&undelivery_qty=" + undelivery_qty + "&total=" + total+ "&details_text=" + details_text+ "&gross_weight=" + gross_weight+ "&net_weight=" + net_weight+ "&vat_amount=" + vat_amount+ "&product_discount=" + product_discount, true);
        httpSaveProduct.onreadystatechange = handleSaveResponse;
        httpSaveProduct.send(null);
    }

} // End of function

function handleSaveResponse() {
    if (httpSaveProduct.readyState == 4) {
        document.getElementById("btnUpdate").style.display = "none";
        document.getElementById("btnUpdateMsg").innerHTML = "Please wait...";
    }
}

function EditOrder(voucher_no, total_order_amount, netOrderValue, product_discount, general_discount, exclusive_discount, additional_discount, total_discount, additional_cost, vat_type, vat_percent, vat_amount, delivery_date,aging_date,terms,vat_no,sms_text,vehicle_no,driver_name,contact_person,delivery_address,ref_voucher) {

    if (voucher_no != "" && total_order_amount > 0 && netOrderValue > 0) {
        httpSaveOrder.open("GET", "index.php?app=sales.delivery&cmd=edit_order&voucher_no=" + voucher_no + "&total_value=" + total_order_amount + "&net_payble=" + netOrderValue + "&product_discount=" + product_discount + "&general_discount=" + general_discount + "&exclusive_discount=" + exclusive_discount + "&additional_discount=" + additional_discount + "&total_discount=" + total_discount + "&additional_cost=" + additional_cost + "&vat_type=" + vat_type + "&vat_percent=" + vat_percent + "&vat_amount=" + vat_amount + "&delivery_date=" + delivery_date + "&aging_date=" + aging_date + "&terms=" + terms + "&vat_no=" + vat_no + "&sms_text=" + sms_text + "&vehicle_no=" + vehicle_no + "&driver_name=" + driver_name + "&contact_person=" + contact_person + "&delivery_address=" + delivery_address + "&ref_voucher=" + ref_voucher, true);
        httpSaveOrder.onreadystatechange = handleSaveOrder;
        httpSaveOrder.send(null);
    }

} // End of function

function handleSaveOrder() {
    if (httpSaveOrder.readyState == 4) {
        var voucher_no = trim(httpSaveOrder.responseText);
        var url_loc = "index.php?app=sales.delivery&cmd=delivery&voucher_no=" + voucher_no + "&msg=Successfully order updated. Now you can delivery";
        window.location = url_loc;

    }
}


function calVat() {
    var total_amount = parseFloat(document.getElementById('total_amount').value) || 0;
    var vat_type = document.getElementById('vat_type').value; // percent | fixed
    var vat_input = parseFloat(document.getElementById('vat_percent').value) || 0;

    if (vat_input < 0) vat_input = 0;
    var total_vat_amount = 0;

    if (vat_type === 'percent') {
        // limit percentage
        if (vat_input > 100) vat_input = 100;
        document.getElementById('vat_percent').value = vat_input;
        // calculate % VAT
        total_vat_amount = (vat_input / 100) * total_amount;
    } else {
        // fixed VAT amount
        total_vat_amount = vat_input;
    }

    document.getElementById("vat_amount").innerHTML = total_vat_amount.toFixed(2);
    document.getElementById("vatAmount").value = total_vat_amount.toFixed(2);

    calDeliveryAmount();
}

function DeliveryOrder() {
    var totalDeliveryAmounts = document.getElementById("total_delivery_amount").value;
    if (totalDeliveryAmounts > 0) {
        document.getElementById("btnPrint").style.display = "none";
        document.getElementById("btnDeliveryMsg").innerHTML = "Order delivery process is running. Please wait....";
    }
}

function Trim(TRIM_VALUE) {
    if (TRIM_VALUE.length < 1) {
        return "";
    }
    TRIM_VALUE = RTrim(TRIM_VALUE);
    TRIM_VALUE = LTrim(TRIM_VALUE);
    if (TRIM_VALUE == "") {
        return "";
    } else {
        return TRIM_VALUE;
    }

} //End Function

function RTrim(VALUE) {
    var w_space = String.fromCharCode(32);
    var v_length = VALUE.length;
    var strTemp = "";

    if (v_length < 0) {
        return "";
    }

    var iTemp = v_length - 1;
    while (iTemp > -1) {
        if (VALUE.charAt(iTemp) == w_space) {
        } else {
            strTemp = VALUE.substring(0, iTemp + 1);
            break;
        }
        iTemp = iTemp - 1;

    } //End While
    return strTemp;
} //End Function

function LTrim(VALUE) {
    var w_space = String.fromCharCode(32);
    if (v_length < 1) {
        return "";
    }
    var v_length = VALUE.length;
    var strTemp = "";
    var iTemp = 0;
    while (iTemp < v_length) {
        if (VALUE.charAt(iTemp) == w_space) {
        } else {
            strTemp = VALUE.substring(iTemp, v_length);
            break;
        }
        iTemp = iTemp + 1;
    } //End While

    return strTemp;

} //End Function
