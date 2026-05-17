RE_NUMBER = new RegExp(/^[0-9]+$/);
RE_EMAIL = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadProduct = getHTTPObject();
var httpSaveProduct = getHTTPObject();
var httpLoadUP = getHTTPObject();

var isIE = document.all;

var rsFound = true;
var needSave = false;

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
    document.getElementById("uprice_m").innerHTML = " (per " + val + ")";
}

function checkTotalUnit(val) {
    document.getElementById("totalqty_m").innerHTML = " (" + val + ")";
    document.getElementById("totalqty_m1").innerHTML = " (" + val + ")";
}

function getProductList(brandArr) {
    var catagoryArr = document.getElementById('catagory').value;
    var catagoryStr = catagoryArr.split("###");
    var catagory_id = catagoryStr[0];
    var brandStr = brandArr.split("###");
    var brand_id = brandStr[0];
    if (brand_id != "") {
        httpLoadProduct.open("GET", "index.php?app=purchase.item&cmd=loadProduct&brand_id=" + brand_id + "&catagory_id=" + catagory_id, true);
        httpLoadProduct.onreadystatechange = handleLoadResponse;
        httpLoadProduct.send(null);
    }
}

function handleLoadResponse() {
    if (httpLoadProduct.readyState == 4) {
        //alert(httpLoadProduct.responseText);
        processData(httpLoadProduct.responseText);
        //alert(httpLoadProduct.responseText);
    }

}

function processData(ResponseStr) {
    //alert(ResponseStr);
    productOption = document.getElementById('product');
    while (productOption.length > 0) {
        productOption.remove(0);
    }

    var arrProduct = Array();

    arrProduct = ResponseStr.split("@@@");
    productOption.options[0] = new Option("Select One", "0");
    for (i = 0; i < arrProduct.length - 1; i++) {
        var arrProductIdName = Array();
        arrProductIdName = arrProduct[i].split("#####");
        var details = arrProductIdName[2];
        productOption.options[i + 1] = new Option(arrProductIdName[1] + '-' + details, arrProductIdName[0] + '###' + arrProductIdName[1]);
    }

}

//********* End get Product List ************
function getProductDtl(product_id) {
    var store_id = document.getElementById('store_id').value;
    if (product_id != "" && store_id == "") {
        alert("Select Store First!!");
        return false;
    }
    if (product_id != "") {
        httpLoadUP.open("GET", "index.php?app=purchase.item&cmd=get_dtl&product_id=" + product_id + "&store_id=" + store_id, true);
        httpLoadUP.onreadystatechange = handleUPResponse;
        httpLoadUP.send(null);
    }
}

function handleUPResponse() {
    if (httpLoadUP.readyState == 4) {
        //alert(httpLoadUP.responseText);
        var UPcontent = trim(httpLoadUP.responseText);
        contentArr = UPcontent.split("#####");
        document.getElementById('m_unit').value = contentArr[0];
        document.getElementById('unit_price').value = contentArr[2];
        document.getElementById('present_stock').value = contentArr[5];

	var catagoryEl = document.getElementById('catagory');
	if (contentArr[3] && catagoryEl) {
	    var category = contentArr[3].split("###");
		
	    catagoryEl.value = category[0] || '';
	}

	var brandEl = document.getElementById('brand_id');
	if (contentArr[4] && brandEl) {
	    var brand = contentArr[4].split("###");
	    brandEl.value = brand[0] || '';
	}

	var last_purchase_date = document.getElementById('last_purchase_date');
 	if(contentArr[6] && last_purchase_date){
	    last_purchase_date.value=contentArr[6];
	}

	var last_purchase_amount = document.getElementById('last_purchase_amount');
 	if(contentArr[7] && last_purchase_amount){
	    last_purchase_amount.value=contentArr[7];
	}

    }

}

//********* End getProductDtl ************

function addCol(html) {
    var td = document.createElement('td');
    td.setAttribute('bgColor', "#D9D6BF")
    td.innerHTML = html;
    //alert("addCol"+html);
    return td;
}

function addColMSG(html) {
    var td = document.createElement('td');
    td.setAttribute('bgColor', "#FFCC66")
    td.innerHTML = html;
    //alert("addCol"+html);
    return td;
}

function addColHead(html) {
    td = document.createElement('td');
    fnt = document.createElement('font');
    fnt.setAttribute('color', "#FFFFFF");
    td.setAttribute('bgColor', "#6699FF")
    td.appendChild(fnt);
    td.innerHTML = html;
    //alert(td);
    return td;
}

function remRows(elem) {
    obj = document.getElementById(elem);
    //alert(obj);
    obj.innerHTML = "";
}

