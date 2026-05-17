<?php

require_once(__DIR__ . "/../../index.php");
require_once($_SERVER['DOCUMENT_ROOT'] . PROJECT_DIR . '/configs/common/main.conf.php');
require_once($_SERVER['DOCUMENT_ROOT'] . PROJECT_DIR . '/configs/common/database.conf.php');
require_once(CLASS_DIR . '/common.list.class.php');

// Create a connection
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check the connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// get data from database
function getData($sql)
{
    global $connection;

    // Execute the query
    $result = mysqli_query($connection, $sql);

    // Check if the query was successful
    if (!$result) {
        die("Query failed: " . mysqli_error($connection));
    }

    return $result;
}


/*****************  helper function start  *******************/
const PROJECTID = "P0005";


?>


<link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
/>


<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link rel="stylesheet" type="text/css"
      href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/uuid/dist/umd/uuidv4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


<div class="bg-base-200 main-container" style="height:fit-content;">
    <div class="before-main" style="height:100%">
        <div class="main-sec" style="padding-top: 25px;width:100%;height:100%">
            <div class="dashboard space-y-6">
                <!-- header -->
                <div class="text-center bg-light-green p-4 rounded-md shadow">
                    <div class="text-2xl font-bold text-dark-green">MIS DASH BOARD</div>
                </div>
                <div class="db-filter bg-light-green">
                    <div class="date-form-wrapper">
                        <label class="input-wrapper">
                            From Date
                            <input
                                    type="date"
                                    name="from_date"
                                    id="from_date"
                                    class="grow font-normal"
				    oninput="selectDate('from_date')"
                            />
                        </label>
                        <label class="input-wrapper">
                            To Date
                            <input
                                    type="date"
                                    name="to_date"
                                    id="to_date"
                                    class="grow font-normal"
				    oninput="selectDate('to_date')"
                            />
                        </label>
                        <button class="filter-btn" type="button" id="db-filter-btn" onclick="selectDaterange()">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                    <div class="radio-btn-wrapper">
                        <label class="radio-label">
                            <input
                                    type="radio"
                                    name="radio-10"
                                    class="radio-container"
                                    id="db_radio_filter_today"
				    onclick="selectDate('today')"
                            />
                            <span class="db-radio-text">Today</span>
                        </label>
                        <label class="radio-label">
                            <input
                                    type="radio"
                                    name="radio-10"
                                    class="radio-container"
                                    id="db_radio_filter_week"
				    onclick="selectDate('week')"
                            />
                            <span class="db-radio-text">This Week</span>
                        </label>
                        <label class="radio-label">
                            <input
                                    type="radio"
                                    name="radio-10"
                                    class="radio-container"
                                    id="db_radio_filter_month"
				    onclick="selectDate('month')"
				    checked
                            />
                            <span class="db_radio_text">This Month</span>
                        </label>
                        <label class="radio-label">
                            <input
                                    type="radio"
                                    name="radio-10"
                                    class="radio-container"
                                    id="db_radio_filter_year"
				    onclick="selectDate('year')"
                            />
                            <span class="db-radio-text">This Year</span>
                        </label>
                    </div>
                </div>
                <!-- charts start -->
                <div class="charts col-layout">
                    <div class="mis-col-five">
                        <!-- column chart start -->
                        <div class="mis-box-chart col-span-5 2xl:col-span-5">
                            <!-- <div class="box-title">Bar chart</div> -->
                            <div id="column-chart"></div>
                        </div>
                        <!-- column chart end -->
                        <!-- mixed chart -->
                        <div class="col-span-5 2xl:col-span-5">
                            <div class="cash-layout">
                                <!-- <div class="box-title">Cash Flow Projection</div> -->
                                <div class="mis-col-six-fixed">
                                    <!-- radial receive Chart -->
                                    
                                    <div
                                            class="col-span-8 md:col-span-8 xl:col-span-8 mis-box-chart"
                                    >
                                        <!-- <div class="box-title">Profit/Loss</div> -->
                                        <div id="profit-loss-chart"></div>
                                    </div>
                                </div>
                                <div class="mis-col-one">
                                    <div class="mis-box-chart">
                                        <!-- <div class="box-title">Cash Flow</div> -->
                                        <div id="cashflow-chart">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- mixed chart end -->
                </div>

		<!-- <div class="charts col-layout">
                    <div class="mis-col-five">
                        <div class="mis-box-chart col-span-5 2xl:col-span-5">
                            <div id="sales-order-chart"></div>
                        </div>
                    </div>
                </div> -->

            </div>
            <!-- charts end -->
        </div>
        <!-- box rows -->
        <div class="box-rows mt-6 col-layout">
            <div class="mis-col-four">
                <div class="mis-box">
                    <div class="box-title">Cash & Bank</div>
                    <div class="items">
                        <div class="item">
                            <div class="box-content">
                                Cash In Hand
                                <span>:</span>
                            </div>
                            <div class="numbers" id="cash_in_hand">
                                0
                                <span>Dr</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content">
                                Cash At Bank
                                <span>:</span>
                            </div>
                            <div class="numbers ul" id="cash_at_bank">
                                0
                                <span>Dr</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content">
                                Total
                                <span>:</span>
                            </div>
                            <div class="numbers double-ul" id="cash_in_hand_and_bank_total">
                                0
                                <span>Dr</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mis-box">
                    <div class="box-title">Cash Flow</div>
                    <div class="items">
                        <div class="item">
                            <div class="box-content">
                                Cash-in-Flow
                                <span>:</span>
                            </div>
                            <div class="numbers">
                                0
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content">
                                Cash-out-Flow
                                <span>:</span>
                            </div>
                            <div class="numbers ul">
                                0
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content">
                                Total
                                <span>:</span>
                            </div>
                            <div class="numbers double-ul">
                                0
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mis-box">
                    <div class="box-title">Outstandings</div>
                    <div class="items">
                        <div class="item">
                            <div class="box-content">
                                Receivables
                                <span>:</span>
                            </div>
                            <div class="numbers" id="account_receivables">
                                0
                                <span>Dr</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content">
                                Payables
                                <span>:</span>
                            </div>
                            <div class="numbers ul" id="account_payables">
                                0
                                <span>Dr</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content">
                                Total
                                <span>:</span>
                            </div>
                            <div class="numbers double-ul" id="account_total">
                                0
                                <span>Dr</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mis-box">
                    <div class="box-title">Capital & Fixed Assets</div>
                    <div class="items">
                        <div class="item">
                            <div class="box-content">
                                Capital Invested
                                <span>:</span>
                            </div>
                            <div class="numbers" id="capital">
                                0
                                <span>Dr</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content">
                                Fixed Assets
                                <span>:</span>
                            </div>
                            <div class="numbers" id="fixedAsset">
                                0
                                <span>Dr</span>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="mis-col-four">
		<div class="mis-box">
                    <div class="box-title">Loans</div>
                    <div class="items">
                        <div class="item">
                            <div class="box-content">
                                Loans & Liability
                                <span>:</span>
                            </div>
                            <div class="numbers" id="liability">
                                0
                                <span>Dr</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content">
                                Advances, Deposit and pre-payments
                                <span>:</span>
                            </div>
                            <div class="numbers" id="advances">
                                0
                                <span>Dr</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mis-box">
                    <div class="box-title">Profit & Loss</div>
                    <div class="items">
                        <div class="item">
                            <div class="box-content">
                                <span id="profit_loss_title">Gross Profit</span>
                                <span>:</span>
                            </div>
                            <div class="numbers" id="grosss_profit">
                                0
                                <span>Dr</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content">
                                <span id="net_profit_title">Net Profit</span>
                                <span>:</span>
                            </div>
                            <div class="numbers" id="net_profit">
                                0
                                <span>Dr</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mis-box">
                    <div class="box-title">Sales & Collection</div>
                    <div class="items">
                        <div class="item">
                            <div class="box-content">
                                Total Sales
                                <span>:</span>
                            </div>
                            <div class="numbers" id="total_sales_amount">
                                0
                                <span>Dr</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content">
                                Total Purchase
                                <span>:</span>
                            </div>
                            <div class="numbers" id="total_receipt_amount">
                                0
                                <span>Dr</span>
                            </div>
                        </div>
                    </div>
                </div>
		<div class="mis-box">
                    <div class="box-title">Over-Due Invoice</div>
                    <div class="items">
                        <div class="item">
                            <div class="box-content">
                                Sales Invoice
                                <span>:</span>
                            </div>
                            <div class="numbers">
                                0
                                <span>Dr</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content">
                                Purchase Invoice
                                <span>:</span>
                            </div>
                            <div class="numbers">
                                0
                                <span>Dr</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mis-col-two">
                <div class="mis-box">
                    <div class="box-title">Top 5 Payable</div>
                    <div class="items">
                        <div class="item">
                            <div class="box-content">
                                Gulshan Footwear
                                <span>:</span>
                            </div>
                            <div class="numbers">0</div>
                        </div>
                        <div class="item">
                            <div class="box-content">
                                New Maharaja Footwear
                                <span>:</span>
                            </div>
                            <div class="numbers">0</div>
                        </div>
                        <div class="item">
                            <div class="box-content">
                                Anand Footwear
                                <span>:</span>
                            </div>
                            <div class="numbers">0</div>
                        </div>
                        <div class="item">
                            <div class="box-content">
                                Nice Shoe Company
                                <span>:</span>
                            </div>
                            <div class="numbers">0</div>
                        </div>
                        <div class="item">
                            <div class="box-content">
                                Ayush Industries
                                <span>:</span>
                            </div>
                            <div class="numbers">0</div>
                        </div>
                    </div>
                </div>
		<div class="mis-box">
                    <div class="box-title">Fast Moving Items</div>
                    <div class="items">
                        <div class="item">
                            <div class="box-content-item">
                                Pvc Foot Wear
                                <span>:</span>
                            </div>
                            <div class="numbers-item">
                                0
                                <span>Pcs</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content-item">
                                Footwear Undear 500
                                <span>:</span>
                            </div>
                            <div class="numbers-item">
                                0
                                <span>Pcs</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content-item">
                                COLOURS LADIES CHAPPAL'
                                <span>:</span>
                            </div>
                            <div class="numbers-item">
                                0
                                <span>Pcs</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content-item">
                                TENNIS 6-9
                                <span>:</span>
                            </div>
                            <div class="numbers-item">
                                0
                                <span>Pcs</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content-item">
                                TITAS 123
                                <span>:</span>
                            </div>
                            <div class="numbers-item">
                                0
                                <span>Pcs</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mis-col-two">
                <div class="mis-box">
                    <div class="box-title">Slow Moving Items</div>
                    <div class="items">
                        <div class="item">
                            <div class="box-content-item">
                                780043-ART.G-0075-MIX-6X10
                                <span>:</span>
                            </div>
                            <div class="numbers-item">
                                0
                                <span>Pcs</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content-item">
                                780049-ART.G-0075-MIX-6X10
                                <span>:</span>
                            </div>
                            <div class="numbers-item">
                                0
                                <span>Pcs</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content-item">
                                780066-ART.G-0075-MIX-6X10
                                <span>:</span>
                            </div>
                            <div class="numbers-item">
                                6
                                <span>Pcs</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content-item">
                                780222-ART.G-0075-MIX-6X10
                                <span>:</span>
                            </div>
                            <div class="numbers-item">
                                0
                                <span>Pcs</span>
                            </div>
                        </div>
                        <div class="item">
                            <div class="box-content-item">
                                Campus 1399
                                <span>:</span>
                            </div>
                            <div class="numbers-item">
                                0
                                <span>Pcs</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mis-box">
                    <div class="box-title">Non Moving Items</div>
                    <div class="items">
                        <div class="item">
                            <div class="box-content-item"></div>
                            <div class="numbers-item"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script language="JavaScript" src="<?=REL_CONTENT_DIR?>/js/component.js" type="module"></script>
