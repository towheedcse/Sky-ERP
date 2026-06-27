RE_NUMBER = new RegExp(/^[0-9]+$/);
RE_EMAIL = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);
RE_NAME = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadProduct = getHTTPObject();
var httpLoadUP = getHTTPObject();
var httpLoadPDTL = getHTTPObject();
var isIE = document.all;

var rsFound = false;
var needSave = false;
var rsArr = Array();

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

//********* Product List *********
function getProductList(brandArr) {
    var catagoryArr = document.getElementById('catagory').value;
    var catagoryStr = catagoryArr.split("###");
    var catagory_id = catagoryStr[0];
    var brandStr = brandArr.split("###");
    var brand_id = brandStr[0];

    if (brand_id != "") {
        httpLoadProduct.open("GET", "index.php?app=purchase_order&cmd=loadProduct&brand_id=" + brand_id + "&catagory_id=" + catagory_id, true);
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
    productOption.options[0] = new Option("Select One", "-1");
    for (i = 0; i < arrProduct.length - 1; i++) {
        var arrProductIdName = Array();
        arrProductIdName = arrProduct[i].split("#####");
        productOption.options[i + 1] = new Option(arrProductIdName[1] + '- ' + arrProductIdName[2], arrProductIdName[0] + '###' + arrProductIdName[1]);
    }

}

//=========== Start Get Product Unit =====
function getMUnit(product_id) {
    product_idArr = product_id.split("###");
    product_id = product_idArr[0];
    if (product_id != "") {
        httpLoadUP.open("GET", "index.php?app=purchase_order&cmd=get_munit&product_id=" + product_id, true);
        httpLoadUP.onreadystatechange = handleUPResponse;
        httpLoadUP.send(null);
    }
}

function handleUPResponse() {
    if (httpLoadUP.readyState == 4) {
        //alert(httpLoadUP.responseText);
        var unite = trim(httpLoadUP.responseText);
        unitArr = unite.split("###");
        document.getElementById('m_unit').value = unitArr[0];
        document.getElementById('total_unit').value = unitArr[1];
    }

}

//=========== End Get Product Unit  =====
//********* End get Product List ************
function getProductDtl(product_id) {
    product_idArr = product_id.split("###");
    product_id = product_idArr[0];
    if (product_id != "") {
        httpLoadPDTL.open("GET", "index.php?app=purchase&cmd=getproductdtl&product_id=" + product_id, true);
        httpLoadPDTL.onreadystatechange = handlePDTLResponse;
        httpLoadPDTL.send(null);
    }
}

function handlePDTLResponse() {
    if (httpLoadPDTL.readyState == 4) {
        //alert(httpLoadPDTL.responseText);
        var content = trim(httpLoadPDTL.responseText);
        contentArr = content.split("###");
        document.getElementById('m_unit').value = contentArr[0];
        document.getElementById('details').value = contentArr[2];
        document.getElementById('catagory').value = contentArr[3] + "###" + contentArr[5];
        document.getElementById('brand').value = contentArr[4] + "###" + contentArr[6];
        checkUnit(contentArr[0]);

    }

}

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

function purchaseProcess() {
    if (includeGrid()) {
        prepareGrid();
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
        //setRequiredField(catagory,           'dropdown',  'catagory_lbl');
        //setRequiredField(supplier,			 'dropdown',  'supplier_lbl');
        setRequiredField(product, 'dropdown', 'product_lbl');
        //setRequiredField(m_unit,			 'dropdown',  'm_unit_lbl');
        setRequiredField(unit_price, 'textbox', 'unit_price_lbl');
        setRequiredField(qty, 'textbox', 'qty_lbl');
        setRequiredField(currency, 'textbox', 'currency_lbl');
        setRequiredField(purchase_type, 'dropdown', 'purchase_type_lbl');
    }
}

function fieldValidation(frm) {
    with (frm) {
        if (!RE_DECIMAL.exec(qty.value)) {
            highlightTableColumn('qty_lbl');
            alert(ERROR_NUMBER);
            return false;
        } else if (currency.value == "") {
            highlightTableColumn('currency_lbl');
            alert(ERROR_NUMBER);
            return false;
        } else {
            return true;
        }
    }
    return true;
}

function prepareGrid() {
    catagoryIdName = document.getElementById('catagory').value;
    catagory = catagoryIdName.split("###");
    catagoryid = catagory[0];
    catagoryname = catagory[1];

    brandIdName = document.getElementById('brand').value;
    brand = brandIdName.split("###");
    brandid = brand[0];
    brandname = brand[1];

    productIdName = document.getElementById('product').value;
    product = productIdName.split("###");
    productid = trim(product[0]);
    productname = product[1];

    var custom_brand = document.getElementById('custom_brand').value;
    if (custom_brand != "") {
        brandname = custom_brand;
    }

    catagory_product_id = catagoryid + '###' + productid;
    //alert(catagory_product_id);
    pdetails = document.getElementById('details').value;
    pdetails = pdetails.replace('"', "&rdquo;");
    pdetails = pdetails.replace('"', "&rdquo;");
    pdetails = pdetails.replace('"', "&rdquo;");
    pdetails = pdetails.replace('"', "&rdquo;");
    pdetails = pdetails.replace('"', "&rdquo;");
    pdetails = pdetails.replace('"', "&rdquo;");
    pdetails = pdetails.replace('"', "&rdquo;");
    pdetails = pdetails.replace('"', "&rdquo;");
    m_unit = document.getElementById('m_unit').value;
    total_unit = document.getElementById('total_unit').value;

    unit_price = parseFloat(document.getElementById('unit_price').value);
    var vat_percent = parseFloat(document.getElementById('vat_percent').value);
    var vat_amount = parseFloat(document.getElementById('vat_amount').value);

    if (isNaN(vat_percent)) {
        vat_percent = 0;
    }

    if (isNaN(vat_amount)) {
        vat_amount = 0;
    }

    qty = parseFloat(document.getElementById('qty').value);
    total_bag = document.getElementById('total_bag').value;
    if (total_bag == "") {
        total_bag = 0;
    }
    currencyIdName = document.getElementById('currency').value;
    currencyArr = currencyIdName.split("###");
    currency = currencyArr[0];
    currencyName = currencyArr[1];

    total_cost = document.getElementById('total_value').value;
    total_value = unit_price * qty;
    total_value = total_value.toFixed(11);
    if (total_cost != "") {
        total_cost = parseFloat(total_cost);

    } else {
        total_cost = 0;
    }


    var edit_spr_voucher_no = document.getElementById('edit_spr_voucher_no').value;
    var edit_spd_id = document.getElementById('edit_spd_id').value;
    var edit_product_id = document.getElementById('edit_product_id').value;
    var edit_max_qty = document.getElementById('edit_max_qty').value;

    if (edit_spr_voucher_no != "" && edit_spd_id != "" && edit_product_id != "" && edit_product_id == productid) {
        var tbody = document.getElementById('tbs'); // Assuming rows are inside a <table>
        var allTRRows = tbody.querySelectorAll('tr');

        allTRRows.forEach(function (row) {
            var spr_voucher_no = row.querySelector('input[name^="spr_voucher_no"]').value;
            var input_product = row.querySelector('input[name^="input_product"]').value;
            var spd_id = row.querySelector('input[name^="spd_id"]').value;

            if (spr_voucher_no == edit_spr_voucher_no && spd_id == edit_spd_id && input_product == edit_product_id) {

                row.querySelector('input[name^="input_custom_brand"]').value = custom_brand;
                row.querySelector('input[name^="input_pdetails"]').value = pdetails;
                row.querySelector('input[name^="input_qty"]').value = qty;
                row.querySelector('input[name^="input_unit_price"]').value = unit_price;
                (row.querySelector('input[name^="input_vat_percent"]') || {}).value = vat_percent || "";
                (row.querySelector('input[name^="input_vat_amount"]') || {}).value = vat_amount || "";
                row.querySelector('input[name^="input_total_value"]').value = total_value;

                document.querySelector('span.display_brand[data-id="' + catagory_product_id + '"]').textContent = brandname;
                document.querySelector('span.display_qty[data-id="' + catagory_product_id + '"]').textContent = qty;
                document.querySelector('span.display_unit_price[data-id="' + catagory_product_id + '"]').textContent = unit_price;
                const vatSpan = document.querySelector('span.display_vat_percent[data-id="' + catagory_product_id + '"]');
                if (vatSpan) {
                    vatSpan.textContent = vat_percent || "";
                }
                const vatAmountSpan = document.querySelector('span.display_vat_amount[data-id="' + catagory_product_id + '"]');
                if (vatAmountSpan) {
                    vatAmountSpan.textContent = vat_amount || "";
                }
                document.querySelector('span.display_total_value[data-id="' + catagory_product_id + '"]').textContent = total_value;

                calAllValue();
            }
        });

    } else if (edit_spr_voucher_no == "" && edit_spd_id == "" && edit_product_id != "" && edit_product_id == productid) {
        var tbody = document.getElementById('tbs'); // Assuming rows are inside a <table>
        var allTRRows = tbody.querySelectorAll('tr');

        allTRRows.forEach(function (row) {
            var input_product = row.querySelector('input[name^="input_product"]').value;
            var catagory_product_id = row.querySelector('input[name^="input_catagory_product_id[]"]').value;

            if (input_product == edit_product_id && catagory_product_id == catagory_product_id) {
                row.querySelector('input[name^="input_custom_brand"]').value = custom_brand;
                row.querySelector('input[name^="input_pdetails"]').value = pdetails;
                row.querySelector('input[name^="input_qty"]').value = qty;
                row.querySelector('input[name^="input_unit_price"]').value = unit_price;
                (row.querySelector('input[name^="input_vat_percent"]') || {}).value = vat_percent || "";
                (row.querySelector('input[name^="input_vat_amount"]') || {}).value = vat_amount || "";
                row.querySelector('input[name^="input_total_value"]').value = total_value;

                document.querySelector('span.display_brand[data-id="' + catagory_product_id + '"]').textContent = brandname;
                document.querySelector('span.display_qty[data-id="' + catagory_product_id + '"]').textContent = qty;
                document.querySelector('span.display_unit_price[data-id="' + catagory_product_id + '"]').textContent = unit_price;
                const vatSpan = document.querySelector('span.display_vat_percent[data-id="' + catagory_product_id + '"]');
                if (vatSpan) {
                    vatSpan.textContent = vat_percent || "";
                }
                const vatAmountSpan = document.querySelector('span.display_vat_amount[data-id="' + catagory_product_id + '"]');
                if (vatAmountSpan) {
                    vatAmountSpan.textContent = vat_amount || "";
                }
                document.querySelector('span.display_total_value[data-id="' + catagory_product_id + '"]').textContent = total_value;

                calAllValue();
            }
        });
    } else {
        if (rsFound) {
            processDataGrid(catagoryid, catagoryname, brandid, brandname, catagory_product_id, productid, productname, custom_brand, pdetails, m_unit, unit_price, vat_percent, vat_amount, qty, total_bag, total_unit, currency, currencyName, total_value);
            rsFound = false;
            total_cost = total_cost + parseFloat(total_value);
            document.getElementById('total_value').value = total_cost;
        }

        if (rowFound(catagory_product_id)) {
            processDataGrid(catagoryid, catagoryname, brandid, brandname, catagory_product_id, productid, productname, custom_brand, pdetails, m_unit, unit_price, vat_percent, vat_amount, qty, total_bag, total_unit, currency, currencyName, total_value);
            total_cost = total_cost + parseFloat(total_value);
            document.getElementById('total_value').value = total_cost;
        }
        calAllValue();
    }

    resetAddFrom();


} // End of function prepareGrid()


function processDataGrid(catagoryid, catagoryname, brandid, brandname, catagory_product_id, productid, productname, custom_brand, pdetails, m_unit, unit_price, vat_percent, vat_amount, qty, total_bag, total_unit, currency, currencyName, total_value) {
    var spr_voucher_no = "";
    var spd_id = "";

    var productNameInput = `<input type=hidden name=input_pro_name[] id=input_pro_name['${catagory_product_id}'] value='${productname}'>`;

    obj = document.getElementById('tbs');
    tr = document.createElement('tr');
    tr.appendChild(addCol(catagoryname + '<input type=hidden name=input_catagory_product_id[] id=catagory_product_id[] value=' + catagory_product_id + '>'));
    tr.appendChild(addCol('<span class="display_brand" data-id="' + catagory_product_id + '">' + brandname + '</span><input type=hidden name=input_brandid[] id=input_brandid[' + catagory_product_id + '] value=' + brandid + '><input type=hidden name=input_brand[] id=input_brand[' + catagory_product_id + '] value="' + brandname + '"><input type=hidden name=input_custom_brand[] id=input_custom_brand[' + catagory_product_id + '] value="' + custom_brand + '">'));
    tr.appendChild(addCol(productname + productNameInput + '<input type=hidden name=input_product[] id=input_product[' + catagory_product_id + '] value=' + productid + '><input type="hidden" name="input_pdetails[' + catagory_product_id + ']" id="input_pdetails[' + catagory_product_id + ']" value="' + pdetails + '"><input type="hidden" name="spr_voucher_no[' + catagory_product_id + ']" id="spr_voucher_no[' + catagory_product_id + ']" value="' + spr_voucher_no + '"><input type="hidden" name="spd_id[' + catagory_product_id + ']" id="spd_id[' + catagory_product_id + ']" value="' + spd_id + '">'));
    tr.appendChild(addCol(m_unit));

    tr.appendChild(addCol('<span class="display_qty" data-id="' + catagory_product_id + '">' + qty + '</span><input type=hidden name=input_qty[' + catagory_product_id + '] id=input_qty[' + catagory_product_id + '] value="' + qty + '"><input type=hidden name=input_max_qty[' + catagory_product_id + '] id=input_max_qty[' + catagory_product_id + '] value="' + qty + '"><input type=hidden name=input_total_bag[' + catagory_product_id + '] id=input_total_bag[' + catagory_product_id + '] value="' + total_bag + '"><input type=hidden name=input_m_unit[' + catagory_product_id + '] id=input_m_unit[' + catagory_product_id + '] value=' + m_unit + '><input type=hidden name=input_total_unit[' + catagory_product_id + '] id=input_total_unit[' + catagory_product_id + '] value=' + total_unit + '>'));
    //tr.appendChild(addCol(total_bag+' '+total_unit+'<input type=hidden name=input_total_bag['+catagory_product_id+'] id=input_total_bag['+catagory_product_id+'] value="'+total_bag+'">'));

    //tr.appendChild(addCol(currencyName+'<input type=hidden name=input_currency['+catagory_product_id+'] id=input_currency['+catagory_product_id+'] value='+currency+'>'));
    tr.appendChild(addCol('<span class="display_unit_price" data-id="' + catagory_product_id + '">' + unit_price + '</span><input type=hidden name=input_unit_price[' + catagory_product_id + '] id=input_unit_price[' + catagory_product_id + '] value=' + unit_price + '>'));

    //tr.appendChild(addCol('<span class="display_vat_percent" data-id="' + catagory_product_id + '">' + vat_percent + '</span><input type=hidden name=input_vat_percent[' + catagory_product_id + '] id=input_vat_percent[' + catagory_product_id + '] value=' + vat_percent + '>'));

//tr.appendChild(addCol('<span class="display_vat_amount" data-id="' + catagory_product_id + '">' + vat_amount + '</span><input type=hidden name=input_vat_amount[' + catagory_product_id + '] id=input_vat_amount[' + catagory_product_id + '] value=' + vat_amount + '>'));

    var inputString = '<span class="display_vat_percent d-none" data-id="' + catagory_product_id + '">' + vat_percent + '</span><input type=hidden name=input_vat_percent[' + catagory_product_id + '] id=input_vat_percent[' + catagory_product_id + '] value=' + vat_percent + '><span class="display_vat_amount  d-none" data-id="' + catagory_product_id + '">' + vat_amount + '</span><input type=hidden name=input_vat_amount[' + catagory_product_id + '] id=input_vat_amount[' + catagory_product_id + '] value=' + vat_amount + '>';

    tr.appendChild(addCol('<span class="display_total_value" data-id="' + catagory_product_id + '">' + total_value + '</span><input type=hidden name=input_total_value[' + catagory_product_id + '] id=input_total_value[' + catagory_product_id + '] value=' + total_value + '>' + inputString));

    tr.appendChild(addCol('<a href="javascript:void(0)" onclick=editTr(this) style="margin-left: 10px;"><img src="images/common/icons/edit.gif" style="padding: 5px;"></a><a href="javascript:void(0)" onclick=removeTr(this) style="margin-left: 10px;"><img src="images/common/icons/delete.gif" style="padding: 5px;"></a>'));

    obj.appendChild(tr);
    rsFound = false;
    needSave = true;
}


function calAllValue() {
    let total_value = 0;

    // Select all input fields inside tbody#tbs that have IDs starting with 'input_total_value'
    const inputs = document.querySelectorAll('#tbs input[id^="input_total_value"]');

    inputs.forEach(input => {
        const value = Number(input.value);
        if (!Number.isNaN(value)) {
            total_value += value;
        }
    });

    document.getElementById('total_value').value = parseFloat(total_value);

    var discount = document.getElementById('discount').value;
    if (discount == '' || discount == 0) {
        discount = 0;
    } else {
        discount = parseFloat(discount);
    }
    var netPay = (parseFloat(total_value) - discount);
    document.getElementById('net_payble').value = netPay;
    document.getElementById('grand_total').value = netPay;
    calTotalVat();
}


function rowFound(idx) {
    var psh = false;
    var arrlen = rsArr.length;
    if (rsArr.length == 0) {
        rsArr.push(idx);
        psh = true;
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


function removeTr(element) {
    var tr = element.closest('tr');

    if (tr && tr.parentNode) {
        // Get the voucher_no value from this row
        var input = tr.querySelector('input[name^="spr_voucher_no"]');

        if (input) {
            var sprVoucherNo = input.value;
            var table = tr.closest('table'); // Assuming rows are inside a <table>
            var allRows = table.querySelectorAll('tr');

            var count = 0;

            allRows.forEach(function (row) {
                if (row !== tr) {
                    var rowInput = row.querySelector('input[name^="spr_voucher_no"]');
                    if (rowInput && rowInput.value === sprVoucherNo) {
                        count++;
                    }
                }
            });

            // If this is the only row with the voucher, remove from spr_no_value
            if (count === 0) {
                let sprField = document.getElementById('spr_no_value');
                if (sprField && sprField.value) {
                    let currentValues = sprField.value.split(',').map(v => v.trim()).filter(Boolean);
                    let updatedValues = currentValues.filter(val => val !== sprVoucherNo);
                    sprField.value = updatedValues.join(',');
                    document.getElementById('showSprNo').innerHTML = updatedValues.join(',');
                }
            }


        }

        var catagory_product_id = tr.querySelector('input[name^="input_catagory_product_id[]"]').value;
        var index = rsArr.indexOf(catagory_product_id);
        if (index !== -1) {
            rsArr.splice(index, 1); // Remove the item
        }

        tr.parentNode.removeChild(tr);
        calAllValue();
    }
}

function wantToSave(e) {
    e.preventDefault();

    var purchase_type = document.getElementById('purchase_type').value;
    if (purchase_type == "") {
        alert("Please select purchase type!");
        return false;
    }

    var supplier = document.getElementById('supplier').value;
    var payable_id = document.getElementById('payable_id').value;

    if (purchase_type == "import") {
        if (payable_id == "") {
            alert("Please select payable first...");
            return false;
        }
    } else {
        if (supplier == "") {
            alert("Please select supplier first...");
            return false;
        }
    }

    var spr_no = document.getElementById('spr_no').value;
    var manual_spr_no = document.getElementById('manual_spr_no').value;
    if (spr_no == "" && manual_spr_no == "") {
        alert("Please input Manual SPR No");
        return false;
    }

    if (needSave) {
        if (confirm("Sure want ot submit???")) {
            const form = document.getElementById('frmbuyerorder');

            // ✅ Force the real submit method to run, even if it's shadowed
            HTMLFormElement.prototype.submit.call(form);
        } else {
            return false;
        }

    } else {
        alert("Empty data!!! Please enter data first...");
        return false;
    }
}


function wantToUpdate(e) {
    e.preventDefault();

    var purchase_type = document.getElementById('purchase_type').value;
    if (purchase_type == "") {
        alert("Please select purchase type!");
        return false;
    }

    var supplier = document.getElementById('supplier').value;
    var payable_id = document.getElementById('payable_id').value;

    if (purchase_type == "import") {
        if (payable_id == "") {
            alert("Please select payable first...");
            return false;
        }
    } else {
        if (supplier == "") {
            alert("Please select supplier first...");
            return false;
        }
    }

    var spr_no = document.getElementById('spr_no_value').value;
    var manual_spr_no = document.getElementById('manual_spr_no').value;
    if (spr_no == "" && manual_spr_no == "") {
        alert("Please input Manual SPR No");
        return false;
    }

    if (confirm("Sure want ot submit???")) {
        const form = document.getElementById('frmbuyerorder');

        // ✅ Force the real submit method to run, even if it's shadowed
        HTMLFormElement.prototype.submit.call(form);
    } else {
        return false;
    }
}


let calTimer;

function calTotalValue() {
    clearTimeout(calTimer);
    calTimer = setTimeout(function () {
        calTotalValueCore();
    }, 800); // delay (ms)
}

function calTotalValueCore() {
    var edit_max_qty_el    = document.getElementById('edit_max_qty');
    var edit_spr_no_el     = document.getElementById('edit_spr_no');
    var edit_product_id_el = document.getElementById('edit_product_id');
    var product_el         = document.getElementById('product');

    var edit_max_qty    = edit_max_qty_el    ? parseFloat(edit_max_qty_el.value)    : NaN;
    var edit_spr_no     = edit_spr_no_el     ? (edit_spr_no_el.value || "")         : "";
    var edit_product_id = edit_product_id_el ? parseFloat(edit_product_id_el.value) : NaN;

    var qty = parseFloat(document.frmbuyerorder.qty.value);

    var product_id;
    if (product_el && product_el.value) {
        var product = product_el.value.split("###");
        product_id = product[0];
    }

    if (edit_product_id && edit_product_id != "" && product_id == edit_product_id) {
        if (edit_max_qty && edit_max_qty != "" && edit_max_qty < qty && edit_spr_no != "") {
            document.getElementById('qty').value = edit_max_qty;
            qty = edit_max_qty;
            alert(`Product Qty must be less than or equal to ${edit_max_qty}`);
        }
    }

    if (edit_max_qty && edit_max_qty != "" && edit_max_qty < qty && edit_spr_no != "") {
        document.getElementById('qty').value = edit_max_qty;
        qty = edit_max_qty;
        alert(`Product Qty must be less than or equal to ${edit_max_qty}`);
    }

    //if (edit_min_qty && edit_min_qty != "" && edit_min_qty > qty) {
    //  document.getElementById('qty').value = edit_min_qty;
    //  qty = edit_min_qty;
    //  alert(`Product Qty must be greater than or equal to ${edit_min_qty}`);
    //}

    var unit_price = document.frmbuyerorder.unit_price.value;

    if (qty == '' || qty <= 0) {
        qty = 1;
        document.getElementById('qty').value = qty;
    }
    if (unit_price == '' || unit_price == 0) {
        unit_price = 0;
    }

    var totalvalue = qty * unit_price;
    document.frmbuyerorder.total.value = totalvalue;
    calVatAmount();
}


function calVatAmount() {
    var qty = parseFloat(document.frmbuyerorder.qty.value);
    var unit_price = parseFloat(document.frmbuyerorder.unit_price.value);
    var vat_percent = parseFloat(document.frmbuyerorder.vat_percent.value);

    if (qty == '' || qty == 0) {
        qty = 0;
    }
    if (unit_price == '' || unit_price == 0) {
        unit_price = 0;
    }
    if (vat_percent == '' || vat_percent == 0) {
        vat_percent = 0;
    }

    var totalvalue = qty * unit_price;
    var vatAmount = ((vat_percent / 100) * totalvalue);
    document.frmbuyerorder.vat_amount.value = vatAmount.toFixed(9);
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