function addItem() {
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

        //setRequiredField(supplier, 'dropdown', 'supplier_lbl');
        setRequiredField(product, 'dropdown', 'product_lbl');
        setRequiredField(unit_price, 'textbox', 'unit_price_lbl');
        setRequiredField(qty, 'textbox', 'qty_lbl');
        setRequiredField(purchase_date, 'textbox', 'purchase_date_lbl');
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

    var unit_price = parseFloat(document.getElementById('unit_price').value);
    var qty = parseFloat(document.getElementById('qty').value);
    var present_stock = parseFloat(document.getElementById('present_stock').value);

    if (unit_price == "") {
        unit_price = 0;
    }
    if (qty == "") {
        qty = 0;
    }
    if (present_stock == "") {
        present_stock = 0;
    }

    var supplier = "";
    var warranty_note = document.getElementById('warranty_note').value;
    var store_id = document.getElementById('store_id').value;
    var purchase_date = document.getElementById('purchase_date').value;
    var unit = document.getElementById('unit').value;
    var department = document.getElementById('department').value;
    var tmp_id = document.getElementById('tmp_id').value;
    var last_purchase_amount = document.getElementById('last_purchase_amount').value;
    var last_purchase_date = document.getElementById('last_purchase_date').value;
    var custom_brand = document.getElementById('custom_brand').value;


    if (productid != "") {
        httpSaveProduct.open("GET", "index.php?app=purchase.item&cmd=save_sprtmp&supplier=" + supplier + "&store_id=" + store_id + "&purchase_date=" + purchase_date + "&productid=" + productid + "&qty=" + qty + "&unit_price=" + unit_price + "&warranty_note=" + warranty_note + "&unit=" + unit + "&department=" + department + "&tmp_id=" + tmp_id + "&present_stock=" + present_stock+ "&last_purchase_amount=" + last_purchase_amount+ "&last_purchase_date=" + last_purchase_date+ "&custom_brand=" + custom_brand, true);
        httpSaveProduct.onreadystatechange = handleSaveResponse;
        httpSaveProduct.send(null);
    }

} // End of function prepareTblGrid()
function handleSaveResponse() {
    if (httpSaveProduct.readyState == 4) {
        //alert(httpSaveProduct.responseText); 
        var salesValue = trim(httpSaveProduct.responseText);
        var arrSaveOrder = salesValue.split("####-@@@@");
        var tbl = arrSaveOrder[0];

        document.getElementById('tbs').innerHTML = tbl;
        document.getElementById('product').focus();
        document.getElementById('product').value = "";
        document.getElementById('custom_brand').value = "";
	resetValue();
        document.getElementById('qty').value = "";
        document.getElementById('unit_price').value = "";
        document.getElementById('total').value = "";
        document.getElementById('present_stock').value = "";
	document.getElementById('tmp_id').value = "";
	document.getElementById('last_purchase_amount').value = "";
	document.getElementById('last_purchase_date').value = "";

	window.history.pushState({}, "", "index.php?app=purchase.item&cmd=add_spr");

        //document.getElementById('product').select();
    }
}


var rsArr = Array();

function rowFound(idx) {
    var psh = false;
    var arrlen = rsArr.length;
    if (rsArr.length == 0) {
        rsArr.push(document.getElementById('catagory_product_id[]').value);
    } else {
        for (var i = 0; i <= arrlen - 1; i++) {
            if (rsArr[i] == idx) {
                alert("You have already chosen!!");
                psh = false;
                break;
            } else {
                rsArr.push(idx);
                psh = true;
            }
        }
    }
    return psh;
} //End of rowFound()

function wantToSave(e) {
    e.preventDefault();
    var store_id = document.getElementById('store_id').value;
    var purchase_date = document.getElementById('purchase_date').value;

    if (store_id != "" && purchase_date != "") {
        if (confirm("Sure want ot submit???") == true) {
            const form = document.getElementById('frmbuyerorder');

            // ✅ Force the real submit method to run, even if it's shadowed
            HTMLFormElement.prototype.submit.call(form);
        } else {
            return false;
        }
    } else {
        alert("Empty data!!! Please enter Store/Date first...");
        return false;
    }
}

function wantToUpdate(e) {
    e.preventDefault();
    var store_id = document.getElementById('store_id').value;
    var purchase_date = document.getElementById('purchase_date').value;

    if (store_id != "" && purchase_date != "") {
        if (confirm("Sure want ot submit???") == true) {
            const form = document.getElementById('frmbuyerorder');

            // ✅ Force the real submit method to run, even if it's shadowed
            HTMLFormElement.prototype.submit.call(form);
        } else {
            return false;
        }
    } else {
        alert("Empty data!!! Please enter Store/Date first...");
        return false;
    }
}

function calTotalValue() {
    var qty = document.frmbuyerorder.qty.value;
    var unit_price = document.frmbuyerorder.unit_price.value;

    if (isNaN(qty)) {
        qty = 0;
    }
    if (isNaN(unit_price)) {
        unit_price = 0;
    }
    var totalvalue = qty * unit_price;
    document.frmbuyerorder.total.value = totalvalue.toFixed(2);
}