<script language="JavaScript" src="<?=REL_CONTENT_DIR?>/js/apexcharts.min.js"></script>

<script>
let black = getComputedStyle(document.body).getPropertyValue("--black");
let white = getComputedStyle(document.body).getPropertyValue("--white");
let light_white = getComputedStyle(document.body).getPropertyValue(
  "--light-white"
);
let gray = "#6b7280";
let light_gray = "#d1d5db";
let blue = "#1A56DB";
let gold = "#f59e0b";

let light_green = "#00E396";
let light_blue = "#008FFB";
let light_gold = "#FEB019";

var columnChart = null;
var salesOrderchart = null;
var profitLossChartHandle = null;
var cashFlowChartHandle = null;

const column_chart = document.getElementById("column-chart");
const radial_receive_chart = document.getElementById("radial-receive-chart");
const radial_paid_chart = document.getElementById("radial-paid-chart");
const profit_loss_chart = document.getElementById("profit-loss-chart");
const cashflow_chart = document.getElementById("cashflow-chart");
const sales_order_chart = document.getElementById("sales-order-chart");

function showAccountSalesCollectionChart(columnChartSeries){
	// column chart start
	const columnChartConf = {
	  chart: {
	    type: "bar",
	    height: 580,
	    fontFamily: "Inter, sans-serif",
	    toolbar: {
	      show: false,
	    },
	    zoom: {
	      enabled: false,
	    },
	    toolbar: {
	      show:true
	    }
	  },
	  colors: [blue, gold],
	  series: columnChartSeries,
	  title: {
	    text: "Sales & Collection",
	    align: "center",
	  },
	  plotOptions: {
	    bar: {
	      horizontal: false,
	      columnWidth: "60%",
	      borderRadiusApplication: "end",
	      borderRadius: 5,
	      dataLabels: {
		position: "top",
	      },
	    },
	  },
	  tooltip: {
	    shared: true,
	    intersect: false,
	    style: {
	      fontFamily: "Inter, sans-serif",
	    },
	  },
	  states: {
	    hover: {
	      filter: {
		type: "darken",
		value: 1,
	      },
	    },
	  },
	  stroke: {
	    show: true,
	    width: 0,
	    colors: ["transparent"],
	  },
	  grid: {
	    show: true,
	    borderColor: gray,
	    strokeDashArray: 0,
	    padding: {
	      left: 20,
	    },
	    position: "back",
	    row: {
	      opacity: 0.5,
	    },
	    column: {
	      opacity: 0.5,
	    },
	    xaxis: {
	      lines: {
		show: false,
	      },
	    },
	    yaxis: {
	      lines: {
		show: true,
		min: 0, 
	  	forceNiceScale: true,
	      },
	    },
	  },
	  dataLabels: {
	    enabled: false,
	    style: {
	      colors: [black],
	    },
	    offsetY: -25,
	  },
	  legend: {
	    show: true,
	    position: "bottom",
	  },
	  xaxis: {
	    floating: false,
	    labels: {
	      show: true,
	      style: {
		fontFamily: "Inter, sans-serif",
		cssClass: "text-xs font-normal fill-black",
	      },
	    },
	    axisBorder: {
	      show: false,
	    },
	    axisTicks: {
	      show: false,
	    },
	  },
	  yaxis: {
	    show: true,
	  },
	  fill: {
	    opacity: 1,
	  },
	};

	if (column_chart && typeof ApexCharts !== "undefined") {
	  //const chart = new ApexCharts(column_chart, columnChartConf);
	  //chart.render();

	  if (columnChart === null) {
	    columnChart = new ApexCharts(column_chart, columnChartConf);
	    columnChart.render();
	  } else {
	    columnChart.updateSeries(columnChartSeries);
	  }

	}

 	

}


