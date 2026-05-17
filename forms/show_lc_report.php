<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>LC report</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <style>
@import url("https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,400;0,500;1,700&display=swap");

/*
! tailwindcss v3.4.0 | MIT License | https://tailwindcss.com
*/

/*
1. Prevent padding and border from affecting element width. (https://github.com/mozdevs/cssremedy/issues/4)
2. Allow adding a border to an element by just adding a border-width. (https://github.com/tailwindcss/tailwindcss/pull/116)
*/

*,
::before,
::after {
  box-sizing: border-box;
  /* 1 */
  border-width: 0;
  /* 2 */
  border-style: solid;
  /* 2 */
  border-color: #e5e7eb;
  /* 2 */
}

::before,
::after {
  --tw-content: '';
}

/*
1. Use a consistent sensible line-height in all browsers.
2. Prevent adjustments of font size after orientation changes in iOS.
3. Use a more readable tab size.
4. Use the user's configured `sans` font-family by default.
5. Use the user's configured `sans` font-feature-settings by default.
6. Use the user's configured `sans` font-variation-settings by default.
7. Disable tap highlights on iOS
*/

html,
:host {
  line-height: 1.5;
  /* 1 */
  -webkit-text-size-adjust: 100%;
  /* 2 */
  -moz-tab-size: 4;
  /* 3 */
  -o-tab-size: 4;
     tab-size: 4;
  /* 3 */
  font-family: Montserrat, sans-serif;
  /* 4 */
  font-feature-settings: normal;
  /* 5 */
  font-variation-settings: normal;
  /* 6 */
  -webkit-tap-highlight-color: transparent;
  /* 7 */
}

/*
1. Remove the margin in all browsers.
2. Inherit line-height from `html` so users can set them as a class directly on the `html` element.
*/

body {
  margin: 0;
  /* 1 */
  line-height: inherit;
  /* 2 */
}

/*
1. Add the correct height in Firefox.
2. Correct the inheritance of border color in Firefox. (https://bugzilla.mozilla.org/show_bug.cgi?id=190655)
3. Ensure horizontal rules are visible by default.
*/

hr {
  height: 0;
  /* 1 */
  color: inherit;
  /* 2 */
  border-top-width: 1px;
  /* 3 */
}

/*
Add the correct text decoration in Chrome, Edge, and Safari.
*/

abbr:where([title]) {
  -webkit-text-decoration: underline dotted;
          text-decoration: underline dotted;
}

/*
Remove the default font size and weight for headings.
*/

h1,
h2,
h3,
h4,
h5,
h6 {
  font-size: inherit;
  font-weight: inherit;
}

/*
Reset links to optimize for opt-in styling instead of opt-out.
*/

a {
  color: inherit;
  text-decoration: inherit;
}

/*
Add the correct font weight in Edge and Safari.
*/

b,
strong {
  font-weight: bolder;
}

/*
1. Use the user's configured `mono` font-family by default.
2. Use the user's configured `mono` font-feature-settings by default.
3. Use the user's configured `mono` font-variation-settings by default.
4. Correct the odd `em` font sizing in all browsers.
*/

code,
kbd,
samp,
pre {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
  /* 1 */
  font-feature-settings: normal;
  /* 2 */
  font-variation-settings: normal;
  /* 3 */
  font-size: 1em;
  /* 4 */
}

/*
Add the correct font size in all browsers.
*/

small {
  font-size: 80%;
}

/*
Prevent `sub` and `sup` elements from affecting the line height in all browsers.
*/

sub,
sup {
  font-size: 75%;
  line-height: 0;
  position: relative;
  vertical-align: baseline;
}

sub {
  bottom: -0.25em;
}

sup {
  top: -0.5em;
}

/*
1. Remove text indentation from table contents in Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=999088, https://bugs.webkit.org/show_bug.cgi?id=201297)
2. Correct table border color inheritance in all Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=935729, https://bugs.webkit.org/show_bug.cgi?id=195016)
3. Remove gaps between table borders by default.
*/

table {
  text-indent: 0;
  /* 1 */
  border-color: inherit;
  /* 2 */
  border-collapse: collapse;
  /* 3 */
}

/*
1. Change the font styles in all browsers.
2. Remove the margin in Firefox and Safari.
3. Remove default padding in all browsers.
*/

button,
input,
optgroup,
select,
textarea {
  font-family: inherit;
  /* 1 */
  font-feature-settings: inherit;
  /* 1 */
  font-variation-settings: inherit;
  /* 1 */
  font-size: 100%;
  /* 1 */
  font-weight: inherit;
  /* 1 */
  line-height: inherit;
  /* 1 */
  color: inherit;
  /* 1 */
  margin: 0;
  /* 2 */
  padding: 0;
  /* 3 */
}

/*
Remove the inheritance of text transform in Edge and Firefox.
*/

button,
select {
  text-transform: none;
}

/*
1. Correct the inability to style clickable types in iOS and Safari.
2. Remove default button styles.
*/

button,
[type='button'],
[type='reset'],
[type='submit'] {
  -webkit-appearance: button;
  /* 1 */
  background-color: transparent;
  /* 2 */
  background-image: none;
  /* 2 */
}

/*
Use the modern Firefox focus style for all focusable elements.
*/

:-moz-focusring {
  outline: auto;
}

/*
Remove the additional `:invalid` styles in Firefox. (https://github.com/mozilla/gecko-dev/blob/2f9eacd9d3d995c937b4251a5557d95d494c9be1/layout/style/res/forms.css#L728-L737)
*/

:-moz-ui-invalid {
  box-shadow: none;
}

/*
Add the correct vertical alignment in Chrome and Firefox.
*/

progress {
  vertical-align: baseline;
}

/*
Correct the cursor style of increment and decrement buttons in Safari.
*/

::-webkit-inner-spin-button,
::-webkit-outer-spin-button {
  height: auto;
}

/*
1. Correct the odd appearance in Chrome and Safari.
2. Correct the outline style in Safari.
*/

[type='search'] {
  -webkit-appearance: textfield;
  /* 1 */
  outline-offset: -2px;
  /* 2 */
}

/*
Remove the inner padding in Chrome and Safari on macOS.
*/

::-webkit-search-decoration {
  -webkit-appearance: none;
}

/*
1. Correct the inability to style clickable types in iOS and Safari.
2. Change font properties to `inherit` in Safari.
*/

::-webkit-file-upload-button {
  -webkit-appearance: button;
  /* 1 */
  font: inherit;
  /* 2 */
}

/*
Add the correct display in Chrome and Safari.
*/

summary {
  display: list-item;
}

/*
Removes the default spacing and border for appropriate elements.
*/

blockquote,
dl,
dd,
h1,
h2,
h3,
h4,
h5,
h6,
hr,
figure,
p,
pre {
  margin: 0;
}

fieldset {
  margin: 0;
  padding: 0;
}

legend {
  padding: 0;
}

ol,
ul,
menu {
  list-style: none;
  margin: 0;
  padding: 0;
}

/*
Reset default styling for dialogs.
*/

dialog {
  padding: 0;
}

/*
Prevent resizing textareas horizontally by default.
*/

textarea {
  resize: vertical;
}

/*
1. Reset the default placeholder opacity in Firefox. (https://github.com/tailwindlabs/tailwindcss/issues/3300)
2. Set the default placeholder color to the user's configured gray 400 color.
*/

input::-moz-placeholder, textarea::-moz-placeholder {
  opacity: 1;
  /* 1 */
  color: #9ca3af;
  /* 2 */
}

input::placeholder,
textarea::placeholder {
  opacity: 1;
  /* 1 */
  color: #9ca3af;
  /* 2 */
}

/*
Set the default cursor for buttons.
*/

button,
[role="button"] {
  cursor: pointer;
}

/*
Make sure disabled buttons don't get the pointer cursor.
*/

:disabled {
  cursor: default;
}

/*
1. Make replaced elements `display: block` by default. (https://github.com/mozdevs/cssremedy/issues/14)
2. Add `vertical-align: middle` to align replaced elements more sensibly by default. (https://github.com/jensimmons/cssremedy/issues/14#issuecomment-634934210)
   This can trigger a poorly considered lint error in some tools but is included by design.
*/

img,
svg,
video,
canvas,
audio,
iframe,
embed,
object {
  display: block;
  /* 1 */
  vertical-align: middle;
  /* 2 */
}

/*
Constrain images and videos to the parent width and preserve their intrinsic aspect ratio. (https://github.com/mozdevs/cssremedy/issues/14)
*/

img,
video {
  max-width: 100%;
  height: auto;
}

/* Make elements with the HTML hidden attribute stay hidden by default */

[hidden] {
  display: none;
}

:root,
[data-theme]{
  background-color: var(--fallback-b1,oklch(var(--b1)/1));
  color: var(--fallback-bc,oklch(var(--bc)/1));
}

@supports not (color: oklch(0 0 0)){
  :root{
    color-scheme: light;
    --fallback-p: #491eff;
    --fallback-pc: #d4dbff;
    --fallback-s: #ff41c7;
    --fallback-sc: #fff9fc;
    --fallback-a: #00cfbd;
    --fallback-ac: #00100d;
    --fallback-n: #2b3440;
    --fallback-nc: #d7dde4;
    --fallback-b1: #ffffff;
    --fallback-b2: #e5e6e6;
    --fallback-b3: #e5e6e6;
    --fallback-bc: #1f2937;
    --fallback-in: #00b3f0;
    --fallback-inc: #000000;
    --fallback-su: #00ca92;
    --fallback-suc: #000000;
    --fallback-wa: #ffc22d;
    --fallback-wac: #000000;
    --fallback-er: #ff6f70;
    --fallback-erc: #000000;
  }

  @media (prefers-color-scheme: dark){
    :root{
      color-scheme: dark;
      --fallback-p: #7582ff;
      --fallback-pc: #050617;
      --fallback-s: #ff71cf;
      --fallback-sc: #190211;
      --fallback-a: #00c7b5;
      --fallback-ac: #000e0c;
      --fallback-n: #2a323c;
      --fallback-nc: #a6adbb;
      --fallback-b1: #1d232a;
      --fallback-b2: #191e24;
      --fallback-b3: #15191e;
      --fallback-bc: #a6adbb;
      --fallback-in: #00b3f0;
      --fallback-inc: #000000;
      --fallback-su: #00ca92;
      --fallback-suc: #000000;
      --fallback-wa: #ffc22d;
      --fallback-wac: #000000;
      --fallback-er: #ff6f70;
      --fallback-erc: #000000;
    }
  }
}

