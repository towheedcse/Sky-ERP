RE_NUMBER = new RegExp(/^[0-9]+$/);

RE_EMAIL = new RegExp(/^[A-Za-z0-9](([_|\.|\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([_|\.|\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$/);

RE_NAME = new RegExp(/[^A-Z^a-z^ ^\.\^]$/);

var httpLoadProduct = getHTTPObject();
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
    document.getElementById("amount_m").innerHTML = " (per " + val + ")";
}

//********* Product List *********
function getProductList(brandArr) {
    var catagoryArr = document.getElementById('catagory').value;
    var catagoryStr = catagoryArr.split("###");
    var catagory_id = catagoryStr[0];
    var brandStr = brandArr.split("###");
    var brand_id = brandStr[0];
    if (brand_id != "") {
        httpLoadProduct.open("GET", "index.php?app=sales&cmd=loadProduct&brand_id=" + brand_id + "&catagory_id=" + catagory_id, true);
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
        productOption.options[i + 1] = new Option(arrProductIdName[2], arrProductIdName[0] + '###' + arrProductIdName[1] + '###' + arrProductIdName[2]);
    }

}

function getProductDtl(product_id) {
    product_idArr = product_id.split("###");
    product_id = product_idArr[0];
    var store_id = document.getElementById('out_store_id').value;
    //alert(voucher_no+' '+product_id);
    if (product_id != "") {
        httpLoadUP.open("GET", "index.php?app=fg.production&cmd=get_productinfo&product_id=" + product_id + "&store_id=" + store_id, true);
        httpLoadUP.onreadystatechange = handleUPResponse;
        httpLoadUP.send(null);
    }
}


function handleUPResponse() {
    if (httpLoadUP.readyState == 4) {
        //alert(httpLoadUP.responseText);
        var UPcontent = trim(httpLoadUP.responseText);
        contentArr = UPcontent.split("#####");
        document.getElementById('amount').value = contentArr[0];
        document.getElementById('stock_qty').value = contentArr[1];
        document.getElementById('product_stock_qty').value = contentArr[1];
        document.getElementById('m_unit').value = contentArr[2];
        document.getElementById('catagory').value = contentArr[3];
        document.getElementById('brand').value = contentArr[4];

    }

}

function getProductInfo(product_id) {
    product_idArr = product_id.split("###");
    product_id = product_idArr[0];
    var store_id = document.getElementById('out_store_id').value;
    //alert(voucher_no+' '+product_id);
    if (product_id != "") {
        httpLoadUP.open("GET", "index.php?app=fg.production&cmd=getProductInfo&product_id=" + product_id + "&store_id=" + store_id, true);
        httpLoadUP.onreadystatechange = handleProUPResponse;
        httpLoadUP.send(null);
    }
}


function handleProUPResponse() {
    if (httpLoadUP.readyState == 4) {
        //alert(httpLoadUP.responseText);
        var UPcontent = trim(httpLoadUP.responseText);
        const data = JSON.parse(UPcontent);

        document.getElementById('amount').value = data.unit_price;
        document.getElementById('default_amount').value = data.unit_price;
        document.getElementById('stock_qty').value = data.Stockbalance;
        document.getElementById('product_stock_qty').value = data.Stockbalance;
        document.getElementById('default_stock_qty').value = data.Stockbalance;
        document.getElementById('m_unit').value = data.m_unit;
        $("#invoice").html(value = data.invoiceList).trigger('change');

        $('#catagory option').each(function () {
            if ($(this).data('category-id') == data.catagory) {
                $(this).prop('selected', true);
                return false; // stop loop
            }
        });

        $('#yourSelectId').trigger('change');

        $('#brand option').each(function () {
            if ($(this).data('brand-id') == data.brand_code) {
                $(this).prop('selected', true);
                return false; // stop loop
            }
        });

        $('#yourSelectId').trigger('change');

    }

}

//********* End get Product List ************

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
        setRequiredField(catagory, 'dropdown', 'catagory_lbl');
        setRequiredField(brand, 'dropdown', 'brand_lbl');
        setRequiredField(product, 'dropdown', 'product_lbl');
        setRequiredField(m_unit, 'dropdown', 'm_unit_lbl');
        setRequiredField(amount, 'textbox', 'amount_lbl');
        // setRequiredField(out_qty, 'textbox', 'out_qty_lbl');
        setRequiredField(qty, 'textbox', 'qty_lbl');
    }
}

function fieldValidation(frm) {
    with (frm) {
        if (qty.value == "") {
            highlightTableColumn('qty_lbl');
            alert(ERROR_NUMBER);
            return false;
        } else {
            return true;
        }
    }
    return true;
}

function prepareGrid() {

    var stock_qty = parseFloat(document.frmbuyerorder.product_stock_qty.value) || 0;
    // var out_qty = parseFloat(document.frmbuyerorder.out_qty.value) || 0;
    // out_qty = Number((out_qty / 1000));


    // if (out_qty <= 0) {
    //alert("Out Qty must be greater than Zero");
    //return false;
    // }

    // if (stock_qty < out_qty) {
    //alert("Out Qty must be equal or less than Stock Qty");
    //return false;
    // }

    catagoryIdName = document.getElementById('catagory').value;
    catagory = catagoryIdName.split("###");
    catagoryid = catagory[0];
    catagoryname = catagory[1];

    brandIdName = document.getElementById('brand').value;
    brand = brandIdName.split("###");
    brandid = brand[0];
    brandname = brand[1];
    var pvoucher_no = "";
    productIdName = document.getElementById('product').value;
    product = productIdName.split("###");
    productid = trim(product[0]);
    productname = product[1];

    catagory_product_id = catagoryid + '###' + brandid + '###' + productid;

    m_unit = document.getElementById('m_unit').value;
    var invoice = document.getElementById('invoice_voucher').value;
    var ledger = document.getElementById('stock_ledger_id').value;

    amount = parseFloat(document.getElementById('amount').value);
    qty = parseFloat(document.getElementById('qty').value);

    var currency = document.getElementById('currency').value;
    var currencyName = "BDT";
    //if(currency=="1"){ currencyName = "BDT";}

    total_cost = document.getElementById('total_value').value;
    total_qty = parseFloat(document.getElementById('total_qty').value);
    var total_value = (amount * qty);
    total_value = total_value.toFixed(2);
    if (total_cost != "") {
        total_cost = parseFloat(total_cost);
    } else {
        total_cost = 0;
        total_qty = 0;
    }

    // if(rsFound)
    // {
    //   processDataGrid(catagoryid,catagoryname,brandid,brandname,catagory_product_id,productid,productname,pvoucher_no,m_unit,qty,currency,currencyName,amount,total_value,invoice,ledger);
    // 	rsFound = false;
    // 	var total_cost = total_cost+parseFloat(total_value); var total_qty=total_qty+qty;
    // 	document.getElementById('total_value').value=total_cost; document.getElementById('total_qty').value=total_qty;
    // }

    if (rowFound(catagory_product_id)) {
        processDataGrid(catagoryid, catagoryname, brandid, brandname, catagory_product_id, productid, productname, pvoucher_no, m_unit, qty, currency, currencyName, amount, total_value, invoice, ledger);
        var total_cost = total_cost + parseFloat(total_value);
        var total_qty = total_qty + qty;
        document.getElementById('total_value').value = total_cost;
        document.getElementById('total_qty').value = total_qty;
    }

} // End of function prepareGrid()

function processDataGrid(catagoryid, catagoryname, brandid, brandname, catagory_product_id, productid, productname, pvoucher_no, m_unit, qty, currency, currencyName, amount, total_value, invoice, ledger) {
    obj = document.getElementById('tbs');
    tr = document.createElement('tr');

    tr.setAttribute("data-id", catagory_product_id);

    tr.appendChild(addCol(catagoryname + '<input type=hidden name=input_catagory_product_id[] id=catagory_product_id[] value=' + catagory_product_id + '>'));
    tr.appendChild(addCol(brandname + '<input type=hidden name=input_brand[' + catagory_product_id + '] id=input_brand[' + catagory_product_id + '] value=' + brandname + '>'));
    tr.appendChild(addCol(productname + '<input type=hidden name=input_product[' + catagory_product_id + '] id=input_product[' + catagory_product_id + '] value=' + product + '>'));

    tr.appendChild(addCol(m_unit + '<input type=hidden name=input_m_unit[' + catagory_product_id + '] id=input_m_unit[' + catagory_product_id + '] value=' + m_unit + '><input type=hidden name=input_pvoucher_no[' + catagory_product_id + '] id=input_pvoucher_no[' + catagory_product_id + '] value=' + pvoucher_no + '>'));
    tr.appendChild(addCol(qty + ' ' + m_unit + '<input type=hidden name=input_qty[' + catagory_product_id + '] id=input_qty[' + catagory_product_id + '] value="' + qty + '">'));
    tr.appendChild(addCol(currencyName + '<input type=hidden name=input_currency[' + catagory_product_id + '] id=input_currency[' + catagory_product_id + '] value=' + currency + '><input type=hidden name=input_amount[' + catagory_product_id + '] id=input_amount[' + catagory_product_id + '] value=' + amount + '>'));
    //tr.appendChild(addCol(total_value+'<input type=hidden name=input_amount['+catagory_product_id+'] id=input_amount['+catagory_product_id+'] value='+amount+'>'));

    tr.appendChild(addCol(invoice + '<input type=hidden name=input_invoice_voucher[' + catagory_product_id + '] id=input_invoice_voucher[' + catagory_product_id + '] value=' + invoice + '>' + '<input type=hidden name=input_stock_ledger_id[' + catagory_product_id + '] id=input_stock_ledger_id[' + catagory_product_id + '] value=' + ledger + '>'));

    let actionCol = document.createElement('td');
    actionCol.innerHTML = `
        <button type="button" onclick="deleteRow(this)" style="color:red;">Delete</button>
    `;
    tr.appendChild(actionCol);

    obj.appendChild(tr);
    rsFound = false;
    needSave = true;
}


function recalculateTotals() {
    let total_cost = 0;
    let total_qty = 0;

    document.querySelectorAll('#tbs tr').forEach(row => {
        let qty = parseFloat(row.querySelector('[name^="input_qty"]')?.value) || 0;
        let amount = parseFloat(row.querySelector('[name^="input_amount"]')?.value) || 0;

        total_qty += qty;
        total_cost += qty * amount;
    });

    document.getElementById('total_value').value = total_cost.toFixed(2);
    document.getElementById('total_qty').value = total_qty;
}

function deleteRow(btn) {
    let row = btn.closest('tr');

    let idInput = row.querySelector('[name="input_catagory_product_id[]"]');
    let id = idInput ? idInput.value : null;

    if (id) {
        rsArr = rsArr.filter(item => item !== id);
    }

    row.parentNode.removeChild(row);
    recalculateTotals();
}


function editRow(btn) {
    let row = btn.closest('tr');

    let id = row.getAttribute("data-id");

    if (id) {
        rsArr = rsArr.filter(item => item !== id);
    }

    let productValue = row.querySelector('[name^="input_product"]').value;
    let invoiceValue = row.querySelector('[name^="input_invoice_voucher"]').value;

    // ✅ set product (Chosen compatible)
    $('#product')
        .val(productValue)
        .trigger('change')
        .trigger('chosen:updated');

    // ✅ set invoice (if also chosen)
    $('#invoice')
        .val(invoiceValue)
        .trigger('change')
        .trigger('chosen:updated');

    // normal fields
    document.getElementById('qty').value =
        row.querySelector('[name^="input_qty"]').value;

    document.getElementById('amount').value =
        row.querySelector('[name^="input_amount"]').value;

    document.getElementById('stock_ledger_id').value =
        row.querySelector('[name^="input_stock_ledger_id"]').value;

    row.parentNode.removeChild(row);

    recalculateTotals();
}


var rsArr = Array();

function rowFound(idx) {
    if (rsArr.includes(idx)) {
        alert("You have already chosen!!");
        return false;
    }

    rsArr.push(idx);
    return true;
}

function rowFoundOld(idx) {
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

function calTotalQty() {
    var stock_qty = parseFloat(document.frmbuyerorder.product_stock_qty.value);
    //var out_qty = parseFloat(document.frmbuyerorder.out_qty.value);
    //alert(total_value);alert(discount);
    //if (out_qty == '' || out_qty == 0 || isNaN(out_qty)) {
    //    out_qty = 0;
    //}

    if (stock_qty == '' || stock_qty == 0 || isNaN(stock_qty)) {
        stock_qty = 0;
    }

    var qty = parseFloat(document.frmbuyerorder.qty.value);
    if (qty == '' || qty == 0 || isNaN(qty)) {
        qty = 0;
    }

    //var qty = Number((out_qty / 1000));
    if (stock_qty < qty) {
        $("#saveItemBtn").prop("disabled", true).addClass("btn-disabled");
    } else {
        $("#saveItemBtn").prop("disabled", false).removeClass("btn-disabled");
    }

    //document.frmbuyerorder.qty.value = qty.toFixed(3);

}

function CostOfGoods() {
    var overhead_cost = parseFloat(document.frmbuyerorder.overhead_cost.value);
    var total_value = parseFloat(document.frmbuyerorder.total_value.value);
    //alert(total_value);alert(discount);
    if (total_value == '' || total_value == 0) {
        total_value = 0;
    }
    if (overhead_cost == '' || overhead_cost == 0) {
        overhead_cost = 0;
    }

    var netPay = (total_value + overhead_cost);
    document.frmbuyerorder.sold_cost.value = netPay.toFixed(2);

}

function wantToSave(e) {
    e.preventDefault();
    var inventory_type = $("#inventory_type").val();
    if (inventory_type == "") {
        alert("please select inventory type");
        return false;
    }

    var job_name = $("#job_name").val();
    if (job_name == "") {
        alert("Please select Job ID/Name");
        return false;
    }

    var finish_product = $("#finish_product").val();
    if (finish_product == "") {
        alert("Please select Finis Product");
        return false;
    }

    if (finish_product != "") {
        var finish_qty = $("#finish_qty").val();
        var production_date = $("#used_date").val();

        // Check quantity
        if (finish_qty === "" || isNaN(finish_qty) || Number(finish_qty) <= 0) {
            alert("Please enter a valid finish quantity greater than 0");
            return false;
        }

        // Check date
        if (production_date === "") {
            alert("Please select a production date");
            return false;
        }
    }


    var wastage_product = $("#wastage_product").val();
    // if(wastage_product == ""){
    // alert("Please select Wastage Item");
    // return false;
    //}
    if (wastage_product != "") {
        var wastage_qty = $("#wastage_qty").val();

        // Check quantity
        if (wastage_qty === "" || isNaN(wastage_qty) || Number(wastage_qty) <= 0) {
            alert("Please enter a valid wastage quantity greater than 0");
            return false;
        }
    }

    if (needSave) {
        if (confirm("Sure want ot submit???") == true) {
            $('#submit_hidden').val('Save');
            const form = document.getElementById('frmbuyerorder');
            HTMLFormElement.prototype.submit.call(form);
        } else {
            return false;
        }
    } else {
        alert("Empty data!!! Please enter data first...");
    }
}


function deleteRecord(id) {
    var url_loc = "index.php?app=purchase&cmd=delete&id=" + id;
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