var column_chart_series =[
    {
      name: "Order",
      color: blue,
      data: [
        { x: "Team 1", y: 31 },
        { x: "Team 2", y: 22 },
        { x: "Team 3", y: 63 },
        { x: "Team 4", y: 21 },
        { x: "Team 5", y: 50 },
      ],
    },
    {
      name: "Target",
      color: gold,
      data: [
        { x: "Team 1", y: 68 },
        { x: "Team 2", y: 13 },
        { x: "Team 3", y: 41 },
        { x: "Team 4", y: 24 },
        { x: "Team 5", y: 22 },
      ],
    },
    {
      name: "Sales",
      color: light_green,
      data: [
        { x: "Team 1", y: 31 },
        { x: "Team 2", y: 22 },
        { x: "Team 3", y: 63 },
        { x: "Team 4", y: 21 },
        { x: "Team 5", y: 50 },
      ],
    },
    {
      name: "Collection",
      color: light_blue,
      data: [
        { x: "Team 1", y: 68 },
        { x: "Team 2", y: 13 },
        { x: "Team 3", y: 41 },
        { x: "Team 4", y: 24 },
        { x: "Team 5", y: 22 },
      ],
    },
  ];

//showAccountSalesCollectionChart(column_chart_series);


function showSalesOrderChart(salesOrderChartSeries){
	// column chart start
	const salesOrderChartConf = {
	  chart: {
	    type: "bar",
	    height: 580,
	    fontFamily: "Inter, sans-serif",
	    toolbar: {
	      show: false,
	    },
	    zoom: {
	      enabled: false,
	    },
	    toolbar: {
	      show:true
	    }
	  },
	  colors: [blue, gold],
	  series: salesOrderChartSeries,
	  title: {
	    text: "Cash Flow 2",
	    align: "center",
	  },
	  plotOptions: {
	    bar: {
	      horizontal: false,
	      columnWidth: "60%",
	      borderRadiusApplication: "end",
	      borderRadius: 5,
	      dataLabels: {
		position: "top",
	      },
	    },
	  },
	  tooltip: {
	    shared: true,
	    intersect: false,
	    style: {
	      fontFamily: "Inter, sans-serif",
	    },
	  },
	  states: {
	    hover: {
	      filter: {
		type: "darken",
		value: 1,
	      },
	    },
	  },
	  stroke: {
	    show: true,
	    width: 0,
	    colors: ["transparent"],
	  },
	  grid: {
	    show: true,
	    borderColor: gray,
	    strokeDashArray: 0,
	    padding: {
	      left: 20,
	    },
	    position: "back",
	    row: {
	      opacity: 0.5,
	    },
	    column: {
	      opacity: 0.5,
	    },
	    xaxis: {
	      lines: {
		show: false,
	      },
	    },
	    yaxis: {
	      lines: {
		show: true,
		min: 0, 
	  	forceNiceScale: true,
	      },
	    },
	  },
	  dataLabels: {
	    enabled: true,
	    style: {
	      colors: [black],
	    },
	    offsetY: -25,
	  },
	  legend: {
	    show: true,
	    position: "bottom",
	  },
	  xaxis: {
	    floating: false,
	    labels: {
	      show: true,
	      style: {
		fontFamily: "Inter, sans-serif",
		cssClass: "text-xs font-normal fill-black",
	      },
	    },
	    axisBorder: {
	      show: false,
	    },
	    axisTicks: {
	      show: false,
	    },
	  },
	  yaxis: {
	    show: true,
	  },
	  fill: {
	    opacity: 1,
	  },
	};

	if (sales_order_chart && typeof ApexCharts !== "undefined") {
	  if (salesOrderchart === null) {
	    salesOrderchart = new ApexCharts(sales_order_chart, salesOrderChartConf);
	    salesOrderchart.render();
	  } else {
	    salesOrderchart.updateSeries(salesOrderChartSeries);
	  }
	}
}