function calGDiscount() {
    var total_amount = parseFloat(document.frmbuyerorder.total_amount.value);
    var general_discount_percent = parseFloat(document.frmbuyerorder.general_discount_percent.value);

    if (isNaN(total_amount)) {
        total_amount = 0;
    }
    if (isNaN(general_discount_percent)) {
        general_discount_percent = 0;
    }
    var general_discount_amount = ((total_amount / 100) * general_discount_percent);
    var netPay = (total_amount - general_discount_amount);
    document.frmbuyerorder.general_discount_amount.value = general_discount_amount.toFixed(2);
    document.frmbuyerorder.net_payble.value = netPay.toFixed(2);
    calNetPayble();
}

function calVat() {
    var total_amount = parseFloat(document.frmbuyerorder.total_amount.value);
    var vat_percent = parseFloat(document.frmbuyerorder.vat_percent.value);
    var net_payble = parseFloat(document.frmbuyerorder.net_payble.value);

    if (isNaN(total_amount)) {
        total_amount = 0;
    }
    if (isNaN(vat_percent)) {
        vat_percent = 0;
    }
    if (isNaN(net_payble)) {
        net_payble = 0;
    }
    var vat_amount = ((total_amount / 100) * vat_percent);
    //var netPay = (net_payble + vat_amount);
    document.frmbuyerorder.vat_amount.value = vat_amount.toFixed(2);
    //document.frmbuyerorder.net_payble.value = netPay.toFixed(2);
    //calNetPayble();
}

function calExDiscount() {
    var total_amount = parseFloat(document.frmbuyerorder.total_amount.value);
    var exclusive_discount_percent = parseFloat(document.frmbuyerorder.exclusive_discount_percent.value);
    var general_discount_amount = parseFloat(document.frmbuyerorder.general_discount_amount.value);
    var vat_amount = parseFloat(document.frmbuyerorder.vat_amount.value);

    if (isNaN(total_amount)) {
        total_amount = 0;
    }
    if (isNaN(exclusive_discount_percent)) {
        exclusive_discount_percent = 0;
    }
    if (isNaN(general_discount_amount)) {
        general_discount_amount = 0;
    }
    if (isNaN(vat_amount)) {
        vat_amount = 0;
    }
    var NetAmount = (total_amount - general_discount_amount);
    var exclusive_discount_amount = ((NetAmount / 100) * exclusive_discount_percent);
    var netPay = (NetAmount - exclusive_discount_amount);
    document.frmbuyerorder.exclusive_discount_amount.value = exclusive_discount_amount.toFixed(2);
    document.frmbuyerorder.net_payble.value = netPay.toFixed(2);
    calNetPayble();
}

function calNetPayble() {
    var total_amount = parseFloat(document.frmbuyerorder.total_amount.value);
    var general_discount_amount = parseFloat(document.frmbuyerorder.general_discount_amount.value);
    var exclusive_discount_amount = parseFloat(document.frmbuyerorder.exclusive_discount_amount.value);
    var additional_discount = parseFloat(document.frmbuyerorder.additional_discount.value);
    var vat_amount = parseFloat(document.frmbuyerorder.vat_amount.value);

    if (isNaN(total_amount)) {
        total_amount = 0;
    }
    if (isNaN(additional_discount)) {
        additional_discount = 0;
    }
    if (isNaN(general_discount_amount)) {
        general_discount_amount = 0;
    }
    if (isNaN(exclusive_discount_amount)) {
        exclusive_discount_amount = 0;
    }
    if (isNaN(vat_amount)) {
        vat_amount = 0;
    }
    var netPay = (total_amount - (additional_discount + general_discount_amount + exclusive_discount_amount));
    document.frmbuyerorder.net_payble.value = netPay.toFixed(2);

}

function calDueAmount() {
    var net_payble = parseFloat(document.frmbuyerorder.net_payble.value);
    var advanced_paid_amount = parseFloat(document.frmbuyerorder.advanced_paid_amount.value);
    var paid_amount = parseFloat(document.frmbuyerorder.paid_amount.value);
    //alert(net_payble);alert(paid_amount);
    if (isNaN(net_payble)) {
        net_payble = 0;
    }
    if (isNaN(advanced_paid_amount)) {
        advanced_paid_amount = 0;
    }
    if (isNaN(paid_amount)) {
        paid_amount = 0;
    }
    var totDue = (net_payble - (paid_amount + advanced_paid_amount));
    document.frmbuyerorder.due.value = totDue.toFixed(2);
}

function creditSale() {
    document.frmbuyerorder.paid_amount.value = 0;
    calDueAmount();
}

// ===== end Expected Salary ==========

function deleteRecord(id) {
    var url_loc = "index.php?app=purchase_order&cmd=delete&id=" + id;
    window.location = url_loc;
}

function checkBalance() {
    if (document.getElementById('transaction_type1').checked) {
        //var transaction_type =document.getElementById('transaction_type1').value;
        var credit = parseFloat(document.getElementById('Payment_f').value);
        var balance = parseFloat(document.getElementById('balance').value);
        if (balance < credit) {
            alert("You can not complete this transaction \nbecause credit amount(" + credit + ") is greter then Account balance(" + balance + ")");
            return false;
        } else {
            return true;
        }
    } else {
        return true;
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

