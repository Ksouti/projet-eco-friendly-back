/\*

-   ENTRY CONFIG
-
-   Each entry will result in one JavaScript file (e.g. app.js)
-   and one CSS file (e.g. app.css) if your JavaScript imports CSS.
    \*/
    .addEntry("app", "./assets/app.js")

// enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
.enableStimulusBridge("./assets/controllers.json")

// When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
.splitEntryChunks()

// will require an extra script tag for runtime.js
// but, you probably want this, unless you're building a single-page app
.enableSingleRuntimeChunk()