var sales_order_chart_series =[
    {
      name: "SL",
      color: blue,
      data: [
        { x: "Team 1", y: 31 },
        { x: "Team 2", y: 22 },
        { x: "Team 3", y: 63 },
        { x: "Team 4", y: 21 },
        { x: "Team 5", y: 50 },
      ],
    },
    {
      name: "OL",
      color: gold,
      data: [
        { x: "Team 1", y: 68 },
        { x: "Team 2", y: 13 },
        { x: "Team 3", y: 41 },
        { x: "Team 4", y: 24 },
        { x: "Team 5", y: 22 },
      ],
    },
    {
      name: "CL",
      color: light_green,
      data: [
        { x: "Team 1", y: 31 },
        { x: "Team 2", y: 22 },
        { x: "Team 3", y: 63 },
        { x: "Team 4", y: 21 },
        { x: "Team 5", y: 50 },
      ],
    },
    {
      name: "CL",
      color: light_blue,
      data: [
        { x: "Team 1", y: 68 },
        { x: "Team 2", y: 13 },
        { x: "Team 3", y: 41 },
        { x: "Team 4", y: 24 },
        { x: "Team 5", y: 22 },
      ],
    },
  ];

//showSalesOrderChart(sales_order_chart_series);


//Account receivabe and payable chart start here
// radialReceiveBar chart start
const radialReceiveBarConf = {
  chart: {
    height: 200,
    width: "100%",
    type: "radialBar",
  },

  series: [80],
  colors: [blue],
  title: {
    text: "Accounts Receivable",
    align: "center",
  },
  plotOptions: {
    radialBar: {
      hollow: {
        margin: 0,
        size: "42%",
        background: "transparent",
      },
      track: {
        dropShadow: {
          enabled: true,
          top: 2,
          left: 0,
          blur: 4,
          opacity: 0.15,
        },
      },
      dataLabels: {
        value: {
          offsetY: -15,
          color: blue,
          fontSize: "30px",
          fontWeight: "bold",
          show: true,
        },
        name: {
          offsetY: 20,
          color: black,
          fontSize: "15px",
          fontWeight: 400,
        },
      },
    },
  },
  grid: {
    show: false,
    strokeDashArray: 0,
    position: "back",
    padding: {
      left: -20,
      right: -20,
      top: -30,
      bottom: -30,
    },
  },
  fill: {
    type: "solid",
  },
  stroke: {
    lineCap: "",
  },
  labels: ["Received"],
};

