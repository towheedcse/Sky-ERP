class ControlPanel extends HTMLElement {
    constructor() {
      super();
      this.innerHTML = `
<div class="table-control-panel">
                <div class="entries-control">
                <span>Show</span>
                  <select class="entries-select" id="entries">
                    <option value="5" selected>5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                <span>entries</span>
                </div>
    <label class="search-box">
      <input type="search" class="grow" id="search" placeholder="Search" />
      <i class="fa-solid fa-magnifying-glass"></i>
    </label>
</div>
  `;
    }
  }
  
  customElements.define("control-panel", ControlPanel);
  