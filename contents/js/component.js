// import "./components/mainLayout.js";
import "./common/sectionHider.js";
import "./common/controlPanel.js";
import "./common/paginationPanel.js";
//import "./chart.js";


// multiselect start
const checkboxes = document.querySelectorAll('#weekend input[type="checkbox"]');
const selectButton = document.getElementById("selectButton");

checkboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
        const selectedOptions = Array.from(checkboxes)
            .filter((checkbox) => checkbox.checked)
            .map((checkbox) => checkbox.value);

        selectButton.innerHTML =
            selectedOptions.length > 0
                ? selectedOptions
                    .map(
                        (op) =>
                            `<span class="bg-info rounded-sm text-white px-1">${op}</span>`
                    )
                    .join("")
                : ` Select <span class="ml-auto pr-2"> <i class="fa-solid fa-caret-down fa-sm"></i> </span>`;
    });
});

// multiselect end

// table controll script start
// employee table selections
const employee_records = document.getElementById("employee-records");
const employee_records_tr = document.querySelectorAll("#employee-records tr");

const probationContainer = document.querySelector("#probation-container");

let probation_record_size = null;
if (probationContainer) {
    probation_record_size = probationContainer.querySelector("#entries");
}

let probation_container = null;
if (probationContainer) {
    probation_container = probationContainer.querySelector(".pagination-container");
}

// employee table variables
let employee_records_per_page = 5;
let employee_page_number = 1;
const employee_total_records = employee_records_tr.length;
let employee_total_page = Math.ceil(
    employee_total_records / employee_records_per_page
);
// console.log(probation_record_size);

// atendence table selections
const atendence_records = document.getElementById("atendence-records");
const atendence_records_tr = document.querySelectorAll("#atendence-records tr");

const atendenceContainer = document.querySelector("#atendence-container");

let atendence_record_size = null;
if (atendenceContainer) {
    atendence_record_size = atendenceContainer.querySelector("#entries");
}

let atendence_container = null;
if (atendenceContainer) {
    atendence_container = atendenceContainer.querySelector(".pagination-container");
}


// atendence table variables
let atendence_records_per_page = 5;
let atendence_page_number = 1;
const atendence_total_records = atendence_records_tr.length;
let atendence_total_page = Math.ceil(
    atendence_total_records / atendence_records_per_page
);

// functions call for employee
generatePagination(employee_total_page, probation_container);
displayRecords(
    employee_records,
    employee_records_tr,
    employee_records_per_page,
    employee_page_number,
    employee_total_records,
    employee_total_page,
    probation_container
);

// functions call for atendence
generatePagination(atendence_total_page, atendence_container);
displayRecords(
    atendence_records,
    atendence_records_tr,
    atendence_records_per_page,
    atendence_page_number,
    atendence_total_records,
    atendence_total_page,
    atendence_container
);

// function for displayRecords
function displayRecords(
    records,
    tr,
    records_per_page,
    page_number,
    total_records,
    total_page,
    target_container
) {
    if (!target_container) {
        return;
    }

    let start_index = (page_number - 1) * records_per_page;
    let end_index = start_index + (records_per_page - 1);
    if (end_index >= total_records) {
        end_index = total_records - 1;
    }

    let dynamicTable = "";
    for (let i = start_index; i <= end_index; i++) {
        dynamicTable += `<tr> ${tr[i].innerHTML} </tr>`;
    }
    records.innerHTML = dynamicTable;
    target_container
        .querySelector(".pagination-list")
        .querySelectorAll(".page-item")
        .forEach((item) => {
            item.classList.remove("btn-active");
        });
    target_container
        .querySelector(".pagination-list")
        .querySelector(`#page_${page_number}`)
        .classList.add("btn-active");
    // for previous btn
    if (page_number == 1) {
        target_container.querySelector("#prevBtn").classList.add("btn-disabled");
    } else {
        target_container.querySelector("#prevBtn").classList.remove("btn-disabled");
    }
    // for next btn
    if (page_number == total_page) {
        target_container.querySelector("#nextBtn").classList.add("btn-disabled");
    } else {
        target_container.querySelector("#nextBtn").classList.remove("btn-disabled");
    }

    // entries
    target_container.querySelector("#page-details").innerHTML = `Showing ${
        start_index + 1
    } to ${end_index + 1} of ${total_records} entries`;
}

