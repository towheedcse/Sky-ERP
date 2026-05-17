class PaginationPanel extends HTMLElement {
  constructor() {
    super();
    this.innerHTML = `
<div class="pagination-container">
<div class="entries-info" id="page-details">Show 1 to 10 of 100 entries</div>
  <div class="pagination-controls" id="pagination">
                  <button class="text-xs join-item btn btn-sm sm:text-sm">«</button>
                  <button class="text-xs join-item btn btn-sm sm:text-sm btn-active">
                    1
                  </button>
                  <button class="text-xs join-item btn btn-sm sm:text-sm">2</button>
                  <button class="text-xs join-item btn btn-sm sm:text-sm btn-disabled">...</button>
                  <button class="text-xs join-item btn btn-sm sm:text-sm">99</button>
                  <button class="text-xs join-item btn btn-sm sm:text-sm">100</button>
                  <button class="text-xs join-item btn btn-sm sm:text-sm">»</button>
  </div>
</div>
`;
  }
}

customElements.define("pagination-panel", PaginationPanel);
