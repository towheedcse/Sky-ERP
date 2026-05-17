function getTemplet(path) {
    const template = document.createElement("template");
    template.innerHTML = `
  <div class="flex">
      <nav class="sidebar" id="sidebar">
        <button id="sidebar-toggle" class="sidebar-toggle">
          <i class="fa-solid fa-bars"></i>
        </button>
        <h1 class="sidebar-logo">EPR</h1>
        <a href="${path}index.html" class="">
        <div class="inline-flex items-center mt-5">
        <img src="${path}assets/avatar.png" alt="avatar" class="sidebar-avatar"/>
        <div class="sidebar-user">Admin Name</div>
        </div>
        </a>
        <ul class="nav-items">
           <!-- Human Resources start -->
          <li class="mb-1 dd-toggle">
            <button class="sidebar-item">
              <div class="sidebar-icon">
                <i class="fa-solid fa-users"></i>
              </div>
              <div class="sidebar-item-text">
                <div>Human Resources</div>
                <div class="dd-icon">
                  <i class="fa-solid fa-chevron-right"></i>
                </div>
              </div>
            </button>
            <ul class="dd-item">
              <li>
                <a href="${path}templets/human-resources/employee.html" class="dd-link">
                  <i class="fa-regular fa-circle"></i>
                 Employee
                </a>
              </li>
              <li>
                <a href="${path}templets/human-resources/manage-holidays.html" class="dd-link">
                  <i class="fa-regular fa-circle"></i>
                 Manage Holidays
                </a>
              </li>
              <li>
                <a href="${path}templets/human-resources/stuff-attendance.html" class="dd-link">
                  <i class="fa-regular fa-circle"></i>
                 Stuff Attendance
                </a>
              </li>
              <li>
                <a href="${path}templets/human-resources/out-of-station-duty.html" class="dd-link">
                  <i class="fa-regular fa-circle"></i>
                 Out Of Station Duty
                </a>
              </li>
              <li>
                <a href="${path}templets/human-resources/attendance-data-load.html" class="dd-link">
                  <i class="fa-regular fa-circle"></i>
                 Attendance Data Load
                </a>
              </li>
              <li>
                <a href="${path}templets/human-resources/monthly-attendance.html" class="dd-link">
                  <i class="fa-regular fa-circle"></i>
                 Monthly Attendance
                </a>
              </li>
            </ul>
          </li>
          <!-- Human Resources end -->
           <!-- Party Manage start -->
          <li class="mb-1 dd-toggle">
            <button class="sidebar-item">
              <div class="sidebar-icon">
                <i class="fas fa-campground"></i>
              </div>
              <div class="sidebar-item-text">
                <div>Party Manage</div>
                <div class="dd-icon">
                  <i class="fa-solid fa-chevron-right"></i>
                </div>
              </div>
            </button>
            <ul class="dd-item">
              <li>
                <a href="${path}templets/party-manage/customer.html" class="dd-link">
                  <i class="fa-regular fa-circle"></i>
                 Customer
                </a>
              </li>
              <li>
                <a href="${path}templets/party-manage/distributor.html" class="dd-link">
                  <i class="fa-regular fa-circle"></i>
                 Distributor
                </a>
              </li>
              <li>
                <a href="${path}templets/party-manage/importer.html" class="dd-link">
                  <i class="fa-regular fa-circle"></i>
                 Importer
                </a>
              </li>
            </ul>
          </li>
          <!-- Party Manage end -->
           <!-- Permission start -->
          <li class="mb-1 dd-toggle">
            <button class="sidebar-item">
              <div class="sidebar-icon">
                <i class="fa-solid fa-key"></i>
              </div>
              <div class="sidebar-item-text">
                <div>Permission</div>
                <div class="dd-icon">
                  <i class="fa-solid fa-chevron-right"></i>
                </div>
              </div>
            </button>
            <ul class="dd-item">
              <li>
                <a href="${path}templets/permission/module-permission.html" class="dd-link">
                  <i class="fa-regular fa-circle"></i>
                 Module Permission
                </a>
              </li>
              <li>
                <a href="${path}templets/permission/menu-permission.html" class="dd-link">
                  <i class="fa-regular fa-circle"></i>
                 Menu Permission
                </a>
              </li>
              <li>
                <a href="${path}templets/permission/option-permission.html" class="dd-link">
                  <i class="fa-regular fa-circle"></i>
                 Option Permission
                </a>
              </li>
            </ul>
          </li>
          <!-- Permission end -->
          <li class="mb-1 dd-toggle">
            <button class="sidebar-item">
              <div class="sidebar-icon">
                <i class="fa-solid fa-briefcase"></i>
              </div>
              <div class="sidebar-item-text">
                <div>Accounts</div>
                <div class="dd-icon">
                  <i class="fa-solid fa-chevron-right"></i>
                </div>
              </div>
            </button>
            <ul class="dd-item">
              <li>
                <a href="${path}templets/accounts/sl-level-1.html" class="dd-link">
                  <i class="fa-regular fa-circle"></i>
                 SL Level-1
                </a>
              </li>
              <li>
                <a href="${path}templets/accounts/sl-level-2.html" class="dd-link">
                  <i class="fa-regular fa-circle"></i>
                 SL Level-2
                </a>
              </li>
              <li>
                <a href="${path}templets/accounts/sl-level-3.html" class="dd-link">
                  <i class="fa-regular fa-circle"></i>
                 SL Level-3
                </a>
              </li>
              <li>
                <a href="${path}templets/accounts/chart-of-accounts.html" class="dd-link">
                  <i class="fa-regular fa-circle"></i>
                 Chart of Accounts
                </a>
              </li>
            </ul>
          </li>
          <!-- Accounts end -->
        <!-- Settings start -->
          <li class="mb-1 dd-toggle">
            <button class="sidebar-item">
              <div class="sidebar-icon">
                <i class="fa-solid fa-gear"></i>
              </div>
              <div class="sidebar-item-text">
                <div>Settings</div>
                <div class="dd-icon">
                  <i class="fa-solid fa-chevron-right"></i>
                </div>
              </div>
            </button>
            <ul class="dd-item">
              <li>
                <a href="${path}templets/settings/organization.html" class="dd-link">
                  <i class="fa-brands fa-diaspora"></i>
                  Organization
                </a>
              </li>
              <li>
                <a href="${path}templets/settings/branch.html" class="dd-link">
                  <i class="fa-solid fa-code-branch"></i>
                  Branch
                </a>
              </li>
              <li>
                <a href="${path}templets/settings/project.html" class="dd-link">
                  <i class="fa-solid fa-sheet-plastic"></i>
                  Project
                </a>
              </li>
              <li>
                <a href="${path}templets/settings/department.html" class="dd-link">
                  <i class="fa-brands fa-codepen"></i>
                  Department
                </a>
              </li>
              <li>
                <a href="${path}templets/settings/section.html" class="dd-link">
                  <i class="fa-solid fa-sign-hanging"></i>
                  Section
                </a>
              </li>
              <li>
                <a href="${path}templets/settings/area.html" class="dd-link">
                  <i class="fa-solid fa-vector-square"></i>
                  Area
                </a>
              </li>
              <li>
                <a href="${path}templets/settings/division.html" class="dd-link">
                  <i class="fa-solid fa-map-location-dot"></i>
                  Division
                </a>
              </li>
              <li>
                <a href="${path}templets/settings/district.html" class="dd-link">
                  <i class="fa-solid fa-map"></i>
                  District
                </a>
              </li>
              <li>
                <a href="${path}templets/settings/territory-area.html" class="dd-link">
                  <i class="fa-solid fa-circle-radiation"></i>
                  Territory Area
                </a>
              </li>
              <li>
                <a href="${path}templets/settings/session.html" class="dd-link">
                  <i class="fa-solid fa-table-cells-large"></i>
                  Session
                </a>
              </li>
              <li>
                <a href="${path}templets/settings/shift.html" class="dd-link">
                  <i class="fa-regular fa-hand"></i>
                  Shift
                </a>
              </li>
              <li>
                <a href="${path}templets/settings/module.html" class="dd-link">
                  <i class="fa-solid fa-screwdriver"></i>
                  Module
                </a>
              </li>
              <li>
                <a href="${path}templets/settings/menu.html" class="dd-link">
                  <i class="fa-solid fa-bars"></i>
                  Menu
                </a>
              </li>
              <li>
                <a href="${path}templets/settings/option.html" class="dd-link">
                  <i class="fa-solid fa-border-top-left"></i>
                  Option
                </a>
              </li>
            </ul>
          </li>
          <!-- Settings end -->

          <!-- Form start-->
          <li class="mb-1 dd-toggle">
            <button class="sidebar-item">
              <div class="sidebar-icon">
                <i class="fa-brands fa-wpforms"></i>
              </div>
              <div class="sidebar-item-text">
                <div>Forms</div>
                <div class="dd-icon">
                  <i class="fa-solid fa-chevron-right"></i>
                </div>
              </div>
            </button>
            <ul class="dd-item">
              <li>
                <a href="${path}templets/forms/accountLedger.html" class="dd-link">
                  <i class="fa-solid fa-bag-shopping"></i>
                  Account Ledger
                </a>
              </li>
              <li>
                <a href="${path}templets/forms/statementOfPLI.html" class="dd-link">
                  <i class="fa-solid fa-bag-shopping"></i>
                  Statement Of P/L
                </a>
              </li>
              <li>
                <a href="${path}templets/forms/balanceSheet.html" class="dd-link">
                  <i class="fa-solid fa-bag-shopping"></i>
                  Balance Sheet
                </a>
              </li>
              <li>
                <a href="${path}templets/forms/cashBook.html" class="dd-link">
                  <i class="fa-solid fa-bag-shopping"></i>
                  Cash Book
                </a>
              </li>
              <li>
                <a href="${path}templets/forms/accountsReceivableAging.html" class="dd-link">
                  <i class="fa-regular fa-hand-point-up"></i>
                  Account Receivable & Aging
                </a>
              </li>
              <li>
                <a href="${path}templets/forms/bankBook.html" class="dd-link">
                  <i class="fa-solid fa-bag-shopping"></i>
                  Bank Book
                </a>
              </li>
              
              <li>
                <a href="${path}templets/forms/journalVoucher.html" class="dd-link">
                  <i class="fa-solid fa-bag-shopping"></i>
                  Journal Voucher
                </a>
              </li>
              <li>
                <a href="${path}templets/forms/payableList.html" class="dd-link">
                  <i class="fa-solid fa-bag-shopping"></i>
                  Payable List
                </a>
              </li>
              <li>
                <a href="${path}templets/forms/receivableList.html" class="dd-link">
                  <i class="fa-solid fa-bag-shopping"></i>
                  Receivable List
                </a>
              </li>
              <li>
                <a href="${path}templets/forms/transactionList.html" class="dd-link">
                  <i class="fa-solid fa-bag-shopping"></i>
                  Transaction List
                </a>
              </li>
              <li>
                <a href="${path}templets/forms/sundryDebtors.html" class="dd-link">
                  <i class="fa-solid fa-bag-shopping"></i>
                  Sundry Debtors
                </a>
              </li>
              <li>
                <a href="${path}templets/forms/customerInfo.html" class="dd-link">
                  <i class="fa-solid fa-bag-shopping"></i>
                  Customer Information
                </a>
              </li>
              <li>
                <a href="${path}templets/forms/lcOpening.html" class="dd-link">
                  <i class="fa-solid fa-bag-shopping"></i>
                  Lc Opening
                </a>
              </li>
              <li>
                <a href="${path}templets/forms/customerWiseSales.html" class="dd-link">
                  <i class="fa-solid fa-bag-shopping"></i>
                  Customer Wise Sales Return
                </a>
              </li>
              <li>
                <a href="${path}templets/forms/salesTarget.html" class="dd-link">
                  <i class="fa-solid fa-bag-shopping"></i>
                   Sales Target
                </a>
              </li>
              <li>
                <a href="${path}templets/forms/physicalSockVerify.html" class="dd-link">
                  <i class="fa-solid fa-bag-shopping"></i>
                   Physical Sock Verification
                </a>
              </li>
              <li>
                <a href="${path}templets/forms/productLedger.html" class="dd-link">
                  <i class="fa-solid fa-bag-shopping"></i>
                   Product Ledger
                </a>
              </li>
            </ul>
          </li>
          <!-- Form end-->

          <!-- Dashboard start -->
          <li class="mb-1 dd-toggle">
            <button class="sidebar-item">
              <div class="sidebar-icon">
                <i class="fa-solid fa-gauge"></i>
              </div>
              <div class="sidebar-item-text">
                <div>Dashboard</div>
                <div class="dd-icon">
                  <i class="fa-solid fa-chevron-right"></i>
                </div>
              </div>
            </button>
            <ul class="dd-item">
              <li>
                <a href="${path}templets/dashboard/hrDashboard.html" class="dd-link">
                  <i class="fa-solid fa-user-tie"></i>
                  HR Dashboard
                </a>
              </li>
              <li>
                <a
                  href="${path}templets/dashboard/misDashboard.html"
                  class="dd-link"
                >
                  <i class="fa-solid fa-chart-line"></i>
                  MIS Dashboard
                </a>
              </li>
            </ul>
          </li>
          <!-- Dashboard end-->

          <li class="mb-1 dd-toggle">
            <button class="sidebar-item">
              <div class="sidebar-icon">
                <i class="fa-solid fa-cart-arrow-down"></i>
              </div>
              <div class="sidebar-item-text">
                <div>Order</div>
                <div class="dd-icon">
                  <i class="fa-solid fa-chevron-right"></i>
                </div>
              </div>
            </button>
            <ul class="dd-item">
              <li>
                <a href="${path}templets/orders/newOrder.html" class="dd-link">
                  <i class="fa-solid fa-plus"></i>New Order</a
                >
              </li>
              <li>
                <a href="${path}templets/orders/viewOrder.html" class="dd-link">
                  <i class="fa-solid fa-eye"></i>View Order</a
                >
              </li>
              <li>
                <a href="${path}templets/orders/viewStock.html" class="dd-link">
                  <i class="fa-solid fa-mountain"></i>View Stock</a
                >
              </li>
              <li>
                <a
                  href="${path}templets/orders/unapprovedOrders.html"
                  class="dd-link"
                >
                  <i class="fa-solid fa-thumbs-down"></i>Unapproved orders</a
                >
              </li>
            </ul>
          </li>
          <li class="mb-1 dd-toggle">
            <button class="sidebar-item">
              <div class="sidebar-icon">
                <i class="fa-solid fa-hand-point-up"></i>
              </div>
              <div class="sidebar-item-text">
                <div>Requisition</div>
                <div class="dd-icon">
                  <i class="fa-solid fa-chevron-right"></i>
                </div>
              </div>
            </button>
            <ul class="dd-item">
              <li>
                <a
                  href="${path}templets/requisition/purchaseRequisition.html"
                  class="dd-link"
                >
                  <i class="fa-solid fa-bag-shopping"></i>Purchase
                  Requisition</a
                >
              </li>
              <li>
                <a
                  href="${path}templets/requisition/requisitionList.html"
                  class="dd-link"
                >
                  <i class="fa-regular fa-hand-point-up"></i> Requisition
                  List</a
                >
              </li>
            </ul>
          </li>
          <li class="mb-1 dd-toggle">
            <button class="sidebar-item px-2">
              <div class="sidebar-icon">
                <i class="fa-solid fa-ticket"></i>
              </div>
              <div class="sidebar-item-text">
                <div>Voucher</div>
                <div class="dd-icon">
                  <i class="fa-solid fa-chevron-right"></i>
                </div>
              </div>
            </button>
            <ul class="dd-item">
              <li>
                <a href="${path}templets/voucher/createVoucher.html" class="dd-link">
                  <i class="fa-solid fa-plus"></i>Create Voucher</a
                >
              </li>
              <li>
                <a href="${path}templets/voucher/viewVoucher.html" class="dd-link">
                  <i class="fa-solid fa-eye"></i>View Voucher</a
                >
              </li>
            </ul>
          </li>
          <li class="mb-1 dd-toggle">
            <button class="sidebar-item px-2">
              <div class="sidebar-icon">
                <i class="fa-solid fa-fingerprint"></i>
              </div>
              <div class="sidebar-item-text">
                <div>Privacy</div>
                <div class="dd-icon">
                  <i class="fa-solid fa-chevron-right"></i>
                </div>
              </div>
            </button>
            <ul class="dd-item">
              <li>
                <a href="${path}templets/privacy/createUser.html" class="dd-link">
                  <i class="fa-solid fa-plus"></i>Create User</a
                >
              </li>
              <li>
                <a
                  href="${path}templets/privacy/changePassword.html"
                  class="dd-link"
                >
                  <i class="fa-solid fa-key"></i>Change Password</a
                >
              </li>
            </ul>
          </li>
          <!-- Production start -->
          <li class="mb-1 dd-toggle">
            <button class="sidebar-item px-2">
              <div class="sidebar-icon">
                <i class="fa-solid fa-gears"></i>
              </div>
              <div class="sidebar-item-text">
                <div>Production</div>
                <div class="dd-icon">
                  <i class="fa-solid fa-chevron-right"></i>
                </div>
              </div>
            </button>
            <ul class="dd-item">
              <li>
                <a href="${path}templets/production/batch-list.html" class="dd-link">
                  <i class="fa-solid fa-list-ol"></i>Batch List</a
                >
              </li>
              <li>
                <a href="${path}templets/production/batch-setup.html" class="dd-link">
                  <i class="fa-solid fa-hammer"></i>Batch Setup</a
                >
              </li>
              <li>
                <a
                  href="${path}templets/production/batchWiseProduction.html"
                  class="dd-link"
                >
                  <i class="fa-solid fa-swatchbook"></i>Batch Wise Production</a
                >
              </li>
              <li>
                <a
                  href="${path}templets/production/cogsStatement.html"
                  class="dd-link"
                >
                  <i class="fa-solid fa-gear"></i>COGS</a
                >
              </li>
              <li>
                <a
                  href="${path}templets/production/finishProductionList.html"
                  class="dd-link"
                >
                  <i class="fa-solid fa-flag-checkered"></i>Finish Production
                  List</a
                >
              </li>
              <li>
                <a
                  href="${path}templets/production/itemsRecived.html"
                  class="dd-link"
                >
                  <i class="fa-solid fa-satellite-dish"></i>Items Received</a
                >
              </li>
              <li>
                <a
                  href="${path}templets/production/machineSetup.html"
                  class="dd-link"
                >
                  <i class="fa-regular fa-hard-drive"></i>Machine Setup</a
                >
              </li>
              <li>
                <a
                  href="${path}templets/production/productSetup.html"
                  class="dd-link"
                >
                  <i class="fa-solid fa-cart-flatbed-suitcase"></i>Product
                  Setup</a
                >
              </li>
              <li>
                <a
                  href="${path}templets/production/rawMaterials.html"
                  class="dd-link"
                >
                  <i class="fa-solid fa-recycle"></i>Raw Materials</a
                >
              </li>
              <li>
                <a
                  href="${path}templets/production/requireProductionList.html"
                  class="dd-link"
                >
                  <i class="fa-solid fa-asterisk"></i>Require Production List</a
                >
              </li>
              <li>
                <a
                  href="${path}templets/production/searchBatchProductions.html"
                  class="dd-link"
                >
                  <i class="fa-brands fa-searchengin"></i>Search Batch
                  Productions</a
                >
              </li>
              <li>
                <a href="${path}templets/production/stockList.html" class="dd-link">
                  <i class="fa-solid fa-boxes-stacked"></i>Stock List</a
                >
              </li>
            </ul>
          </li>
          <!-- Production end -->
          <li class="mb-1 dd-toggle">
            <button class="sidebar-item px-2">
              <div class="sidebar-icon">
                <i class="fa-solid fa-book"></i>
              </div>
              <div class="sidebar-item-text">
                <div>Reports</div>
                <div class="dd-icon">
                  <i class="fa-solid fa-chevron-right"></i>
                </div>
              </div>
            </button>
            <ul class="dd-item">
              <li>
                <a
                  href="${path}templets/reports/ProductWiseSalesSummary.html"
                  class="dd-link"
                >
                  <i class="fa-solid fa-table-list"></i>Product Wise Sales
                  Summary</a
                >
              </li>
            </ul>
          </li>
          <li class="mb-1">
            <a href="/logout" class="sidebar-item">
              <div class="sidebar-icon">
                <i class="fa-solid fa-right-from-bracket"></i>
              </div>
              <div class="sidebar-item-text">Logout</div>
            </a>
          </li>
        </ul>
        <div class="mt-auto">
          <div class="mb-1 dd-toggle">
            <div class="dd-item-bottom">
              <div class="change-theme-box">
                <button
                  class="theme-color-icon"
                  title="light"
                  onclick="setTheme('light', event)"
                >
                  <div class="bg-[#4A00FF] w-1/2 h-full"></div>
                  <div class="bg-[#fff] w-1/2 h-full"></div>
                </button>
                <button
                  class="theme-color-icon"
                  title="dark"
                  onclick="setTheme('dark', event)"
                >
                  <div class="bg-[#4A00FF] w-1/2 h-full"></div>
                  <div class="bg-[#000] w-1/2 h-full"></div>
                </button>
              </div>
            </div>
            <button class="sidebar-item px-2">
              <div class="sidebar-icon">
                <i class="fa-solid fa-paint-roller"></i>
              </div>
              <div class="sidebar-item-text">
                <div>Change Theme</div>
                <div class="dd-icon-bottom">
                  <i class="fa-solid fa-chevron-right"></i>
                </div>
              </div>
            </button>
          </div>
        </div>
      </nav>
      <slot id="slot"></slot>
    </div>  
  `;
    return template;
}

export class MainLayout extends HTMLElement {
    constructor() {
        super();
        const content = this.innerHTML;
        this.innerHTML = "";
        const template = getTemplet(this.getAttribute("path") || "./");
        const slot = template.content.getElementById("slot");
        slot.innerHTML = content;
        this.appendChild(template.content.cloneNode(true));
    }
}

customElements.define("main-layout", MainLayout);