if (radial_receive_chart && typeof ApexCharts !== "undefined") {
  
	  //if (radialReceiveChart === null) {
	    const radialReceiveChart = new ApexCharts(radial_receive_chart, radialReceiveBarConf);
	    radialReceiveChart.render();
	  //} else {
	    //radialReceiveChart.updateSeries();
	  //}
}
// radialReceiveBar chart end
// radialPaidBar chart start
const radialPaidBarConf = {
  chart: {
    height: 200,
    width: "100%",
    type: "radialBar",
  },
  series: [85],
  colors: [blue],
  title: {
    text: "Accounts Payable",
    align: "center",
  },
  plotOptions: {
    radialBar: {
      hollow: {
        margin: 0,
        size: "42%",
        background: "transparent",
      },
      track: {
        dropShadow: {
          enabled: true,
          top: 2,
          left: 0,
          blur: 4,
          opacity: 0.15,
        },
      },
      dataLabels: {
        value: {
          offsetY: -15,
          color: blue,
          fontSize: "30px",
          fontWeight: "bold",
          show: true,
        },
        name: {
          offsetY: 20,
          color: black,
          fontSize: "15px",
          fontWeight: 400,
        },
      },
    },
  },
  grid: {
    show: false,
    strokeDashArray: 0,
    position: "back",
    padding: {
      left: -20,
      right: -20,
      top: -30,
      bottom: -30,
    },
  },
  fill: {
    type: "solid",
  },
  stroke: {
    lineCap: "",
  },
  labels: ["Paid"],
};

