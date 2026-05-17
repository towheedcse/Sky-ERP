const documentElm = document.documentElement;
const setTheme = (theme) => {
  documentElm.setAttribute("data-theme", theme);
  localStorage.setItem("theme", theme);
};

function toggleTheme() {
  const clientTheme = documentElm.dataset.theme;

  if (clientTheme) {
    clientTheme === "dark" ? setTheme("light") : setTheme("dark");
    return;
  }

  const darkTheme = window.matchMedia("(prefers-color-scheme: dark)").matches;
  darkTheme ? setTheme("light") : setTheme("dark");
}

const storedTheme = localStorage.getItem("theme");
storedTheme && setTheme(storedTheme);

// text sag

const textInputs = document.querySelectorAll("input[type='text'");
const allInputSagDD = document.querySelectorAll(".input-suggestions-dd");

function removeAllSagDD() {
  for (let inputSagDD of allInputSagDD) {
    inputSagDD.style.display = "none";
  }
}
document.addEventListener("click", removeAllSagDD);

textInputs.forEach((textInput) => {
  const inputSagDD = textInput.parentElement.querySelector(
    ".input-suggestions-dd"
  );

  if (!inputSagDD) return;

  const inputSags = inputSagDD?.querySelectorAll(".input-suggestion");
  const searchArr = [];

  inputSags?.forEach((sag) => searchArr.push(sag.innerText));

  textInput.addEventListener("click", (e) => e.stopPropagation());

  textInput.addEventListener("focus", (e) => {
    removeAllSagDD();
    inputSagDD.style.display = "block";
  });

  inputSagDD.addEventListener("click", (e) => {
    const clickedElm = e.target;

    if (!Array.from(clickedElm.classList).includes("input-suggestion")) return;

    textInput.value = clickedElm.innerText;
  });

  setSuggestions(textInput, inputSagDD, searchArr);
});

function setSuggestions(inp, dd, arr) {
  let activeSagIndex = -1;

  inp.addEventListener("input", (e) => {
    const inputText = inp.value;

    dd.innerHTML = "";
    const matches = getMatch(inputText, arr);

    matches.forEach((match) => {
      const div = document.createElement("div");
      div.classList.add("input-suggestion");
      div.innerText = match;

      dd.appendChild(div);
    });
  });

  inp.addEventListener("keydown", (e) => {
    if (![40, 38, 13].includes(e.keyCode)) return;

    const sagElms = dd.children;

    if (e.keyCode === 40)
      activeSagIndex = limitInc(-1, activeSagIndex + 1, sagElms.length - 1);
    if (e.keyCode === 38)
      activeSagIndex = limitInc(-1, activeSagIndex - 1, sagElms.length - 1);

    const activeSagElm = sagElms[activeSagIndex];

    if (e.keyCode === 13) {
      e.preventDefault();
      inp.value = activeSagElm.innerText;
    }

    for (let sagElm of sagElms) {
      sagElm.classList.remove("active");
    }
    activeSagElm?.classList.add("active");
  });
}

function getMatch(text, arr) {
  const matchArr = [];
  arr.forEach((item) => {
    if (item.toLowerCase().includes(text.toLowerCase())) matchArr.push(item);
  });
  return matchArr;
}

function limitInc(min, value, max) {
  if (value < min) return min;
  if (value > max) return max;
  return value;
}