// function for generatePagination
function generatePagination(total_page_range, target_container) {
    if (!target_container) {
        return;
    }

    const paginationEl = target_container.querySelector("#pagination");
    if (!paginationEl) {
        return;
    }

    let prevBtn = `<button class="text-xs join-item btn btn-sm sm:text-sm" id="prevBtn">«</button>`;

    let nextBtn = `<button class="text-xs join-item btn btn-sm sm:text-sm" id="nextBtn">»</button>`;

    let buttons = "";
    let activeClass = "";
    //   const dottedBtn = `<button class="text-xs join-item btn btn-sm sm:text-sm btn-disabled">...</button>`
    for (let i = 1; i <= total_page_range; i++) {
        if (i == 1) {
            activeClass = "btn-active";
        } else {
            activeClass = "";
        }

        buttons += `<div class="page-item text-xs join-item btn btn-sm sm:text-sm ${activeClass}" id="page_${i}">${i}</div>`;
    }

    target_container.querySelector(
        "#pagination"
    ).innerHTML = `${prevBtn} <div class="pagination-list"> ${buttons} </div> ${nextBtn}`;
}

// employee click events
if (probation_container) {
    probation_container.querySelector("#prevBtn").addEventListener("click", function prevBtn(e) {
            if (this.classList.contains("disabled")) {
                e.stopPropagation();
                return;
            } else {
                employee_page_number--;
                displayRecords(
                    employee_records,
                    employee_records_tr,
                    employee_records_per_page,
                    employee_page_number,
                    employee_total_records,
                    employee_total_page,
                    probation_container
                );
            }
        });
}

if (probation_container) {
probation_container
    .querySelector("#nextBtn")
    .addEventListener("click", function nextBtn(e) {
        if (this.classList.contains("disabled")) {
            e.stopPropagation();
            return;
        } else {
            employee_page_number++;
            displayRecords(
                employee_records,
                employee_records_tr,
                employee_records_per_page,
                employee_page_number,
                employee_total_records,
                employee_total_page,
                probation_container
            );
        }
    });
}

// switching employee table page numbers
if (probation_container) {
probation_container
    .querySelector(".pagination-list")
    .childNodes.forEach((div) => {
    div.addEventListener("click", function (e) {
        let page = e.target.id;
        let pattern = /[0-9]/g;
        let index = page.match(pattern).toString();
        employee_page_number = parseInt(index);
        displayRecords(
            employee_records,
            employee_records_tr,
            employee_records_per_page,
            employee_page_number,
            employee_total_records,
            employee_total_page,
            probation_container
        );
    });
});
}

// employee entries
if (probation_record_size) {
probation_record_size.addEventListener("change", function (e) {
    employee_records_per_page = parseInt(probation_record_size.value);
    employee_total_page = Math.ceil(
        employee_total_records / employee_records_per_page
    );
    employee_page_number = 1;
    generatePagination(employee_total_page, probation_container);
    displayRecords(
        employee_records,
        employee_records_tr,
        employee_records_per_page,
        employee_page_number,
        employee_total_records,
        employee_total_page,
        probation_container
    );

if (probation_container) {
    probation_container
        .querySelector("#prevBtn")
        .addEventListener("click", function prevBtn(e) {
            if (this.classList.contains("disabled")) {
                e.stopPropagation();
                return;
            } else {
                employee_page_number--;
                displayRecords(
                    employee_records,
                    employee_records_tr,
                    employee_records_per_page,
                    employee_page_number,
                    employee_total_records,
                    employee_total_page,
                    probation_container
                );
            }
        });

    probation_container
        .querySelector("#nextBtn")
        .addEventListener("click", function nextBtn(e) {
            if (this.classList.contains("disabled")) {
                e.stopPropagation();
                return;
            } else {
                employee_page_number++;
                displayRecords(
                    employee_records,
                    employee_records_tr,
                    employee_records_per_page,
                    employee_page_number,
                    employee_total_records,
                    employee_total_page,
                    probation_container
                );
            }
        });

    probation_container
        .querySelector(".pagination-list")
        .childNodes.forEach((div) => {
        div.addEventListener("click", function (e) {
            let page = e.target.id;
            let pattern = /[0-9]/g;
            let index = page.match(pattern).toString();
            employee_page_number = parseInt(index);
            displayRecords(
                employee_records,
                employee_records_tr,
                employee_records_per_page,
                employee_page_number,
                employee_total_records,
                employee_total_page,
                probation_container
            );
        });
    });
}

});

}