if (radial_paid_chart && typeof ApexCharts !== "undefined") {
  const chart = new ApexCharts(radial_paid_chart, radialPaidBarConf);
  chart.render();
}

// Account receivabe and payable chart end here


function profitLossChart(categories,profitValue,lossValue){
	// profit/loss chart start

	var profitLossBarConf = {
	  chart: {
	    type: "bar",
	    height: 280,
	    zoom: {
	      enabled: false,
	    },
	    stacked: true,
	  },
	  series: [
	    {
	      name: "Profit",
	      data: profitValue,
	    },
	    {
	      name: "Loss",
	      data: lossValue,
	    },
	  ],
	  title: {
	    text: "Profit/Loss",
	    align: "center",
	  },
	  legend: {
	    show: true,
	    position: "bottom",
	    markers: {
	      size: 7,
	      strokeWidth: 1,
	      fillColors: ["#00E396", "#FF4560"],
	    },
	  },
	  xaxis: {
	    labels: {
	      rotate: -45
	    },
	    categories: categories,
	  },

	  plotOptions: {
	    bar: {
	      horizontal: false,
	      columnWidth: "60%",
	      borderRadiusApplication: "end",
	      borderRadiusWhenStacked: "all",
	      borderRadius: 5,
	    },
	  },
	  dataLabels: {
	    enabled: false,
	  },
	  yaxis: {
	    labels: {
	      formatter: function (val) {
		//return "" + Number(val).toFixed(0);
		return Number(val).toLocaleString();
	      },
	    },
	  },
	  colors: ["#00E396", "#FF4560"], 
	  grid: {
	    borderColor: gray,
	  },
	};

	if (profit_loss_chart && typeof ApexCharts !== "undefined") {
	  
		if (profitLossChartHandle === null) {
		    profitLossChartHandle = new ApexCharts(profit_loss_chart, profitLossBarConf);
  		    profitLossChartHandle.render();
		  } else {
		    // Update data
			profitLossChartHandle.updateSeries([
			    { name: "Profit", data: profitValue },
			    { name: "Loss", data: lossValue }
			]);

			// Update categories
			profitLossChartHandle.updateOptions({
			    xaxis: { categories: categories }
			});

		  }
	}


}

const categories = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
const profit =  [1000, 0, 2000, 1500, 2000, 1000, 0, 2900, 1000, 0, 0, 1500];
const loss =  [0, -1000, 0, 0, 0, 0, -500, 0, 0, -2500, -2000, 0];

//profitLossChart(categories,profit,loss);

function cashFlowChart(){
	//cashflow chart start
	var cashFlowColumnLineConf = {
	  chart: {
	    type: "line",
	    height: 280,
	    zoom: {
	      enabled: false,
	    },
	    stacked: false,
	  },
	  series: [
	    {
	      name: "Cash Going In",
	      type: "column",
	      data: [
		0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
	      ],
	    },
	    {
	      name: "Cash Going Out",
	      type: "column",
	      data: [
		 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
	      ],
	    },
	    {
	      name: "End Cash On Hand",
	      type: "line",
	      data: [
		 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
	      ],
	    },
	  ],
	  xaxis: {
	    categories: [
	      "Jan",
	      "Feb",
	      "Mar",
	      "Apr",
	      "May",
	      "Jun",
	      "Jul",
	      "Aug",
	      "Sep",
	      "Oct",
	      "Nov",
	      "Dec",
	    ],
	  },
	  yaxis: {
	    // title: {
	    //   text: "Amount",
	    // },
	    labels: {
	      formatter: function (val) {
		return Number(val).toFixed(0);
	      },
	    },
	  },
	  title: {
	    text: "Cash Flow",
	    align: "center",
	  },
	  legend: {
	    show: true,
	    position: "bottom",
	    horizontalAlign: "center",
	    markers: {
	      size: 7,
	      shape: "square",
	      strokeWidth: 1,
	    },
	  },
	  colors: ["#00E396", "#008FFB", "#FEB019"],
	  stroke: {
	    width: [0, 0, 3],
	    // curve: "smooth",
	  },
	  dataLabels: {
	    enabled: false,
	  },
	  plotOptions: {
	    bar: {
	      horizontal: false,
	      columnWidth: "60%",
	      borderRadiusApplication: "end",
	      borderRadiusWhenStacked: "all",
	      borderRadius: 5,
	    },
	  },
	  grid: {
	    borderColor: gray,
	  },
	  markers: {
	    size: 4,
	    hover: {
	      sizeOffset: 3
	    }
	}
	};
	if (cashflow_chart && typeof ApexCharts !== "undefined") {
	  if (cashFlowChartHandle === null) {
	    cashFlowChartHandle = new ApexCharts(cashflow_chart, cashFlowColumnLineConf);
	    cashFlowChartHandle.render();
	  } else {
	    cashFlowChartHandle.updateSeries();
	  }	
	}


}