html{
  -webkit-tap-highlight-color: transparent;
}

:root{
  color-scheme: light;
  --in: 0.7206 0.191 231.6;
  --su: 64.8% 0.150 160;
  --wa: 0.8471 0.199 83.87;
  --er: 0.7176 0.221 22.18;
  --pc: 0.89824 0.06192 275.75;
  --ac: 0.15352 0.0368 183.61;
  --inc: 0 0 0;
  --suc: 0 0 0;
  --wac: 0 0 0;
  --erc: 0 0 0;
  --rounded-box: 1rem;
  --rounded-btn: 0.5rem;
  --rounded-badge: 1.9rem;
  --animation-btn: 0.25s;
  --animation-input: .2s;
  --btn-focus-scale: 0.95;
  --border-btn: 1px;
  --tab-border: 1px;
  --tab-radius: 0.5rem;
  --p: 0.4912 0.3096 275.75;
  --s: 0.6971 0.329 342.55;
  --sc: 0.9871 0.0106 342.55;
  --a: 0.7676 0.184 183.61;
  --n: 0.321785 0.02476 255.701624;
  --nc: 0.894994 0.011585 252.096176;
  --b1: 1 0 0;
  --b2: 0.961151 0 0;
  --b3: 0.924169 0.00108 197.137559;
  --bc: 0.278078 0.029596 256.847952;
}

@media (prefers-color-scheme: dark){
  :root{
    color-scheme: dark;
    --in: 0.7206 0.191 231.6;
    --su: 64.8% 0.150 160;
    --wa: 0.8471 0.199 83.87;
    --er: 0.7176 0.221 22.18;
    --pc: 0.13138 0.0392 275.75;
    --sc: 0.1496 0.052 342.55;
    --ac: 0.14902 0.0334 183.61;
    --inc: 0 0 0;
    --suc: 0 0 0;
    --wac: 0 0 0;
    --erc: 0 0 0;
    --rounded-box: 1rem;
    --rounded-btn: 0.5rem;
    --rounded-badge: 1.9rem;
    --animation-btn: 0.25s;
    --animation-input: .2s;
    --btn-focus-scale: 0.95;
    --border-btn: 1px;
    --tab-border: 1px;
    --tab-radius: 0.5rem;
    --p: 0.6569 0.196 275.75;
    --s: 0.748 0.26 342.55;
    --a: 0.7451 0.167 183.61;
    --n: 0.313815 0.021108 254.139175;
    --nc: 0.746477 0.0216 264.435964;
    --b1: 0.253267 0.015896 252.417568;
    --b2: 0.232607 0.013807 253.100675;
    --b3: 0.211484 0.01165 254.087939;
    --bc: 0.746477 0.0216 264.435964;
  }
}

[data-theme=light]{
  color-scheme: light;
  --in: 0.7206 0.191 231.6;
  --su: 64.8% 0.150 160;
  --wa: 0.8471 0.199 83.87;
  --er: 0.7176 0.221 22.18;
  --pc: 0.89824 0.06192 275.75;
  --ac: 0.15352 0.0368 183.61;
  --inc: 0 0 0;
  --suc: 0 0 0;
  --wac: 0 0 0;
  --erc: 0 0 0;
  --rounded-box: 1rem;
  --rounded-btn: 0.5rem;
  --rounded-badge: 1.9rem;
  --animation-btn: 0.25s;
  --animation-input: .2s;
  --btn-focus-scale: 0.95;
  --border-btn: 1px;
  --tab-border: 1px;
  --tab-radius: 0.5rem;
  --p: 0.4912 0.3096 275.75;
  --s: 0.6971 0.329 342.55;
  --sc: 0.9871 0.0106 342.55;
  --a: 0.7676 0.184 183.61;
  --n: 0.321785 0.02476 255.701624;
  --nc: 0.894994 0.011585 252.096176;
  --b1: 1 0 0;
  --b2: 0.961151 0 0;
  --b3: 0.924169 0.00108 197.137559;
  --bc: 0.278078 0.029596 256.847952;
}

[data-theme=dark]{
  color-scheme: dark;
  --in: 0.7206 0.191 231.6;
  --su: 64.8% 0.150 160;
  --wa: 0.8471 0.199 83.87;
  --er: 0.7176 0.221 22.18;
  --pc: 0.13138 0.0392 275.75;
  --sc: 0.1496 0.052 342.55;
  --ac: 0.14902 0.0334 183.61;
  --inc: 0 0 0;
  --suc: 0 0 0;
  --wac: 0 0 0;
  --erc: 0 0 0;
  --rounded-box: 1rem;
  --rounded-btn: 0.5rem;
  --rounded-badge: 1.9rem;
  --animation-btn: 0.25s;
  --animation-input: .2s;
  --btn-focus-scale: 0.95;
  --border-btn: 1px;
  --tab-border: 1px;
  --tab-radius: 0.5rem;
  --p: 0.6569 0.196 275.75;
  --s: 0.748 0.26 342.55;
  --a: 0.7451 0.167 183.61;
  --n: 0.313815 0.021108 254.139175;
  --nc: 0.746477 0.0216 264.435964;
  --b1: 0.253267 0.015896 252.417568;
  --b2: 0.232607 0.013807 253.100675;
  --b3: 0.211484 0.01165 254.087939;
  --bc: 0.746477 0.0216 264.435964;
}

*, ::before, ::after{
  --tw-border-spacing-x: 0;
  --tw-border-spacing-y: 0;
  --tw-translate-x: 0;
  --tw-translate-y: 0;
  --tw-rotate: 0;
  --tw-skew-x: 0;
  --tw-skew-y: 0;
  --tw-scale-x: 1;
  --tw-scale-y: 1;
  --tw-pan-x:  ;
  --tw-pan-y:  ;
  --tw-pinch-zoom:  ;
  --tw-scroll-snap-strictness: proximity;
  --tw-gradient-from-position:  ;
  --tw-gradient-via-position:  ;
  --tw-gradient-to-position:  ;
  --tw-ordinal:  ;
  --tw-slashed-zero:  ;
  --tw-numeric-figure:  ;
  --tw-numeric-spacing:  ;
  --tw-numeric-fraction:  ;
  --tw-ring-inset:  ;
  --tw-ring-offset-width: 0px;
  --tw-ring-offset-color: #fff;
  --tw-ring-color: rgb(59 130 246 / 0.5);
  --tw-ring-offset-shadow: 0 0 #0000;
  --tw-ring-shadow: 0 0 #0000;
  --tw-shadow: 0 0 #0000;
  --tw-shadow-colored: 0 0 #0000;
  --tw-blur:  ;
  --tw-brightness:  ;
  --tw-contrast:  ;
  --tw-grayscale:  ;
  --tw-hue-rotate:  ;
  --tw-invert:  ;
  --tw-saturate:  ;
  --tw-sepia:  ;
  --tw-drop-shadow:  ;
  --tw-backdrop-blur:  ;
  --tw-backdrop-brightness:  ;
  --tw-backdrop-contrast:  ;
  --tw-backdrop-grayscale:  ;
  --tw-backdrop-hue-rotate:  ;
  --tw-backdrop-invert:  ;
  --tw-backdrop-opacity:  ;
  --tw-backdrop-saturate:  ;
  --tw-backdrop-sepia:  ;
}

::backdrop{
  --tw-border-spacing-x: 0;
  --tw-border-spacing-y: 0;
  --tw-translate-x: 0;
  --tw-translate-y: 0;
  --tw-rotate: 0;
  --tw-skew-x: 0;
  --tw-skew-y: 0;
  --tw-scale-x: 1;
  --tw-scale-y: 1;
  --tw-pan-x:  ;
  --tw-pan-y:  ;
  --tw-pinch-zoom:  ;
  --tw-scroll-snap-strictness: proximity;
  --tw-gradient-from-position:  ;
  --tw-gradient-via-position:  ;
  --tw-gradient-to-position:  ;
  --tw-ordinal:  ;
  --tw-slashed-zero:  ;
  --tw-numeric-figure:  ;
  --tw-numeric-spacing:  ;
  --tw-numeric-fraction:  ;
  --tw-ring-inset:  ;
  --tw-ring-offset-width: 0px;
  --tw-ring-offset-color: #fff;
  --tw-ring-color: rgb(59 130 246 / 0.5);
  --tw-ring-offset-shadow: 0 0 #0000;
  --tw-ring-shadow: 0 0 #0000;
  --tw-shadow: 0 0 #0000;
  --tw-shadow-colored: 0 0 #0000;
  --tw-blur:  ;
  --tw-brightness:  ;
  --tw-contrast:  ;
  --tw-grayscale:  ;
  --tw-hue-rotate:  ;
  --tw-invert:  ;
  --tw-saturate:  ;
  --tw-sepia:  ;
  --tw-drop-shadow:  ;
  --tw-backdrop-blur:  ;
  --tw-backdrop-brightness:  ;
  --tw-backdrop-contrast:  ;
  --tw-backdrop-grayscale:  ;
  --tw-backdrop-hue-rotate:  ;
  --tw-backdrop-invert:  ;
  --tw-backdrop-opacity:  ;
  --tw-backdrop-saturate:  ;
  --tw-backdrop-sepia:  ;
}

.avatar.placeholder > div{
  display: flex;
  align-items: center;
  justify-content: center;
}

@media (hover:hover){
  .label a:hover{
    --tw-text-opacity: 1;
    color: var(--fallback-bc,oklch(var(--bc)/var(--tw-text-opacity)));
  }

  .menu li > *:not(ul):not(.menu-title):not(details):active,
.menu li > *:not(ul):not(.menu-title):not(details).active,
.menu li > details > summary:active{
    --tw-bg-opacity: 1;
    background-color: var(--fallback-n,oklch(var(--n)/var(--tw-bg-opacity)));
    --tw-text-opacity: 1;
    color: var(--fallback-nc,oklch(var(--nc)/var(--tw-text-opacity)));
  }

  .table tr.hover:hover,
  .table tr.hover:nth-child(even):hover{
    --tw-bg-opacity: 1;
    background-color: var(--fallback-b2,oklch(var(--b2)/var(--tw-bg-opacity)));
  }

  .table-zebra tr.hover:hover,
  .table-zebra tr.hover:nth-child(even):hover{
    --tw-bg-opacity: 1;
    background-color: var(--fallback-b3,oklch(var(--b3)/var(--tw-bg-opacity)));
  }
}

