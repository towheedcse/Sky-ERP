const template = document.createElement("template");
template.innerHTML = `
<div part="hidden-template-container">
    <div part="heading">
        <!-- Header -->
            <h2 class="">Branch</h2>
            <button part="hide-btn-style" id="hide-btn">&minus;
            </button>
    </div>
        <!-- Section to hide -->
        <div class="" id="section-to-hide">
            <slot name="hidden-section" />
        </div>
</div>
    `;

class SectionHider extends HTMLElement {
  constructor() {
    super();

    this.showSection = false;

    this.attachShadow({ mode: "open" });
    this.shadowRoot.appendChild(template.content.cloneNode(true));
    this.shadowRoot.querySelector("h2").innerText = this.getAttribute("title");
    // const hiddenSection = this.shadowRoot.getElementById("section-to-hide");
    // hiddenSection.style.display = "none";
    // const toggleBtn = this.shadowRoot.querySelector('#hide-btn');
    // toggleBtn.getAttribute("class", "");
  }

  toggleInfo() {
    this.showSection = !this.showSection;

    const hiddenSection = this.shadowRoot.getElementById("section-to-hide");
    if (this.showSection) {
      hiddenSection.setAttribute("aria-expanded", "false");
      hiddenSection.style.display = "none";
      // hiddenSection.classList.add = "hidden";
      // hiddenSection.style.transform = "translateY(-120%)";
      // hiddenSection.style.transitionDuration = '200ms';
      // hiddenSection.style.transitionTimingFunction = 'cubic-bezier(0.4, 0, 0.2, 1)';
    } else {
      hiddenSection.setAttribute("aria-expanded", "true");
      hiddenSection.style.display = "block";
      // hiddenSection.classList.remove = "hidden";
      // hiddenSection.style.transform = "translateY(0%)";
      // hiddenSection.style.transitionDuration = '200ms';
      // hiddenSection.style.transitionTimingFunction = 'cubic-bezier(0.4, 0, 0.2, 1)';
    }
  }

  connectedCallback() {
    this.shadowRoot
      .querySelector("#hide-btn")
      .addEventListener("click", () => this.toggleInfo());
  }

  disconnectedCallback() {
    this.shadowRoot.querySelector("#hide-btn").removeEventListener();
  }
}

customElements.define("section-hider", SectionHider);