cashFlowChart();

selectDate('month'); 


function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}


function selectDate(selectType) {
    let from_date = "";
    let to_date = "";
    const now = new Date(); // always a fresh date object


    if (selectType === "today") {
        from_date = to_date = formatDate(now);

    } else if (selectType === "week") {
        // Week starts on Saturday
        const currentDay = now.getDay(); // 0=Sunday, 6=Saturday
        const daysSinceSaturday = (currentDay + 1) % 7;

        const startOfWeek = new Date(now);
        startOfWeek.setDate(now.getDate() - daysSinceSaturday);

        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);

        from_date = formatDate(startOfWeek);
        to_date = formatDate(endOfWeek);

    } else if (selectType === "month") {
        const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
        const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        from_date = formatDate(startOfMonth);
        to_date = formatDate(endOfMonth);

    } else if (selectType === "year") {
        const startOfYear = new Date(now.getFullYear(), 0, 1);
        const endOfYear = new Date(now.getFullYear(), 11, 31);
        from_date = formatDate(startOfYear);
        to_date = formatDate(endOfYear);

    } else if (selectType === "from_date") {
        from_date = document.getElementById("from_date").value;
        return;

    } else if (selectType === "to_date") {
        to_date = document.getElementById("to_date").value;
        return;
    }

    document.getElementById("from_date").value = from_date;
    document.getElementById("to_date").value = to_date;

    //getAllReport(from_date, to_date);
}

function selectDaterange(){
    var from_date = document.getElementById("from_date").value;
    var to_date = document.getElementById("to_date").value;
    getAllReport(from_date, to_date);
}


function getAllReport(from_date, to_date){
	accountsSalesAndCollectionChart(from_date, to_date);
	cashAndBank(from_date, to_date);
	accountsReceiveAndPayable(from_date, to_date);
	//cashFlow(from_date, to_date);
	capitalAndFixedAssets(from_date, to_date);
	loans(from_date, to_date);
	profitAndLoss(from_date, to_date);
	//overDueInvoice(from_date, to_date);
}


function accountsSalesAndCollectionChart(from_date, to_date){
	const formData = {
		"from_date" : from_date,
		"to_date" : to_date,
		"report" : "sales_and_collection",
	}
	
	axios.post("?app=sales.report&cmd=mis_dashboard_report", formData).then(function (response) {
                if (response.data.status == true) {
const divsionTargetAmount = response.data.data.divsionTargetAmount;
const divsionOrderAmount = response.data.data.divsionOrderAmount;
const divsionSalesAmount = response.data.data.divsionSalesAmount;
const divsionReceiptAmount = response.data.data.divsionReceiptAmount;

const totalDivsionSalesAmount = response.data.data.totalDivsionSalesAmount;
const totalDivsionReceiptAmount = response.data.data.totalDivsionReceiptAmount;
$("#total_sales_amount").html(totalDivsionSalesAmount);
$("#total_receipt_amount").html(totalDivsionReceiptAmount);

const divsions = Object.values(response.data.data.divsions);

if(divsions){

	orderData = divsions.map((divsion) => {
		const id = String(divsion.division_id);
	  	const yVal = parseFloat(divsionOrderAmount[id]) || 0;
		return { x: divsion.division_name_eng, y: yVal }
	})

	targetData = divsions.map((divsion) => {
		const id = String(divsion.division_id);
	  	const yVal = parseFloat(divsionTargetAmount[id]) || 0;
		return { x: divsion.division_name_eng, y: yVal }
	})

	salesData = divsions.map((divsion) => {
		const id = String(divsion.division_id);
	  	const yVal = parseFloat(divsionSalesAmount[id]) || 0;
		return { x: divsion.division_name_eng, y: yVal }
	})

	collectionData = divsions.map((divsion) => {
		const id = String(divsion.division_id);
	  	const yVal = parseFloat(divsionReceiptAmount[id]) || 0;
		return { x: divsion.division_name_eng, y: yVal }
	})

	column_chart_series =[
	    {
	      name: "Order",
	      color: blue,
	      data: orderData,
	    },
	    {
	      name: "Target",
	      color: gold,
	      data: targetData,
	    },
	    {
	      name: "Sales",
	      color: light_green,
	      data: salesData,
	    },
	    {
	      name: "Collection",
	      color: light_blue,
	      data: collectionData,
	    },
	  ];
	showAccountSalesCollectionChart(column_chart_series);

}
                } else {
                    console.log("error: ",response.data.message);
                }
            })
}