.btn{
  display: inline-flex;
  height: 3rem;
  min-height: 3rem;
  flex-shrink: 0;
  cursor: pointer;
  -webkit-user-select: none;
     -moz-user-select: none;
          user-select: none;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  border-radius: var(--rounded-btn, 0.5rem);
  border-color: transparent;
  border-color: oklch(var(--btn-color, var(--b2)) / var(--tw-border-opacity));
  padding-left: 1rem;
  padding-right: 1rem;
  text-align: center;
  font-size: 0.875rem;
  line-height: 1em;
  gap: 0.5rem;
  font-weight: 600;
  text-decoration-line: none;
  transition-duration: 200ms;
  transition-timing-function: cubic-bezier(0, 0, 0.2, 1);
  border-width: var(--border-btn, 1px);
  animation: button-pop var(--animation-btn, 0.25s) ease-out;
  transition-property: color, background-color, border-color, opacity, box-shadow, transform;
  --tw-text-opacity: 1;
  color: var(--fallback-bc,oklch(var(--bc)/var(--tw-text-opacity)));
  --tw-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
  outline-color: var(--fallback-bc,oklch(var(--bc)/1));
  background-color: oklch(var(--btn-color, var(--b2)) / var(--tw-bg-opacity));
  --tw-bg-opacity: 1;
  --tw-border-opacity: 1;
}

.btn-disabled,
  .btn[disabled],
  .btn:disabled{
  pointer-events: none;
}

.btn-circle{
  height: 3rem;
  width: 3rem;
  border-radius: 9999px;
  padding: 0px;
}

:where(.btn:is(input[type="checkbox"])),
:where(.btn:is(input[type="radio"])){
  width: auto;
  -webkit-appearance: none;
     -moz-appearance: none;
          appearance: none;
}

.btn:is(input[type="checkbox"]):after,
.btn:is(input[type="radio"]):after{
  --tw-content: attr(aria-label);
  content: var(--tw-content);
}

.checkbox{
  flex-shrink: 0;
  --chkbg: var(--fallback-bc,oklch(var(--bc)/1));
  --chkfg: var(--fallback-b1,oklch(var(--b1)/1));
  height: 1.5rem;
  width: 1.5rem;
  cursor: pointer;
  -webkit-appearance: none;
     -moz-appearance: none;
          appearance: none;
  border-radius: var(--rounded-btn, 0.5rem);
  border-width: 1px;
  border-color: var(--fallback-bc,oklch(var(--bc)/var(--tw-border-opacity)));
  --tw-border-opacity: 0.2;
}

.dropdown{
  position: relative;
  display: inline-block;
}

.dropdown > *:not(summary):focus{
  outline: 2px solid transparent;
  outline-offset: 2px;
}

.dropdown .dropdown-content{
  position: absolute;
}

.dropdown:is(:not(details)) .dropdown-content{
  visibility: hidden;
  opacity: 0;
  transform-origin: top;
  --tw-scale-x: .95;
  --tw-scale-y: .95;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, -webkit-backdrop-filter;
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter, -webkit-backdrop-filter;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-timing-function: cubic-bezier(0, 0, 0.2, 1);
  transition-duration: 200ms;
}

.dropdown-end .dropdown-content{
  inset-inline-end: 0px;
}

.dropdown-left .dropdown-content{
  bottom: auto;
  inset-inline-end: 100%;
  top: 0px;
  transform-origin: right;
}

.dropdown-right .dropdown-content{
  bottom: auto;
  inset-inline-start: 100%;
  top: 0px;
  transform-origin: left;
}

.dropdown-bottom .dropdown-content{
  bottom: auto;
  top: 100%;
  transform-origin: top;
}

.dropdown-top .dropdown-content{
  bottom: 100%;
  top: auto;
  transform-origin: bottom;
}

.dropdown-end.dropdown-right .dropdown-content{
  bottom: 0px;
  top: auto;
}

.dropdown-end.dropdown-left .dropdown-content{
  bottom: 0px;
  top: auto;
}

.dropdown.dropdown-open .dropdown-content,
.dropdown:not(.dropdown-hover):focus .dropdown-content,
.dropdown:focus-within .dropdown-content{
  visibility: visible;
  opacity: 1;
}

@media (hover: hover){
  .dropdown.dropdown-hover:hover .dropdown-content{
    visibility: visible;
    opacity: 1;
  }

  .btm-nav > *.disabled:hover,
      .btm-nav > *[disabled]:hover{
    pointer-events: none;
    --tw-border-opacity: 0;
    background-color: var(--fallback-n,oklch(var(--n)/var(--tw-bg-opacity)));
    --tw-bg-opacity: 0.1;
    color: var(--fallback-bc,oklch(var(--bc)/var(--tw-text-opacity)));
    --tw-text-opacity: 0.2;
  }

  .btn:hover{
    --tw-border-opacity: 1;
    border-color: var(--fallback-b3,oklch(var(--b3)/var(--tw-border-opacity)));
    --tw-bg-opacity: 1;
    background-color: var(--fallback-b3,oklch(var(--b3)/var(--tw-bg-opacity)));
  }

  @supports (color: color-mix(in oklab, black, black)){
    .btn:hover{
      background-color: color-mix(
            in oklab,
            oklch(var(--btn-color, var(--b2)) / var(--tw-bg-opacity, 1)) 90%,
            black
          );
      border-color: color-mix(
            in oklab,
            oklch(var(--btn-color, var(--b2)) / var(--tw-border-opacity, 1)) 90%,
            black
          );
    }
  }

  @supports not (color: oklch(0 0 0)){
    .btn:hover{
      background-color: var(--btn-color, var(--fallback-b2));
      border-color: var(--btn-color, var(--fallback-b2));
    }
  }

  .btn.glass:hover{
    --glass-opacity: 25%;
    --glass-border-opacity: 15%;
  }

  .btn-ghost:hover{
    border-color: transparent;
  }

  @supports (color: oklch(0 0 0)){
    .btn-ghost:hover{
      background-color: var(--fallback-bc,oklch(var(--bc)/0.2));
    }
  }

  .btn-outline.btn-primary:hover{
    --tw-text-opacity: 1;
    color: var(--fallback-pc,oklch(var(--pc)/var(--tw-text-opacity)));
  }

  @supports (color: color-mix(in oklab, black, black)){
    .btn-outline.btn-primary:hover{
      background-color: color-mix(in oklab, var(--fallback-p,oklch(var(--p)/1)) 90%, black);
      border-color: color-mix(in oklab, var(--fallback-p,oklch(var(--p)/1)) 90%, black);
    }
  }

  .btn-disabled:hover,
    .btn[disabled]:hover,
    .btn:disabled:hover{
    --tw-border-opacity: 0;
    background-color: var(--fallback-n,oklch(var(--n)/var(--tw-bg-opacity)));
    --tw-bg-opacity: 0.2;
    color: var(--fallback-bc,oklch(var(--bc)/var(--tw-text-opacity)));
    --tw-text-opacity: 0.2;
  }

  @supports (color: color-mix(in oklab, black, black)){
    .btn:is(input[type="checkbox"]:checked):hover, .btn:is(input[type="radio"]:checked):hover{
      background-color: color-mix(in oklab, var(--fallback-p,oklch(var(--p)/1)) 90%, black);
      border-color: color-mix(in oklab, var(--fallback-p,oklch(var(--p)/1)) 90%, black);
    }
  }

  .dropdown.dropdown-hover:hover .dropdown-content{
    --tw-scale-x: 1;
    --tw-scale-y: 1;
    transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
  }

  :where(.menu li:not(.menu-title):not(.disabled) > *:not(ul):not(details):not(.menu-title)):not(.active):hover, :where(.menu li:not(.menu-title):not(.disabled) > details > summary:not(.menu-title)):not(.active):hover{
    cursor: pointer;
    outline: 2px solid transparent;
    outline-offset: 2px;
  }

  @supports (color: oklch(0 0 0)){
    :where(.menu li:not(.menu-title):not(.disabled) > *:not(ul):not(details):not(.menu-title)):not(.active):hover, :where(.menu li:not(.menu-title):not(.disabled) > details > summary:not(.menu-title)):not(.active):hover{
      background-color: var(--fallback-bc,oklch(var(--bc)/0.1));
    }
  }
}

.dropdown:is(details) summary::-webkit-details-marker{
  display: none;
}

.form-control{
  display: flex;
  flex-direction: column;
}

