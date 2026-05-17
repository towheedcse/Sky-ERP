RE_NUMBER = new RegExp(/^[0-9]+$/);

RE_EMAIL = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);

RE_NAME = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadProduct = getHTTPObject();
var httpSaveProduct = getHTTPObject();
var httpLoadPS = getHTTPObject();
var httpLoadPSE = getHTTPObject();
var isIE = document.all;

var rsFound = true;

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

function checkUnit(val) {
    document.getElementById("qty_m").innerHTML = " (" + val + ")";
    document.getElementById("amount_m").innerHTML = " (per " + val + ")";
}

function calTotalValue() {
    var qty = document.frmbuyerorder.qty.value;
    var unit_price = document.frmbuyerorder.unit_price.value;

    if (qty == '' || qty == 0) {
        qty = 0;
    }
    if (unit_price == '' || unit_price == 0) {
        unit_price = 0;
    }

    var totalvalue = qty * unit_price;
    document.frmbuyerorder.total.value = totalvalue.toFixed(2);
}

function calTotal(sl) {
    var qty = parseFloat(document.getElementById('qty' + sl).value);
    var unit_price = parseFloat(document.getElementById('unit_price' + sl).value);
    if (isNaN(qty)) {
        qty = 0;
    }
    if (isNaN(unit_price)) {
        unit_price = 0;
    }

    var totalvalue = (qty * unit_price);
    document.getElementById('total' + sl).value = totalvalue.toFixed(2);

    var ttlfields = parseInt(document.getElementById("ttlfield").value);
    var j = 1;
    var TotalAmount = 0;
    var TotalQty = 0;
    for (j; j < ttlfields; j++) {
        TotalQty += parseFloat(document.getElementById('qty' + j).value);
        TotalAmount += parseFloat(document.getElementById('total' + j).value);
    }
    document.getElementById('total_amount').value = TotalAmount.toFixed(2);
    document.getElementById("txttotalqty").innerHTML = TotalQty + " pc";
    document.getElementById("txttotalamount").innerHTML = TotalAmount.toFixed(2) + " TK";

}

function getProductStock() {
    var transfer_stock = document.getElementById('transfer_from').value;
    var product_id = document.getElementById('product').value;
    document.getElementById('qty').value = 0;
    if (product_id == "") {
        var product_id = document.getElementById('product').value;
    }
    if (product_id != "") {
        httpLoadPS.open("GET", "index.php?app=groupwise.stock.transfer&cmd=load_stock&product_id=" + product_id + "&transfer_stock=" + transfer_stock, true);
        httpLoadPS.onreadystatechange = handlePSResponse;
        httpLoadPS.send(null);
    }
}

function handlePSResponse() {
    if (httpLoadPS.readyState == 4) {
        //alert(httpLoadPS.responseText);
        var PScontent = trim(httpLoadPS.responseText);
        contentArr = PScontent.split("#####");
        document.getElementById('stock_qty').value = contentArr[0];
        document.getElementById('unit_price').value = contentArr[1];
        document.getElementById("qty_s").innerHTML = " " + contentArr[2];
        document.getElementById("qty_t").innerHTML = " " + contentArr[2];
        document.getElementById('qty').focus();
        document.getElementById('qty').select();
    }
}

/* ==== For Edit ==== */
function getProductStockQty(product_id, sl) {
    var transfer_stock = document.getElementById('transfer_from').value;
    document.getElementById('stock_qty' + sl).value = 0;
    if (product_id != "") {
        httpLoadPSE.open("GET", "index.php?app=groupwise.stock.transfer&cmd=loadstockqty&product_id=" + product_id + "&transfer_stock=" + transfer_stock + "&sl=" + sl, true);
        httpLoadPSE.onreadystatechange = handleStockQtyResponse;
        httpLoadPSE.send(null);
    }
}

function handleStockQtyResponse() {
    if (httpLoadPSE.readyState == 4) {
        //alert(httpLoadPS.responseText);
        var PScontent = trim(httpLoadPSE.responseText);
        contentArr = PScontent.split("#####");
        var sl = contentArr[3];
        document.getElementById('stock_qty' + sl).value = contentArr[0];
        document.getElementById('unit_price' + sl).value = contentArr[1];
        document.getElementById('unit-s' + sl).innerHTML = " " + contentArr[2];
        document.getElementById('unit-t' + sl).innerHTML = " " + contentArr[2];
        document.getElementById('unit-e' + sl).innerHTML = " " + contentArr[2];
        document.getElementById('qty' + sl).focus();
        document.getElementById('qty' + sl).select();
    }
}

function salesProcess() {
    if (includeGrid()) {
        prepareTblGrid(); // prepareGrid();
        return true;
    }
}

function includeGrid() {
    var frm = document.frmbuyerorder;
    formSetup(frm);
    if (validateForm(frm)) {
        alert(MISSING_REQUIRED_FIELDS);
        return false;
    } else {
        if (fieldValidation(frm)) {
            return true;
        } else {
            return false;
        }
    }
}