function cashAndBank(from_date, to_date){
	const formData = {
		"from_date" : from_date,
		"to_date" : to_date,
		"report" : "cash_and_bank",
	}
	
	axios.post("?app=sales.report&cmd=mis_dashboard_report", formData).then(function (response) {
                if (response.data.status == true) {
			const data = response.data.data;
			$("#cash_in_hand").html(data.cash_in_hand);
			$("#cash_at_bank").html(data.cash_at_bank);
			$("#cash_in_hand_and_bank_total").html(data.total);
		} else {
                    //console.log("error: ",response.data.message);
                }
	})
}

function accountsReceiveAndPayable(from_date, to_date){
	const formData = {
		"from_date" : from_date,
		"to_date" : to_date,
		"report" : "receivable_and_payable",
	}
	
	axios.post("?app=sales.report&cmd=mis_dashboard_report", formData).then(function (response) {
                if (response.data.status == true) {
			const data = response.data.data;
			$("#account_receivables").html(data.receivable);
			$("#account_payables").html(data.payable);
			$("#account_total").html(data.total);
		} else {
                    //console.log("error: ",response.data.message);
                }
	})
}

function cashFlow(from_date, to_date){
	//console.log("cashFlow: ",from_date, to_date);
}


function capitalAndFixedAssets(from_date, to_date){
	const formData = {
		"from_date" : from_date,
		"to_date" : to_date,
		"report" : "capital_and_fixedAssets",
	}
	
	axios.post("?app=sales.report&cmd=mis_dashboard_report", formData).then(function (response) {
                if (response.data.status == true) {
			const data = response.data.data;
			$("#capital").html(data.capital);
			$("#fixedAsset").html(data.fixedAsset);
		} else {
                    //console.log("error: ",response.data.message);
                }
	})
}


function loans(from_date, to_date){
	const formData = {
		"from_date" : from_date,
		"to_date" : to_date,
		"report" : "loan",
	}
	
	axios.post("?app=sales.report&cmd=mis_dashboard_report", formData).then(function (response) {
                if (response.data.status == true) {
			const data = response.data.data;
			$("#liability").html(data.liability);
			$("#advances").html(data.advances);
		} else {
                    //console.log("error: ",response.data.message);
                }
	})
}

function profitAndLoss(from_date, to_date){
	const formData = {
		"from_date" : from_date,
		"to_date" : to_date,
		"report" : "profit_and_loss",
	}
	
	axios.post("?app=sales.report&cmd=mis_dashboard_report", formData).then(function (response) {
                if (response.data.status == true) {
			const data = response.data.data.grosss_profit;
			$("#profit_loss_title").html(data.profitLossTitle);
			$("#grosss_profit").html(data.grosss_profit);
			$("#net_profit").html(data.net_profit);
			$("#net_profit_title").html(data.net_profit_title);

			const categories = response.data.data.chartData.labels;
			const profit = [];
			const loss = [];

			response.data.data.chartData.values.forEach(val => {
			    // Remove commas and convert to float
			    const num = parseFloat(String(val).replace(/,/g, ''));

			    if (num >= 0) {
				profit.push(num);
				loss.push(0);
			    } else {
				profit.push(0);
				loss.push(num);
			    }
			});
//console.log(categories, profit, loss);

			// Now you can pass to your chart function
			profitLossChart(categories, profit, loss);
		} else {
                    //console.log("error: ",response.data.message);
                }
	})
}

function overDueInvoice(from_date, to_date){
	console.log("overDueInvoice: ",from_date, to_date);
}




</script>