// atendence click events
if(atendence_container){
atendence_container
    .querySelector("#prevBtn")
    .addEventListener("click", function prevBtn(e) {
        if (this.classList.contains("disabled")) {
            e.stopPropagation();
            return;
        } else {
            atendence_page_number--;
            displayRecords(
                atendence_records,
                atendence_records_tr,
                atendence_records_per_page,
                atendence_page_number,
                atendence_total_records,
                atendence_total_page,
                atendence_container
            );
        }
    });

atendence_container
    .querySelector("#nextBtn")
    .addEventListener("click", function nextBtn(e) {
        if (this.classList.contains("disabled")) {
            e.stopPropagation();
            return;
        } else {
            atendence_page_number++;
            displayRecords(
                atendence_records,
                atendence_records_tr,
                atendence_records_per_page,
                atendence_page_number,
                atendence_total_records,
                atendence_total_page,
                atendence_container
            );
        }
    });

// switching atendence table page numbers
atendence_container
    .querySelector(".pagination-list")
    .childNodes.forEach((div) => {
    div.addEventListener("click", function (e) {
        let page = e.target.id;
        let pattern = /[0-9]/g;
        let index = page.match(pattern).toString();
        atendence_page_number = parseInt(index);
        displayRecords(
            atendence_records,
            atendence_records_tr,
            atendence_records_per_page,
            atendence_page_number,
            atendence_total_records,
            atendence_total_page,
            atendence_container
        );
    });
});
}


// atendence entries
if(atendence_record_size){
atendence_record_size.addEventListener("change", function (e) {
    atendence_records_per_page = parseInt(atendence_record_size.value);
    atendence_total_page = Math.ceil(
        atendence_total_records / atendence_records_per_page
    );
    atendence_page_number = 1;
    generatePagination(atendence_total_page, atendence_container);
    displayRecords(
        atendence_records,
        atendence_records_tr,
        atendence_records_per_page,
        atendence_page_number,
        atendence_total_records,
        atendence_total_page,
        atendence_container
    );

if(atendence_container){
    atendence_container
        .querySelector("#prevBtn")
        .addEventListener("click", function prevBtn(e) {
            if (this.classList.contains("disabled")) {
                e.stopPropagation();
                return;
            } else {
                atendence_page_number--;
                displayRecords(
                    atendence_records,
                    atendence_records_tr,
                    atendence_records_per_page,
                    atendence_page_number,
                    atendence_total_records,
                    atendence_total_page,
                    atendence_container
                );
            }
        });

    atendence_container
        .querySelector("#nextBtn")
        .addEventListener("click", function nextBtn(e) {
            if (this.classList.contains("disabled")) {
                e.stopPropagation();
                return;
            } else {
                atendence_page_number++;
                displayRecords(
                    atendence_records,
                    atendence_records_tr,
                    atendence_records_per_page,
                    atendence_page_number,
                    atendence_total_records,
                    atendence_total_page,
                    atendence_container
                );
            }
        });

    atendence_container
        .querySelector(".pagination-list")
        .childNodes.forEach((div) => {
        div.addEventListener("click", function (e) {
            let page = e.target.id;
            let pattern = /[0-9]/g;
            let index = page.match(pattern).toString();
            atendence_page_number = parseInt(index);
            displayRecords(
                atendence_records,
                atendence_records_tr,
                atendence_records_per_page,
                atendence_page_number,
                atendence_total_records,
                atendence_total_page,
                atendence_container
            );
        });
    });
}

});
}

// to hide elements
const probationToHide = document.getElementById("minimize-section");
const notesToHide = document.getElementById("minimize-note-section");
const linksToHide = document.getElementById("minimize-link-section");

// hidden class injection
function displayNone(section) {
    section.classList.toggle("hidden");
}

const minimizeProbation = document.getElementById("minimize-probation");
if(minimizeProbation){
	minimizeProbationaddEventListener("click", () => displayNone(probationToHide));
}

const minimizeNote = document.getElementById("minimize-note");
if(minimizeNote){
	minimizeNote.addEventListener("click", () => displayNone(notesToHide));
}

const minimizeLink = document.getElementById("minimize-link");
if(minimizeLink){
	minimizeLink.addEventListener("click", () => displayNone(linksToHide));
}

window.addEventListener("DOMContentLoaded", () => {
    // atendence table sort
    const atendenceTable = document.querySelector(".atendence-table");
    if(atendenceTable){
        const atendence_sort = atendenceTable.querySelectorAll("th");
   
	for (let i = 0; i < atendence_sort.length; i++) {
	   atendence_sort[i].setAttribute(
	      "onclick",
	      `sortTable(${i},this,'#atendence-container')`
	   );
	}
    }

    // employee table sort
    const employeeTable = document.querySelector(".employee-table");
    if(employeeTable){
            const employee_sort = employeeTable.querySelectorAll("th");

	    for (let j = 0; j < employee_sort.length; j++) {
		employee_sort[j].setAttribute(
		    "onclick",
		    `sortTable(${j},this,'#probation-container')`
		);
	    }
    }

    // date format
    const date_format = document.querySelectorAll(".date-box");
    if(employeeTable){
	    date_format.forEach((element) => {
		if (element.classList.contains("month")) {
		    element.innerHTML = "Apr-2025";
		} else {
		    element.innerHTML = "04-04-2025";
		}
	    });
    }
});