.label{
  display: flex;
  -webkit-user-select: none;
     -moz-user-select: none;
          user-select: none;
  align-items: center;
  justify-content: space-between;
  padding-left: 0.25rem;
  padding-right: 0.25rem;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

.indicator{
  position: relative;
  display: inline-flex;
  width: -moz-max-content;
  width: max-content;
}

.indicator :where(.indicator-item){
  z-index: 1;
  position: absolute;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
  white-space: nowrap;
}

.input{
  flex-shrink: 1;
  -webkit-appearance: none;
     -moz-appearance: none;
          appearance: none;
  height: 3rem;
  padding-left: 1rem;
  padding-right: 1rem;
  font-size: 1rem;
  line-height: 2;
  line-height: 1.5rem;
  border-radius: var(--rounded-btn, 0.5rem);
  border-width: 1px;
  border-color: transparent;
  --tw-bg-opacity: 1;
  background-color: var(--fallback-b1,oklch(var(--b1)/var(--tw-bg-opacity)));
}

.join{
  display: inline-flex;
  align-items: stretch;
  border-radius: var(--rounded-btn, 0.5rem);
}

.join :where(.join-item){
  border-start-end-radius: 0;
  border-end-end-radius: 0;
  border-end-start-radius: 0;
  border-start-start-radius: 0;
}

.join .join-item:not(:first-child):not(:last-child),
  .join *:not(:first-child):not(:last-child) .join-item{
  border-start-end-radius: 0;
  border-end-end-radius: 0;
  border-end-start-radius: 0;
  border-start-start-radius: 0;
}

.join .join-item:first-child:not(:last-child),
  .join *:first-child:not(:last-child) .join-item{
  border-start-end-radius: 0;
  border-end-end-radius: 0;
}

.join .dropdown .join-item:first-child:not(:last-child),
  .join *:first-child:not(:last-child) .dropdown .join-item{
  border-start-end-radius: inherit;
  border-end-end-radius: inherit;
}

.join :where(.join-item:first-child:not(:last-child)),
  .join :where(*:first-child:not(:last-child) .join-item){
  border-end-start-radius: inherit;
  border-start-start-radius: inherit;
}

.join .join-item:last-child:not(:first-child),
  .join *:last-child:not(:first-child) .join-item{
  border-end-start-radius: 0;
  border-start-start-radius: 0;
}

.join :where(.join-item:last-child:not(:first-child)),
  .join :where(*:last-child:not(:first-child) .join-item){
  border-start-end-radius: inherit;
  border-end-end-radius: inherit;
}

@supports not selector(:has(*)){
  :where(.join *){
    border-radius: inherit;
  }
}

@supports selector(:has(*)){
  :where(.join *:has(.join-item)){
    border-radius: inherit;
  }
}

.link{
  cursor: pointer;
  text-decoration-line: underline;
}

.menu{
  display: flex;
  flex-direction: column;
  flex-wrap: wrap;
  font-size: 0.875rem;
  line-height: 1.25rem;
  padding: 0.5rem;
}

.menu :where(li ul){
  position: relative;
  white-space: nowrap;
  margin-inline-start: 1rem;
  padding-inline-start: 0.5rem;
}

.menu :where(li:not(.menu-title) > *:not(ul):not(details):not(.menu-title)),
  .menu :where(li:not(.menu-title) > details > summary:not(.menu-title)){
  display: grid;
  grid-auto-flow: column;
  align-content: flex-start;
  align-items: center;
  gap: 0.5rem;
  grid-auto-columns: minmax(auto, max-content) auto max-content;
  -webkit-user-select: none;
     -moz-user-select: none;
          user-select: none;
}

.menu li.disabled{
  cursor: not-allowed;
  -webkit-user-select: none;
     -moz-user-select: none;
          user-select: none;
  color: var(--fallback-bc,oklch(var(--bc)/0.3));
}

.menu :where(li > .menu-dropdown:not(.menu-dropdown-show)){
  display: none;
}

:where(.menu li){
  position: relative;
  display: flex;
  flex-shrink: 0;
  flex-direction: column;
  flex-wrap: wrap;
  align-items: stretch;
}

:where(.menu li) .badge{
  justify-self: end;
}

.navbar{
  display: flex;
  align-items: center;
  padding: var(--navbar-padding, 0.5rem);
  min-height: 4rem;
  width: 100%;
}

:where(.navbar > *){
  display: inline-flex;
  align-items: center;
}

.navbar-start{
  width: 50%;
  justify-content: flex-start;
}

.navbar-center{
  flex-shrink: 0;
}

.navbar-end{
  width: 50%;
  justify-content: flex-end;
}

.select{
  display: inline-flex;
  cursor: pointer;
  -webkit-user-select: none;
     -moz-user-select: none;
          user-select: none;
  -webkit-appearance: none;
     -moz-appearance: none;
          appearance: none;
  height: 3rem;
  min-height: 3rem;
  padding-left: 1rem;
  padding-right: 2.5rem;
  font-size: 0.875rem;
  line-height: 1.25rem;
  line-height: 2;
  border-radius: var(--rounded-btn, 0.5rem);
  border-width: 1px;
  border-color: transparent;
  --tw-bg-opacity: 1;
  background-color: var(--fallback-b1,oklch(var(--b1)/var(--tw-bg-opacity)));
  background-image: linear-gradient(45deg, transparent 50%, currentColor 50%),
    linear-gradient(135deg, currentColor 50%, transparent 50%);
  background-position: calc(100% - 20px) calc(1px + 50%),
    calc(100% - 16.1px) calc(1px + 50%);
  background-size: 4px 4px,
    4px 4px;
  background-repeat: no-repeat;
}

.select[multiple]{
  height: auto;
}

.table{
  position: relative;
  width: 100%;
  border-radius: var(--rounded-box, 1rem);
  text-align: left;
  font-size: 0.875rem;
  line-height: 1.25rem;
}

.table :where(.table-pin-rows thead tr){
  position: sticky;
  top: 0px;
  z-index: 1;
  --tw-bg-opacity: 1;
  background-color: var(--fallback-b1,oklch(var(--b1)/var(--tw-bg-opacity)));
}

.table :where(.table-pin-rows tfoot tr){
  position: sticky;
  bottom: 0px;
  z-index: 1;
  --tw-bg-opacity: 1;
  background-color: var(--fallback-b1,oklch(var(--b1)/var(--tw-bg-opacity)));
}

.table :where(.table-pin-cols tr th){
  position: sticky;
  left: 0px;
  right: 0px;
  --tw-bg-opacity: 1;
  background-color: var(--fallback-b1,oklch(var(--b1)/var(--tw-bg-opacity)));
}

.table-zebra tbody tr:nth-child(even) :where(.table-pin-cols tr th){
  --tw-bg-opacity: 1;
  background-color: var(--fallback-b2,oklch(var(--b2)/var(--tw-bg-opacity)));
}

.btm-nav > *:where(.active){
  border-top-width: 2px;
  --tw-bg-opacity: 1;
  background-color: var(--fallback-b1,oklch(var(--b1)/var(--tw-bg-opacity)));
}

.btm-nav > *.disabled,
    .btm-nav > *[disabled]{
  pointer-events: none;
  --tw-border-opacity: 0;
  background-color: var(--fallback-n,oklch(var(--n)/var(--tw-bg-opacity)));
  --tw-bg-opacity: 0.1;
  color: var(--fallback-bc,oklch(var(--bc)/var(--tw-text-opacity)));
  --tw-text-opacity: 0.2;
}

.btm-nav > * .label{
  font-size: 1rem;
  line-height: 1.5rem;
}

.btn:active:hover,
  .btn:active:focus{
  animation: button-pop 0s ease-out;
  transform: scale(var(--btn-focus-scale, 0.97));
}

@supports not (color: oklch(0 0 0)){
  .btn{
    background-color: var(--btn-color, var(--fallback-b2));
    border-color: var(--btn-color, var(--fallback-b2));
  }

  .btn-primary{
    --btn-color: var(--fallback-p);
  }
}

@supports (color: color-mix(in oklab, black, black)){
  .btn-outline.btn-primary.btn-active{
    background-color: color-mix(in oklab, var(--fallback-p,oklch(var(--p)/1)) 90%, black);
    border-color: color-mix(in oklab, var(--fallback-p,oklch(var(--p)/1)) 90%, black);
  }
}

.btn:focus-visible{
  outline-style: solid;
  outline-width: 2px;
  outline-offset: 2px;
}

.btn-primary{
  --tw-text-opacity: 1;
  color: var(--fallback-pc,oklch(var(--pc)/var(--tw-text-opacity)));
  outline-color: var(--fallback-p,oklch(var(--p)/1));
}

@supports (color: oklch(0 0 0)){
  .btn-primary{
    --btn-color: var(--p);
  }
}

.btn.glass{
  --tw-shadow: 0 0 #0000;
  --tw-shadow-colored: 0 0 #0000;
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
  outline-color: currentColor;
}

.btn.glass.btn-active{
  --glass-opacity: 25%;
  --glass-border-opacity: 15%;
}

.btn-ghost{
  border-width: 1px;
  border-color: transparent;
  background-color: transparent;
  color: currentColor;
  --tw-shadow: 0 0 #0000;
  --tw-shadow-colored: 0 0 #0000;
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
  outline-color: currentColor;
}

.btn-ghost.btn-active{
  border-color: transparent;
  background-color: var(--fallback-bc,oklch(var(--bc)/0.2));
}

.btn-outline.btn-primary{
  --tw-text-opacity: 1;
  color: var(--fallback-p,oklch(var(--p)/var(--tw-text-opacity)));
}

.btn-outline.btn-primary.btn-active{
  --tw-text-opacity: 1;
  color: var(--fallback-pc,oklch(var(--pc)/var(--tw-text-opacity)));
}

.btn.btn-disabled,
  .btn[disabled],
  .btn:disabled{
  --tw-border-opacity: 0;
  background-color: var(--fallback-n,oklch(var(--n)/var(--tw-bg-opacity)));
  --tw-bg-opacity: 0.2;
  color: var(--fallback-bc,oklch(var(--bc)/var(--tw-text-opacity)));
  --tw-text-opacity: 0.2;
}

.btn:is(input[type="checkbox"]:checked),
.btn:is(input[type="radio"]:checked){
  --tw-border-opacity: 1;
  border-color: var(--fallback-p,oklch(var(--p)/var(--tw-border-opacity)));
  --tw-bg-opacity: 1;
  background-color: var(--fallback-p,oklch(var(--p)/var(--tw-bg-opacity)));
  --tw-text-opacity: 1;
  color: var(--fallback-pc,oklch(var(--pc)/var(--tw-text-opacity)));
}

.btn:is(input[type="checkbox"]:checked):focus-visible, .btn:is(input[type="radio"]:checked):focus-visible{
  outline-color: var(--fallback-p,oklch(var(--p)/1));
}

@keyframes button-pop{
  0%{
    transform: scale(var(--btn-focus-scale, 0.98));
  }

  40%{
    transform: scale(1.02);
  }

  100%{
    transform: scale(1);
  }
}

.checkbox:focus{
  box-shadow: none;
}

.checkbox:focus-visible{
  outline-style: solid;
  outline-width: 2px;
  outline-offset: 2px;
  outline-color: var(--fallback-bc,oklch(var(--bc)/1));
}

.checkbox:checked,
  .checkbox[checked="true"],
  .checkbox[aria-checked="true"]{
  background-repeat: no-repeat;
  animation: checkmark var(--animation-input, 0.2s) ease-out;
  background-color: var(--chkbg);
  background-image: linear-gradient(-45deg, transparent 65%, var(--chkbg) 65.99%),
      linear-gradient(45deg, transparent 75%, var(--chkbg) 75.99%),
      linear-gradient(-45deg, var(--chkbg) 40%, transparent 40.99%),
      linear-gradient(
        45deg,
        var(--chkbg) 30%,
        var(--chkfg) 30.99%,
        var(--chkfg) 40%,
        transparent 40.99%
      ),
      linear-gradient(-45deg, var(--chkfg) 50%, var(--chkbg) 50.99%);
}

.checkbox:indeterminate{
  --tw-bg-opacity: 1;
  background-color: var(--fallback-bc,oklch(var(--bc)/var(--tw-bg-opacity)));
  background-repeat: no-repeat;
  animation: checkmark var(--animation-input, 0.2s) ease-out;
  background-image: linear-gradient(90deg, transparent 80%, var(--chkbg) 80%),
      linear-gradient(-90deg, transparent 80%, var(--chkbg) 80%),
      linear-gradient(0deg, var(--chkbg) 43%, var(--chkfg) 43%, var(--chkfg) 57%, var(--chkbg) 57%);
}

.checkbox:disabled{
  cursor: not-allowed;
  border-color: transparent;
  --tw-bg-opacity: 1;
  background-color: var(--fallback-bc,oklch(var(--bc)/var(--tw-bg-opacity)));
  opacity: 0.2;
}

@keyframes checkmark{
  0%{
    background-position-y: 5px;
  }

  50%{
    background-position-y: -2px;
  }

  100%{
    background-position-y: 0;
  }
}

.dropdown.dropdown-open .dropdown-content,
.dropdown:focus .dropdown-content,
.dropdown:focus-within .dropdown-content{
  --tw-scale-x: 1;
  --tw-scale-y: 1;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

.label-text{
  font-size: 0.875rem;
  line-height: 1.25rem;
  --tw-text-opacity: 1;
  color: var(--fallback-bc,oklch(var(--bc)/var(--tw-text-opacity)));
}

.input input:focus{
  outline: 2px solid transparent;
  outline-offset: 2px;
}

.input[list]::-webkit-calendar-picker-indicator{
  line-height: 1em;
}

.input-bordered{
  border-color: var(--fallback-bc,oklch(var(--bc)/0.2));
}

.input:focus,
  .input:focus-within{
  box-shadow: none;
  border-color: var(--fallback-bc,oklch(var(--bc)/0.2));
  outline-style: solid;
  outline-width: 2px;
  outline-offset: 2px;
  outline-color: var(--fallback-bc,oklch(var(--bc)/0.2));
}

.input-ghost{
  --tw-bg-opacity: 0.05;
}

.input-ghost:focus,
    .input-ghost:focus-within{
  --tw-bg-opacity: 1;
  --tw-text-opacity: 1;
  color: var(--fallback-bc,oklch(var(--bc)/var(--tw-text-opacity)));
  box-shadow: none;
}

.input-disabled,
  .input:disabled,
  .input[disabled]{
  cursor: not-allowed;
  --tw-border-opacity: 1;
  border-color: var(--fallback-b2,oklch(var(--b2)/var(--tw-border-opacity)));
  --tw-bg-opacity: 1;
  background-color: var(--fallback-b2,oklch(var(--b2)/var(--tw-bg-opacity)));
  color: var(--fallback-bc,oklch(var(--bc)/0.4));
}

.input-disabled::-moz-placeholder, .input:disabled::-moz-placeholder, .input[disabled]::-moz-placeholder{
  color: var(--fallback-bc,oklch(var(--bc)/var(--tw-placeholder-opacity)));
  --tw-placeholder-opacity: 0.2;
}

.input-disabled::placeholder,
  .input:disabled::placeholder,
  .input[disabled]::placeholder{
  color: var(--fallback-bc,oklch(var(--bc)/var(--tw-placeholder-opacity)));
  --tw-placeholder-opacity: 0.2;
}

.input::-webkit-date-and-time-value{
  text-align: inherit;
}

.join > :where(*:not(:first-child)){
  margin-top: 0px;
  margin-bottom: 0px;
  margin-inline-start: -1px;
}

.join-item:focus{
  isolation: isolate;
}

.link:focus{
  outline: 2px solid transparent;
  outline-offset: 2px;
}

.link:focus-visible{
  outline: 2px solid currentColor;
  outline-offset: 2px;
}

:where(.menu li:empty){
  --tw-bg-opacity: 1;
  background-color: var(--fallback-bc,oklch(var(--bc)/var(--tw-bg-opacity)));
  opacity: 0.1;
  margin: 0.5rem 1rem;
  height: 1px;
}

.menu :where(li ul):before{
  position: absolute;
  bottom: 0.75rem;
  inset-inline-start: 0px;
  top: 0.75rem;
  width: 1px;
  --tw-bg-opacity: 1;
  background-color: var(--fallback-bc,oklch(var(--bc)/var(--tw-bg-opacity)));
  opacity: 0.1;
  content: "";
}

.menu :where(li:not(.menu-title) > *:not(ul):not(details):not(.menu-title)),
.menu :where(li:not(.menu-title) > details > summary:not(.menu-title)){
  border-radius: var(--rounded-btn, 0.5rem);
  padding-left: 1rem;
  padding-right: 1rem;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  text-align: start;
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, -webkit-backdrop-filter;
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter, -webkit-backdrop-filter;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-timing-function: cubic-bezier(0, 0, 0.2, 1);
  transition-duration: 200ms;
  text-wrap: balance;
}

:where(.menu li:not(.menu-title):not(.disabled) > *:not(ul):not(details):not(.menu-title)):not(summary):not(.active).focus,
  :where(.menu li:not(.menu-title):not(.disabled) > *:not(ul):not(details):not(.menu-title)):not(summary):not(.active):focus,
  :where(.menu li:not(.menu-title):not(.disabled) > *:not(ul):not(details):not(.menu-title)):is(summary):not(.active):focus-visible,
  :where(.menu li:not(.menu-title):not(.disabled) > details > summary:not(.menu-title)):not(summary):not(.active).focus,
  :where(.menu li:not(.menu-title):not(.disabled) > details > summary:not(.menu-title)):not(summary):not(.active):focus,
  :where(.menu li:not(.menu-title):not(.disabled) > details > summary:not(.menu-title)):is(summary):not(.active):focus-visible{
  cursor: pointer;
  background-color: var(--fallback-bc,oklch(var(--bc)/0.1));
  --tw-text-opacity: 1;
  color: var(--fallback-bc,oklch(var(--bc)/var(--tw-text-opacity)));
  outline: 2px solid transparent;
  outline-offset: 2px;
}

.menu li > *:not(ul):not(.menu-title):not(details):active,
.menu li > *:not(ul):not(.menu-title):not(details).active,
.menu li > details > summary:active{
  --tw-bg-opacity: 1;
  background-color: var(--fallback-n,oklch(var(--n)/var(--tw-bg-opacity)));
  --tw-text-opacity: 1;
  color: var(--fallback-nc,oklch(var(--nc)/var(--tw-text-opacity)));
}

.menu :where(li > details > summary)::-webkit-details-marker{
  display: none;
}

.menu :where(li > details > summary):after,
.menu :where(li > .menu-dropdown-toggle):after{
  justify-self: end;
  display: block;
  margin-top: -0.5rem;
  height: 0.5rem;
  width: 0.5rem;
  transform: rotate(45deg);
  transition-property: transform, margin-top;
  transition-duration: 0.3s;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  content: "";
  transform-origin: 75% 75%;
  box-shadow: 2px 2px;
  pointer-events: none;
}

.menu :where(li > details[open] > summary):after,
.menu :where(li > .menu-dropdown-toggle.menu-dropdown-show):after{
  transform: rotate(225deg);
  margin-top: 0;
}

.mockup-phone .display{
  overflow: hidden;
  border-radius: 40px;
  margin-top: -25px;
}

.mockup-browser .mockup-browser-toolbar .input{
  position: relative;
  margin-left: auto;
  margin-right: auto;
  display: block;
  height: 1.75rem;
  width: 24rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  --tw-bg-opacity: 1;
  background-color: var(--fallback-b2,oklch(var(--b2)/var(--tw-bg-opacity)));
  padding-left: 2rem;
  direction: ltr;
}

.mockup-browser .mockup-browser-toolbar .input:before{
  content: "";
  position: absolute;
  left: 0.5rem;
  top: 50%;
  aspect-ratio: 1 / 1;
  height: 0.75rem;
  --tw-translate-y: -50%;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
  border-radius: 9999px;
  border-width: 2px;
  border-color: currentColor;
  opacity: 0.6;
}

.mockup-browser .mockup-browser-toolbar .input:after{
  content: "";
  position: absolute;
  left: 1.25rem;
  top: 50%;
  height: 0.5rem;
  --tw-translate-y: 25%;
  --tw-rotate: -45deg;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
  border-radius: 9999px;
  border-width: 1px;
  border-color: currentColor;
  opacity: 0.6;
}

@keyframes modal-pop{
  0%{
    opacity: 0;
  }
}

@keyframes progress-loading{
  50%{
    background-position-x: -115%;
  }
}

@keyframes radiomark{
  0%{
    box-shadow: 0 0 0 12px var(--fallback-b1,oklch(var(--b1)/1)) inset,
      0 0 0 12px var(--fallback-b1,oklch(var(--b1)/1)) inset;
  }

  50%{
    box-shadow: 0 0 0 3px var(--fallback-b1,oklch(var(--b1)/1)) inset,
      0 0 0 3px var(--fallback-b1,oklch(var(--b1)/1)) inset;
  }

  100%{
    box-shadow: 0 0 0 4px var(--fallback-b1,oklch(var(--b1)/1)) inset,
      0 0 0 4px var(--fallback-b1,oklch(var(--b1)/1)) inset;
  }
}

@keyframes rating-pop{
  0%{
    transform: translateY(-0.125em);
  }

  40%{
    transform: translateY(-0.125em);
  }

  100%{
    transform: translateY(0);
  }
}

.select-bordered{
  border-color: var(--fallback-bc,oklch(var(--bc)/0.2));
}

.select:focus{
  box-shadow: none;
  border-color: var(--fallback-bc,oklch(var(--bc)/0.2));
  outline-style: solid;
  outline-width: 2px;
  outline-offset: 2px;
  outline-color: var(--fallback-bc,oklch(var(--bc)/0.2));
}

.select-ghost{
  --tw-bg-opacity: 0.05;
}

.select-ghost:focus{
  --tw-bg-opacity: 1;
  --tw-text-opacity: 1;
  color: var(--fallback-bc,oklch(var(--bc)/var(--tw-text-opacity)));
}

.select-disabled,
  .select:disabled,
  .select[disabled]{
  cursor: not-allowed;
  --tw-border-opacity: 1;
  border-color: var(--fallback-b2,oklch(var(--b2)/var(--tw-border-opacity)));
  --tw-bg-opacity: 1;
  background-color: var(--fallback-b2,oklch(var(--b2)/var(--tw-bg-opacity)));
  --tw-text-opacity: 0.2;
}

.select-disabled::-moz-placeholder, .select:disabled::-moz-placeholder, .select[disabled]::-moz-placeholder{
  color: var(--fallback-bc,oklch(var(--bc)/var(--tw-placeholder-opacity)));
  --tw-placeholder-opacity: 0.2;
}

.select-disabled::placeholder,
  .select:disabled::placeholder,
  .select[disabled]::placeholder{
  color: var(--fallback-bc,oklch(var(--bc)/var(--tw-placeholder-opacity)));
  --tw-placeholder-opacity: 0.2;
}

.select-multiple,
  .select[multiple],
  .select[size].select:not([size="1"]){
  background-image: none;
  padding-right: 1rem;
}

[dir="rtl"] .select{
  background-position: calc(0% + 12px) calc(1px + 50%),
    calc(0% + 16px) calc(1px + 50%);
}

@keyframes skeleton{
  from{
    background-position: 150%;
  }

  to{
    background-position: -50%;
  }
}

:is([dir="rtl"] .table){
  text-align: right;
}

.table :where(th, td){
  padding-left: 1rem;
  padding-right: 1rem;
  padding-top: 0.75rem;
  padding-bottom: 0.75rem;
  vertical-align: middle;
}

.table tr.active,
  .table tr.active:nth-child(even),
  .table-zebra tbody tr:nth-child(even){
  --tw-bg-opacity: 1;
  background-color: var(--fallback-b2,oklch(var(--b2)/var(--tw-bg-opacity)));
}

.table-zebra tr.active,
    .table-zebra tr.active:nth-child(even),
    .table-zebra-zebra tbody tr:nth-child(even){
  --tw-bg-opacity: 1;
  background-color: var(--fallback-b3,oklch(var(--b3)/var(--tw-bg-opacity)));
}

.table :where(thead, tbody) :where(tr:not(:last-child)),
    .table :where(thead, tbody) :where(tr:first-child:last-child){
  border-bottom-width: 1px;
  --tw-border-opacity: 1;
  border-bottom-color: var(--fallback-b2,oklch(var(--b2)/var(--tw-border-opacity)));
}

.table :where(thead, tfoot){
  white-space: nowrap;
  font-size: 0.75rem;
  line-height: 1rem;
  font-weight: 700;
  color: var(--fallback-bc,oklch(var(--bc)/0.6));
}

@keyframes toast-pop{
  0%{
    transform: scale(0.9);
    opacity: 0;
  }

  100%{
    transform: scale(1);
    opacity: 1;
  }
}

.btm-nav-xs > *:where(.active){
  border-top-width: 1px;
}

.btm-nav-sm > *:where(.active){
  border-top-width: 2px;
}

.btm-nav-md > *:where(.active){
  border-top-width: 2px;
}

.btm-nav-lg > *:where(.active){
  border-top-width: 4px;
}

.btn-circle:where(.btn-xs){
  height: 1.5rem;
  width: 1.5rem;
  border-radius: 9999px;
  padding: 0px;
}

.btn-circle:where(.btn-sm){
  height: 2rem;
  width: 2rem;
  border-radius: 9999px;
  padding: 0px;
}

.btn-circle:where(.btn-md){
  height: 3rem;
  width: 3rem;
  border-radius: 9999px;
  padding: 0px;
}

.btn-circle:where(.btn-lg){
  height: 4rem;
  width: 4rem;
  border-radius: 9999px;
  padding: 0px;
}

.indicator :where(.indicator-item){
  bottom: auto;
  inset-inline-end: 0px;
  inset-inline-start: auto;
  top: 0px;
  --tw-translate-y: -50%;
  --tw-translate-x: 50%;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

:is([dir="rtl"] .indicator :where(.indicator-item)){
  --tw-translate-x: -50%;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

.indicator :where(.indicator-item.indicator-start){
  inset-inline-end: auto;
  inset-inline-start: 0px;
  --tw-translate-x: -50%;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

:is([dir="rtl"] .indicator :where(.indicator-item.indicator-start)){
  --tw-translate-x: 50%;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

.indicator :where(.indicator-item.indicator-center){
  inset-inline-end: 50%;
  inset-inline-start: 50%;
  --tw-translate-x: -50%;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

:is([dir="rtl"] .indicator :where(.indicator-item.indicator-center)){
  --tw-translate-x: 50%;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

.indicator :where(.indicator-item.indicator-end){
  inset-inline-end: 0px;
  inset-inline-start: auto;
  --tw-translate-x: 50%;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

:is([dir="rtl"] .indicator :where(.indicator-item.indicator-end)){
  --tw-translate-x: -50%;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

.indicator :where(.indicator-item.indicator-bottom){
  bottom: 0px;
  top: auto;
  --tw-translate-y: 50%;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

.indicator :where(.indicator-item.indicator-middle){
  bottom: 50%;
  top: 50%;
  --tw-translate-y: -50%;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

.indicator :where(.indicator-item.indicator-top){
  bottom: auto;
  top: 0px;
  --tw-translate-y: -50%;
  transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

.join.join-vertical{
  flex-direction: column;
}

.join.join-vertical .join-item:first-child:not(:last-child),
  .join.join-vertical *:first-child:not(:last-child) .join-item{
  border-end-start-radius: 0;
  border-end-end-radius: 0;
  border-start-start-radius: inherit;
  border-start-end-radius: inherit;
}

.join.join-vertical .join-item:last-child:not(:first-child),
  .join.join-vertical *:last-child:not(:first-child) .join-item{
  border-start-start-radius: 0;
  border-start-end-radius: 0;
  border-end-start-radius: inherit;
  border-end-end-radius: inherit;
}

.join.join-horizontal{
  flex-direction: row;
}

.join.join-horizontal .join-item:first-child:not(:last-child),
  .join.join-horizontal *:first-child:not(:last-child) .join-item{
  border-end-end-radius: 0;
  border-start-end-radius: 0;
  border-end-start-radius: inherit;
  border-start-start-radius: inherit;
}

.join.join-horizontal .join-item:last-child:not(:first-child),
  .join.join-horizontal *:last-child:not(:first-child) .join-item{
  border-end-start-radius: 0;
  border-start-start-radius: 0;
  border-end-end-radius: inherit;
  border-start-end-radius: inherit;
}

.join.join-vertical > :where(*:not(:first-child)){
  margin-left: 0px;
  margin-right: 0px;
  margin-top: -1px;
}

.join.join-horizontal > :where(*:not(:first-child)){
  margin-top: 0px;
  margin-bottom: 0px;
  margin-inline-start: -1px;
}

.menu-sm :where(li:not(.menu-title) > *:not(ul):not(details):not(.menu-title)),
  .menu-sm :where(li:not(.menu-title) > details > summary:not(.menu-title)){
  border-radius: var(--rounded-btn, 0.5rem);
  padding-left: 0.75rem;
  padding-right: 0.75rem;
  padding-top: 0.25rem;
  padding-bottom: 0.25rem;
  font-size: 0.875rem;
  line-height: 1.25rem;
}

.menu-sm .menu-title{
  padding-left: 0.75rem;
  padding-right: 0.75rem;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

.relative{
  position: relative;
}

.z-\[1\]{
  z-index: 1;
}

.my-auto{
  margin-top: auto;
  margin-bottom: auto;
}

.mb-1{
  margin-bottom: 0.25rem;
}

.mb-3{
  margin-bottom: 0.75rem;
}

.ml-2{
  margin-left: 0.5rem;
}

.mr-2{
  margin-right: 0.5rem;
}

.mt-3{
  margin-top: 0.75rem;
}

.block{
  display: block;
}

.flex{
  display: flex;
}

.table{
  display: table;
}

.grid{
  display: grid;
}

.h-5{
  height: 1.25rem;
}

.h-7{
  height: 1.75rem;
}

.h-svh{
  height: 100svh;
}

.w-5{
  width: 1.25rem;
}

.w-52{
  width: 13rem;
}

.w-7{
  width: 1.75rem;
}

.w-full{
  width: 100%;
}

.w-svw{
  width: 100svw;
}

.min-w-\[200px\]{
  min-width: 200px;
}

.max-w-xs{
  max-width: 20rem;
}

.flex-1{
  flex: 1 1 0%;
}

.cursor-pointer{
  cursor: pointer;
}

.flex-wrap{
  flex-wrap: wrap;
}

.items-end{
  align-items: flex-end;
}

.items-center{
  align-items: center;
}

.justify-center{
  justify-content: center;
}

.gap-1{
  gap: 0.25rem;
}

.gap-3{
  gap: 0.75rem;
}

.overflow-x-auto{
  overflow-x: auto;
}

.rounded-box{
  border-radius: var(--rounded-box, 1rem);
}

.rounded-lg{
  border-radius: 0.5rem;
}

.rounded-none{
  border-radius: 0px;
}

.rounded-xl{
  border-radius: 0.75rem;
}

.border-b{
  border-bottom-width: 1px;
}

.border-r{
  border-right-width: 1px;
}

.border-base-200{
  --tw-border-opacity: 1;
  border-color: var(--fallback-b2,oklch(var(--b2)/var(--tw-border-opacity)));
}

.border-primary{
  --tw-border-opacity: 1;
  border-color: var(--fallback-p,oklch(var(--p)/var(--tw-border-opacity)));
}

.bg-base-100{
  --tw-bg-opacity: 1;
  background-color: var(--fallback-b1,oklch(var(--b1)/var(--tw-bg-opacity)));
}

.p-0{
  padding: 0px;
}

.p-2{
  padding: 0.5rem;
}

.px-1{
  padding-left: 0.25rem;
  padding-right: 0.25rem;
}

.px-2{
  padding-left: 0.5rem;
  padding-right: 0.5rem;
}

.px-4{
  padding-left: 1rem;
  padding-right: 1rem;
}

.py-2{
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

.py-3{
  padding-top: 0.75rem;
  padding-bottom: 0.75rem;
}

.pb-1{
  padding-bottom: 0.25rem;
}

.text-center{
  text-align: center;
}

.text-right{
  text-align: right;
}

.text-3xl{
  font-size: 1.875rem;
  line-height: 2.25rem;
}

.text-lg{
  font-size: 1.125rem;
  line-height: 1.75rem;
}

.text-xl{
  font-size: 1.25rem;
  line-height: 1.75rem;
}

.text-xs{
  font-size: 0.75rem;
  line-height: 1rem;
}

.font-bold{
  font-weight: 700;
}

.font-medium{
  font-weight: 500;
}

.capitalize{
  text-transform: capitalize;
}

.shadow{
  --tw-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
  --tw-shadow-colored: 0 1px 3px 0 var(--tw-shadow-color), 0 1px 2px -1px var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}

.shadow-lg{
  --tw-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
  --tw-shadow-colored: 0 10px 15px -3px var(--tw-shadow-color), 0 4px 6px -4px var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}

.shadow-sm{
  --tw-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}

.input-suggestions-dd {
  display: none;
  position: absolute;
  top: 100%;
  width: 100%;
  z-index: 1;
  max-height: 500px;
  overflow-y: auto;
  --tw-bg-opacity: 1;
  background-color: var(--fallback-b2,oklch(var(--b2)/var(--tw-bg-opacity)));
  --tw-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
}

.select-dropdown.active {
  display: block;
}

.input-suggestion {
  padding: 0.3em 0.5em;
}

.input-suggestion:hover,
.input-suggestion.active{
  --tw-bg-opacity: 1;
  background-color: var(--fallback-b3,oklch(var(--b3)/var(--tw-bg-opacity)));
  --tw-text-opacity: 1;
  color: var(--fallback-bc,oklch(var(--bc)/var(--tw-text-opacity)));
}

.hover\:bg-base-200:hover{
  --tw-bg-opacity: 1;
  background-color: var(--fallback-b2,oklch(var(--b2)/var(--tw-bg-opacity)));
}

.hover\:bg-primary:hover{
  --tw-bg-opacity: 1;
  background-color: var(--fallback-p,oklch(var(--p)/var(--tw-bg-opacity)));
}

.hover\:text-primary-content:hover{
  --tw-text-opacity: 1;
  color: var(--fallback-pc,oklch(var(--pc)/var(--tw-text-opacity)));
}

@media (min-width: 640px){
  .sm\:min-w-\[250px\]{
    min-width: 250px;
  }

  .sm\:justify-between{
    justify-content: space-between;
  }
}

@media (min-width: 768px){
  .md\:max-w-sm{
    max-width: 24rem;
  }

  .md\:px-12{
    padding-left: 3rem;
    padding-right: 3rem;
  }

  .md\:px-5{
    padding-left: 1.25rem;
    padding-right: 1.25rem;
  }

  .md\:px-7{
    padding-left: 1.75rem;
    padding-right: 1.75rem;
  }

  .md\:py-4{
    padding-top: 1rem;
    padding-bottom: 1rem;
  }

  .md\:py-8{
    padding-top: 2rem;
    padding-bottom: 2rem;
  }
}
        button.join-item.btn.btn-active {
            background-color: color-mix(in oklab, oklch(var(--btn-color, var(--b2)) / var(--tw-bg-opacity, 1)) 90%, black);
            border-color: color-mix(in oklab, oklch(var(--btn-color, var(--b2)) / var(--tw-border-opacity, 1)) 90%, black);
        }
.join {
	display: initial;
}
@media screen and (max-width: 833px) {
    #loged_user {
        margin-top: -10px !important;
    }
}
select#cost_center {
    background: #fff !important;
    border: 1px solid #c1c1c1;
}
.select2Parent, .select2Parent1, .select2Parent2, .select2Parent3, .select2Parent4, .select2Parent5, .select2Parent6, .select2Parent7, .select2Parent8, .select2Parent9, .select2Parent10 {
        position: relative !important;
    }
span#select2-cost_center-container {
    margin-top: -15px;
    font-size: 16px;
    text-align: start;
}

span.select2-selection.select2-selection--single {
    padding: 23px;
}
ul#select2-cost_center-results {
    text-align: left;
}

span.select2.select2-container.select2-container--default.select2-container--below {
            width: 100% !important;
        }

        span.select2.select2-container.select2-container--default.select2-container--focus {
            width: 100% !important;
        }

        span.select2-selection.select2-selection--single {
            padding: 0px 0px 12px 0px !important;
            margin-top: 2px !important;
            border-color: #c9c9c9 !important;
        }

        .select2-container .select2-selection--single {
            height: 48px;
        }

        span.select2-selection.select2-selection--single {
            padding: 0px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 48px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 48px;
        }
	span#select2-cost_center-container {
	    margin-top: 0px !important;
	}

    </style>
</head>
<body>
<div class="bg-base-200" style="height:fit-content;">
    <div class="main-sec" style="width:100%;height:100%">
<div class="navbar bg-base-100 shadow-sm">
    <div class="navbar-start">
        
    </div>
    <div class="navbar-center">
        <a class="btn btn-ghost text-xl">LC report</a>
    </div>
    <div class="navbar-end">
        
    </div>
</div>
<div class="px-4 py-2 md:px-12 md:py-4" style="background:#fff">
    <?php

    // Database credentials
    $hostname = 'localhost';
    $username = 'root';
    $password = ''; // No password
    $database = 'testdb'; // Replace with your actual database name

    // Create a connection
    $connection = mysqli_connect($hostname, $username, $password, $database);

    // Check the connection
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $total_dr = 0;
    $total_cr = 0;

    $from_date = "";
    $to_date = "";
    $description = "";
    $cost_center = "";
    $from = 0;
    $to = 500;
    $page = 1;
    $slFrom = 0;

    $isSearch = false;

      $project_id = "P0005";
      if (isset($_SESSION["project_id"])){
          $project_id = $_SESSION["project_id"];
      }

    if (isset($_SERVER["REQUEST_METHOD"]) && isset($_REQUEST['submit']) && $_SERVER["REQUEST_METHOD"] == "POST") {
        $from_date = isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : "";
        $to_date = isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : "";
        $description = isset($_REQUEST['lc_number']) ? $_REQUEST['lc_number'] : "";
        $cost_center = isset($_REQUEST['cost_center']) ? $_REQUEST['cost_center'] : "";

        if (isset($_REQUEST['from'])) {
            $from = is_numeric($_REQUEST['from']) ? (int)$_REQUEST['from'] : $from;
        }
        if (isset($_REQUEST['to'])) {
            $to = is_numeric($_REQUEST['to']) ? (int)$_REQUEST['to'] : $to;
        }
        if (isset($_REQUEST['page'])) {
            $page = is_numeric($_REQUEST['page']) ? (int)$_REQUEST['page'] : $page;
        }

	if ($description != "" || $cost_center != "") {
            $isSearch = true;
        }
    }

    // SQL query
    $sql = "SELECT * 
            FROM sub_acc_head 
            INNER JOIN account_journal ON sub_acc_head.sub_id = account_journal.sub_id 
            WHERE account_journal.project_id='$project_id' AND sub_acc_head.cost_center_required='1'";

    if ($description != "" && $cost_center == "") {
        $sql .= " AND account_journal.description LIKE 'lc%$description'";
    }elseif ($description == "" && $cost_center != "") {
        $sql .= " AND account_journal.cost_center='$cost_center'";
    }elseif ($description != "" && $cost_center != "") {
        $sql .= " AND (account_journal.cost_center='$cost_center' OR account_journal.description LIKE 'lc%$description')";
    }else{
	$sql .= " AND account_journal.description LIKE 'lc%$description'";
    }

    if ($from_date != "" && $to_date == "") {
        $sql .= " AND account_journal.created_date >= '$from_date'";
    } elseif ($from_date == "" && $to_date != "") {
        $sql .= " AND account_journal.created_date <= '$to_date'";
    } elseif ($from_date != "" && $to_date != "") {
        $sql .= " AND account_journal.created_date BETWEEN '$from_date' AND '$to_date'";
    }

    // Execute the query
    $totalResult = mysqli_query($connection, $sql);

    if(!$isSearch){
        $sql .= " LIMIT $from,$to";
    }

    $slFrom = $from;

    $result = mysqli_query($connection, $sql);

    // Check if the query was successful
    if (!$result) {
        die("Query failed: " . mysqli_error($connection));
    }


    ?>
    <h1 class="text-3xl font-bold mb-3"></h1>
    <form
            id="lcForm"
            action="<?php echo "?app=sales.report&cmd=show_lc_report"; ?>"
            method="POST"
            class="px-1 py-2 md:px-7 md:py-8 shadow-lg rounded-xl flex items-end flex-wrap gap-3 ml-2"
    >
        <label class="form-control w-full max-w-xs md:max-w-sm">
            <div class="label">
                <span class="label-text font-bold">From</span>
            </div>
            <input
                    id="from_date"
                    name="from_date"
                    value="<?php echo isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : '' ?>"
                    type="date"
                    class="input input-ghost input-bordered hover:bg-base-200 w-full max-w-xs md:max-w-sm"
            />

            <input type="hidden" id="from" name="from"
                   value="0"/>
            <input type="hidden" id="to" name="to"
                   value=""/>
            <input type="hidden" id="page" name="page"
                   value=""/>
        </label>
        <label class="form-control w-full max-w-xs md:max-w-sm">
            <div class="label">
                <span class="label-text font-bold">To</span>
            </div>
            <input
                    id="to_date"
                    name="to_date"
                    type="date"
                    value="<?php echo isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : '' ?>"
                    class="input input-ghost input-bordered hover:bg-base-200 w-full max-w-xs md:max-w-sm"
            />
        </label>
	<label class="form-control flex-1 relative select2Parent2">
            <div class="label">
                <span class="label-text font-bold">Cost Center</span>
            </div>
            <select class="custom-select cost_center" id="cost_center" name="cost_center">
                <option value="">Select Cost Center</option>
		<?php
                        //$costsql = "SELECT DISTINCT cost_center FROM account_journal WHERE cost_center IS NOT NULL AND cost_center != ''";
                        $costsql = "SELECT DISTINCT sub_id,code,sub_head_name FROM sub_acc_head WHERE head_type = 'Cost Center'";
                        $costRes = mysqli_query($connection, $costsql);
                        if ($costRes->num_rows > 0) {
                            while ($row = mysqli_fetch_assoc($costRes)) {
                                $sub_id = $row['sub_id'];
                                $sub_head_name = $row['sub_head_name'];
                                $code = $row['code'];
				$name = !empty($row['code']) ? $row['code'] ."::". $row['sub_head_name'] : $row['sub_head_name'];
                                ?>
                                <option value="<?=$sub_id?>" <?php if($cost_center==$sub_id){echo "selected";}?>><?=$name?></option>
                                <?php
                            }
                        }
                        ?>
            </select>
        </label>
        <label class="form-control flex-1 relative">
            <div class="label">
                <span class="label-text font-bold">LC Number</span>
            </div>
            <input
                    id="lc_number"
                    name="lc_number"
                    value="<?php echo isset($description) ? $description : '' ?>"
                    type="text"
                    placeholder="Type here"
                    class="input input-ghost input-bordered hover:bg-base-200 w-full"
            />
            <!--            <div class="input-suggestions-dd active">-->
            <!--                 add all the batch list -->
            <!--                <div class="input-suggestion">have to find</div>-->
            <!--                <div class="input-suggestion">taddakdd</div>-->
            <!--                <div class="input-suggestion">hello, world</div>-->
            <!--            </div>-->
        </label>
        <button name="submit" type="submit" class="btn btn-primary rounded-lg">Submit
        </button>
    </form>
    <div class="py-3 md:py-8 px-2 md:px-5 mt-3 rounded-lg shadow-lg ml-2">
	<?php
	    if(!$isSearch){
        
	?>
        <div
                class="flex items-center justify-center sm:justify-between gap-3 flex-wrap mb-3"
        >
            <h2 class="text-xl font-bold mb-3"></h2>
            <div class="join">
                <?php

                $totalrecord = $totalResult->num_rows;

                $block = $to;

                $callFunc = "nextPage";

                $from_rs = $page;
                if ($from_rs == "") {
                    $from_rs = 0;
                }
                if ($block == "") {
                    $block = 12;
                }
                $to_rs = (int)$from_rs + $block;
                if ($from_rs >= $block) {
                    $from_rs = $from_rs + 1;
                }
                if ($from_rs == "" || $from_rs == 0) {
                    $from_rs = 1;
                }
                if ($to_rs == "" || $totalrecord < $block) {
                    $to_rs = $totalrecord;
                } else if ($to_rs == "" && $totalrecord > $block) {
                    $to_rs = $block;
                }
                if ($to_rs > $totalrecord) {
                    $to_rs = $totalrecord;
                }
                if ($totalrecord == 0) {
                    $from_rs = 0;
                }

                $plink = $page;
                if ($plink == "") {
                    $plink = 1;
                }
                if ($totalrecord > $block) {
                    $res = $totalrecord / $block;
                    $res = (int)$res;
                    if (($totalrecord % $block) != 0) {
                        $totalpage = $res + 1;
                    } else {
                        $totalpage = $res;
                    }
                } else {
                    $totalpage = 1;
                }
                $paginationStr = "";

                if ($totalrecord > $block) {
                    $two = $from;
                    if ($two == "") {
                        $two = 0;
                    }
                    $pno = $page;
                    if ($pno == "") {
                        $pno = 0;
                    }
                    $pno = $pno - 1;
                    $frm = $two - $block;
                    $to = $block;
                    if ($pno <= $totalpage && $pno > 0) {
                        $paginationStr .= "<button class='join-item btn' onclick=" . $callFunc . "($frm,$to,$pno) >&laquo;</button>";
                    }
                } else {
                    $paginationStr .= "<button class='join-item btn' disabled>&laquo;</button>";
                }
                if ($totalpage >= 1) {
                    $i = 1;
                    $from = 0;
                    $to = $block;
                    while ($i <= $totalpage) {
                        if ($from == 0) {
                            $paginationStr .= "<button class='join-item btn";
                            if ($i == $plink) {
                                $paginationStr .= " btn-active";
                            }
                            $paginationStr .= "' ";
                            $paginationStr .= "onclick=" . $callFunc . "($from,$to,$i)>$i";
                            $paginationStr .= "</button>";
                        } else {
                            $paginationStr .= "<button class='join-item btn";
                            if ($i == $plink) {
                                $paginationStr .= " btn-active";
                            }
                            $paginationStr .= "' ";
                            $paginationStr .= "onclick=" . $callFunc . "($from,$to,$i)>$i";
                            $paginationStr .= "</button>";
                        }
                        $i++;
                        $from = $from + $block;
                        if ($to > $totalrecord) {
                            $to = $totalrecord;
                        }
                    }
                }
                if ($totalrecord > $block) {
                    $f = $from;
                    $page = (int)$page + 1;
                    if ($f == "" || $f == 0) {
                        $f = $block;
                        $page = 2;
                    } else {
                        $f = $f + $block;
                    }
                    $t = $block;
                    if ($t > $totalrecord) {
                        $t = $totalrecord;
                    }
                    if ($page <= $totalpage) {
                        $paginationStr .= "<button class='join-item btn' onclick=" . $callFunc . "($f,$t,$page) >&raquo;</button>";
                    }
                } else {
                    $paginationStr .= "<button class='join-item btn' disabled>&raquo;</button>";
                }

                echo $paginationStr;
                ?>
                <!--                <button-->
                <!--                        class="join-item btn hover:bg-primary hover:text-primary-content"-->
                <!--                >-->
                <!--                    «-->
                <!--                </button>-->
                <!--                <div class="dropdown join-item">-->
                <!--                    <div tabindex="0" role="button" class="btn rounded-none">-->
                <!--                        page 1-->
                <!--                    </div>-->
                <!--                    <ul-->
                <!--                            tabindex="0"-->
                <!--                            class="dropdown-content z-[1] menu shadow bg-base-100 p-0 w-full"-->
                <!--                    >-->
                <!--                        <li><a class="text-center">page 2</a></li>-->
                <!--                        <li><a class="text-center">page 3</a></li>-->
                <!--                        <li><a class="text-center">page 4</a></li>-->
                <!--                        <li><a class="text-center">page 5</a></li>-->
                <!--                    </ul>-->
                <!--                </div>-->
                <!--                <button-->
                <!--                        class="join-item btn hover:bg-primary hover:text-primary-content"-->
                <!--                >-->
                <!--                    »-->
                <!--                </button>-->
            </div>
        </div>
	<?php
	    }
        
	?>
        <div class="text-center font-medium">
            <h2 class="text-xl font-bold mb-3">LC Transaction List</h2>
            <div class="text-lg">Heritage Polymer & Lami Tubes Ltd.</div>
            <div>Gulshan-1, Dhaka, bangladesh</div>
        </div>
        <div class="overflow-x-auto mt-3">
            <table class="table table-zebra">
                <!-- head -->
                <thead>
                <tr>
                    <th>SL.</th>
                    <th>Date</th>
                    <th>Voucher No.</th>
                    <th>Voucher Type</th>
                    <th>Account Name</th>
                    <th>LC Number</th>
                    <th>Debit (BDT)</th>
                    <th>Credit (BDT)</th>
                    <th>Balance</th>
                    <th class="text-center">Created By</th>
                    <!--                    <th class="text-center">Action</th>-->
                </tr>
                </thead>
                <tbody>
                <?php
                // Close the connection
                mysqli_close($connection);


                // Check if the query was successful
                if ($result->num_rows <= 0) {
                    ?>
                    <tr>
                        <td colspan="9" class="text-center">Record not found</td>
                    </tr>
                    <?php
                } else {
		$i = $slFrom + 1;
                    // Fetch and display the results
                    while ($row = mysqli_fetch_assoc($result)) {
                        $total_dr += $row['dr'];
                        $total_cr += $row['cr'];
			$balance = (float)$row['dr'] - (float)$row['cr'];
			if($balance < 0){
				$balance = abs($balance) . " (Cr)";
			}else{
				$balance = $balance . " (Dr)";
			}
                        ?>

                        <tr>
                            <th><?php echo $i++; ?></th>
                            <th><?php echo $row['created_date']; ?></th>
                            <td><?php echo $row['voucher_no']; ?></td>
                            <td>
                                <div><?php echo $row['head_type']; ?></div>
                            </td>
                            <td>
                                <div><?php echo $row['sub_head_name']; ?></div>

                            </td>
                            <td>
                                <div><?php echo $row['description']; ?></div>
                            </td>
                            <td>
                                <div><?php echo $row['dr']; ?></div>

                            </td>
                            <td>
                                <div><?php echo $row['cr']; ?></div>

                            </td>
                            <td>
                                <div><?php echo $balance; ?></div>

                            </td>
                            <td>
                                <div><?php echo $row['created_by']; ?></div>

                            </td>
                            <!--                            <td>-->
                            <!--                                <div class="flex items-center justify-center gap-1">-->
                            <!--                                    <button class="btn btn-ghost">-->
                            <!--                                        <svg-->
                            <!--                                                xmlns="http://www.w3.org/2000/svg"-->
                            <!--                                                fill="none"-->
                            <!--                                                viewBox="0 0 24 24"-->
                            <!--                                                stroke-width="1.5"-->
                            <!--                                                stroke="currentColor"-->
                            <!--                                                class="w-5 h-5"-->
                            <!--                                        >-->
                            <!--                                            <path-->
                            <!--                                                    stroke-linecap="round"-->
                            <!--                                                    stroke-linejoin="round"-->
                            <!--                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"-->
                            <!--                                            />-->
                            <!--                                        </svg>-->
                            <!--                                    </button>-->
                            <!--                                    <button class="btn btn-ghost">-->
                            <!--                                        <svg-->
                            <!--                                                xmlns="http://www.w3.org/2000/svg"-->
                            <!--                                                fill="none"-->
                            <!--                                                viewBox="0 0 24 24"-->
                            <!--                                                stroke-width="1.5"-->
                            <!--                                                stroke="currentColor"-->
                            <!--                                                class="w-7 h-7"-->
                            <!--                                        >-->
                            <!--                                            <path-->
                            <!--                                                    stroke-linecap="round"-->
                            <!--                                                    stroke-linejoin="round"-->
                            <!--                                                    d="M6 18 18 6M6 6l12 12"-->
                            <!--                                            />-->
                            <!--                                        </svg>-->
                            <!--                                    </button>-->
                            <!--                                </div>-->
                            <!--                            </td>-->
                        </tr>

                        <?php
                    }

                }
                ?>


                <!-- row exmp -->

                <tr>
                    <td
                            colspan="6"
                            class="text-right border-r border-primary capitalize"
                    >
                        total
                    </td>
                    <td><?php echo number_format($total_dr, '2', ".", ",") ?> Tk</td>

                    <td><?php echo number_format($total_cr, '2', ".", ",") ?> Tk</td>
                    
		<?php
			$total_balance = (float)$total_dr - (float)$total_cr;
			if($total_balance < 0){
				$total_balance = number_format(abs($total_balance)) . " (Cr)";
			}elseif($total_balance == 0){
				$total_balance = number_format(abs($total_balance));
			}else{
				$total_balance = number_format($total_balance) . " (Dr)";
			}

		?>
		<td><?php echo $total_balance ?></td>
		<td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>

<script>
    const nextPage = (frm, to, page_no) => {
        document.getElementById("from").value = frm;
        document.getElementById("to").value = to;
        document.getElementById("page").value = page_no;

        document.getElementById("lcForm").submit.click();
    }

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

</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    // Put jQuery in noConflict mode and use $j instead of $
    var $j = jQuery.noConflict();
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>

<script>
    $j(document).ready(function () {
        $j(".cost_center").select2({
            width: '100%',
            dropdownParent: $j('.select2Parent2')
        });
    });
</script>

</body>
</html>