function formSetup(frm) {
    with (frm) {

        setRequiredField(transfer_from, 'dropdown', 'transfer_from_lbl');
        setRequiredField(delivery_point, 'dropdown', 'delivery_point_lbl');
        setRequiredField(transfer_date, 'textbox', 'transfer_date_lbl');
        setRequiredField(currency, 'textbox', 'currency_lbl');
        setRequiredField(product, 'dropdown', 'product_lbl');
        setRequiredField(unit_price, 'textbox', 'unit_price_lbl');
        setRequiredField(qty, 'textbox', 'qty_lbl');
        setRequiredField(total, 'textbox', 'amount_lbl');
    }
}

function fieldValidation(frm) {
    with (frm) {
        if (!RE_DECIMAL.exec(unit_price.value)) {
            highlightTableColumn('unit_price_lbl');
            alert(ERROR_NUMBER);
            return false;
        } else if (!RE_DECIMAL.exec(qty.value)) {
            highlightTableColumn('qty_lbl');
            alert(ERROR_NUMBER);
            return false;
        } else if (product.value == 0) {
            highlightTableColumn('product_lbl');
            return false;
        } else {
            return true;
        }
    }
    return true;
}

function prepareTblGrid() {
    var productid = document.getElementById('product').value;
    var new_product_id = "";

    if (document.getElementById("convert_product").checked) {
        new_product_id = document.getElementById('new_product').value;
        if (new_product_id == "") {
            alert("Please select New Product for conversion.");
            return false;
        }
    } else {
        new_product_id = "";
    }

    var unit_price = parseFloat(document.getElementById('unit_price').value);
    var qty = parseFloat(document.getElementById('qty').value);
    var stock_qty = parseFloat(document.getElementById('stock_qty').value);
    var total = (unit_price * qty);
    currencyIdName = document.getElementById('currency').value;
    currencyArr = currencyIdName.split("###");
    var currency = currencyArr[0];
    var currencyName = currencyArr[1];
    var transfer_from = document.getElementById('transfer_from').value;
    var delivery_point = document.getElementById('delivery_point').value;
    var transfer_date = document.getElementById('transfer_date').value;

    if ((productid != "" && qty != "") && (stock_qty >= qty)) {
        httpSaveProduct.open("GET", "index.php?app=groupwise.stock.transfer&cmd=save_tmp&transfer_from=" + transfer_from + "&delivery_point=" + delivery_point + "&transfer_date=" + transfer_date + "&currency=" + currency + "&currencyName=" + currencyName + "&productid=" + productid + "&new_product_id=" + new_product_id + "&qty=" + qty + "&unit_price=" + unit_price + "&total=" + total, true);
        httpSaveProduct.onreadystatechange = handleSaveResponse;
        httpSaveProduct.send(null);
    } else {
        chkTransferQty(qty);
    }

} // End of function prepareTblGrid()
function handleSaveResponse() {
    if (httpSaveProduct.readyState == 4) {
        //alert(httpSaveProduct.responseText);
        var salesValue = trim(httpSaveProduct.responseText);
        var arrSaveOrder = salesValue.split("####-@@@@");
        var tbl = arrSaveOrder[0];
        var total_amount = parseFloat(arrSaveOrder[1]);
        if (total_amount > 0) {
            needSave = true;
        }
        document.getElementById('total_amount').value = total_amount;
        document.getElementById('tbs').innerHTML = tbl;
        document.getElementById('product').focus();
        document.getElementById('product').select();

	var productConvert = arrSaveOrder[2];
	if(productConvert !== "" && productConvert !== "0") {
    		document.getElementById("convert_product").checked = true;
		document.getElementById("convert_product_section_label").style.display = "";
		document.getElementById("convert_product_section").style.display = "";
		document.getElementById("emptyTd").style.display = "";
	}
    }
}

function chkTransferQty(transfer_qty) {
    var stock_qty = parseFloat(document.getElementById('stock_qty').value);
    var transfer_qty = parseFloat(transfer_qty);
    if ((transfer_qty > stock_qty) || (stock_qty == 0)) {
        document.getElementById('qty').value = "";
    }
}

function clearStockQty() {
    document.getElementById('qty').value = "";
    document.getElementById('stock_qty').value = "";
}

//********* End get Product List ************

function wantToSave(e) {
    e.preventDefault()

	var job_required = $("#job_required").val();

	if(job_required != "" && job_required == "true"){
	   var job_name = $("#job_name").val();
	    if(job_name == ""){
	        alert("Please enter Job ID/Name");
	        return false;
	   }

	   var finish_item = $("#finish_item").val();
	    if(finish_item == ""){
	        alert("Please select Finis Item");
	        return false;
	   }
	}

    var needSave = parseFloat(document.getElementById('total_amount').value);
    if (needSave > 0) {
	var convert_product = document.getElementById("convert_product").checked;
        if (convert_product) {
            document.getElementById("cmd").value = "convert_transfer";
        } else {
            document.getElementById("cmd").value = "save_transfer";
        }

        if (confirm("Sure want ot submit???") == true) {
            // $('#submit_hidden').val('Save');
            const form = document.getElementById('frmbuyerorder');
            HTMLFormElement.prototype.submit.call(form);
        } else {
            return false;
        }

    } else {
        alert("Empty data!!! Please enter data first...");
    }
}

//********** End ***********

//=============== Start validation ============


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
