<h1>Change Log</h1>
All notable changes to this project will be documented in this file.

<h1>1.4.6 - 17/05/2019</h1>

<h3>Added</h3>

- [ENH:6cbe50a](https://github.com/buttflattery/yii2-formwizard/commit/6cbe50ae60137cb31c4e6d4c541d1b24850a2b5d) Added support for the missing input type number by [@sircovsw](https://github.com/sircovsw).
- [ENH:25dd39b]() Limit rows for tabular form and code enhancement

  <h3>Changed</h3>

- [BUG:84899ca](https://github.com/buttflattery/yii2-formwizard/commit/84899ca84f9efabae9df6482ada79ca0e34f92fb) Fixed the bug in formwizard preview steps while retrieving the text from drop-down by [@sircovsw](https://github.com/sircovsw).
- [HOTFIX:36594062f97fcd31cf142484fc3c86cd511fd0c5](https://github.com/buttflattery/yii2-formwizard/commit/36594062f97fcd31cf142484fc3c86cd511fd0c5) added missing semi-colon.
- [HOTFIX:9f9c8892fc8e155695f15b83662cae610294f438](https://github.com/buttflattery/yii2-formwizard/commit/9f9c8892fc8e155695f15b83662cae610294f438) added check for the options type.
- [BUG:71efb65]() Fixed name for the classListGroup option

<h1>1.4.0 - 25/04/2019</h1>

<h3>Added</h3>
- April 24, 2019 [FEATURE:f387e81](https://github.com/buttflattery/yii2-formwizard/commit/f387e81a9f48241904fed9f1e4b4e17ef213ad9c) added ability to group fields under heading.
- April 21, 2019 [FEATURE:97e5464](https://github.com/buttflattery/yii2-formwizard/commit/97e5464d6c3b84ad5cb4472320cdf56b4fa5bd9d) added new theme tags.

<h3>Changed </h3>
- April 25, 2019[BUG:30936c3](https://github.com/buttflattery/yii2-formwizard/commit/30936c352a93b365e681bbb76cc5b8dfdb74e9aa) added fix for the form-persistence bug when using multiple instances.

- April 23, 2019 [BUG:22b06d2 ](https://github.com/buttflattery/yii2-formwizard/commit/22b06d2cb6eb0c233ca82f4864f21de0e19d86f7) fix for tabular row width for all themes and zindex for the tags theme tool bar.

- April 22, 2019 [BUG:3ab6f4f](https://github.com/buttflattery/yii2-formwizard/commit/3ab6f4f7d6ebd721c6834f6d5a26e802fa919e19) fixed toolbar position.

- April 16,2019 [BUG:e4bcf15](https://github.com/buttflattery/yii2-formwizard/commit/e4bcf154b51c8e3cbac9c8f962b23c767e7f9111) fixed the navigation padding and the background for the toolbar buttons.

- April 15, 2019 [BUG:30936c3](https://github.com/buttflattery/yii2-formwizard/commit/30936c352a93b365e681bbb76cc5b8dfdb74e9aa) added fix for the form-persistence bug when using multiple instances.

---

<h1>1.3.0 - 22/10/2018</h1>

<h3>Added</h3>

- April 02, 2019 [FEATURE:6fca8ea](https://github.com/buttflattery/yii2-formwizard/commit/6fca8ea9a29de0da9c4c3c14a8e291a47566615b) Added new feaature to save/restore un-saved form fields by enabling `enablePersistence` option.

- March 27, 2019 [FEATURE:1beb505](https://github.com/buttflattery/yii2-formwizard/commit/1beb5050f8fbaa8d8b8cb6af89cc8cb8de20fe7d) (feature/final-preview) Added new feature Preview Step.
- March 18, 2019 [FEATURE:5769d27](https://github.com/buttflattery/yii2-formwizard/commit/5769d27fc9059cf4d7d5f7348d0a67213678c8c9) Added new feature for the Tabular step

  <h3>Removed</h3>

- March 25, 2019 [2d7d08f](https://github.com/buttflattery/yii2-formwizard/commit/2d7d08f5aa6a6489a57e30a87baaa67e3225b29f) removed the `form-inline` class for the tabular form.

---

<h1>1.0.0 - 22/10/2018</h1>

<h3>Added</h3>

- March 13, 2019 [ENH:4e52390](https://github.com/buttflattery/yii2-formwizard/commit/4e523900e3f5312a1bc72e7561b5b43a731f1fb3) Added hint option for the inputs to provide customized text.

- Nov 26, 2018 [ENH:bdcd340](https://github.com/buttflattery/yii2-formwizard/commit/bdcd34097f19ce9b37dfa63c065366c5ae3b3a52) added bootstrap4 support and updated all the css and assets files.

- Nov 25, 2018 [ENH:f27ec80](https://github.com/buttflattery/yii2-formwizard/commit/f27ec80b8f2f2b40200a9eb1b0ecda8e9e20b884) updates for the bootstrap-4-compatibility.

- Nov 24, 2018 [ENH:6111316](https://github.com/buttflattery/yii2-formwizard/commit/61113161729a4ed1aa0aebd75dd1a8359ccfc789) added support for field order using `fieldOrder` under the `steps` options and a new option to specify `inputOptions` for the ActiveField under the `fieldConfig`, and updated the docs.

- Nov 21, 2018 [ENH:5c74aa0](https://github.com/buttflattery/yii2-formwizard/commit/5c74aa069eb37947777e4fa3f43e359173e1b652) added support for the `password` and `hidden` active field.

- Nov 7, 2018 [ENH:678be15](https://github.com/buttflattery/yii2-formwizard/commit/678be15d4a8be813653cf8a29dc8a05715e11ede) added support for array based field names by adding `multifield` option for the active fields.

- Nov 6, 2018 [ENH:223dd53](https://github.com/buttflattery/yii2-formwizard/commit/223dd5379b1c34aeed41e0facd9b4259e5bd0c18) added support for multiple models in single step.

- Oct 7, 2018 [ENH:d8b14a6](https://github.com/buttflattery/yii2-formwizard/commit/d8b14a6de252bb0ff6e48963e2ecebdfbbeb9adf) updated the sections to customize all fields with `textarea`, `radio`, `checkbox`.

<h3>Changed</h3>
- March 30,2019 [BUG:52d9582](https://github.com/buttflattery/yii2-formwizard/commit/52d9582014c5b8e3b80633b8fbaa161f60800cc5) fixed unsynced preview containers for theme arrows.

- March 25, 2019 [BUG:9e44bf7](https://github.com/buttflattery/yii2-formwizard/commit/9e44bf7805f7c78f7a33d746a1d42bf736bfbdd6) Fixed the bug with the add row when using widgets, closes #14.

- Dec 6, 2018 [BUG:19c9619](https://github.com/buttflattery/yii2-formwizard/commit/19c96197bceb3767d4e9623897bd1f20ee3de02b) Added fix for kartik/depdrop widget Fixes #8

- Nov 28, 2018 [BUG:142f4de](https://github.com/buttflattery/yii2-formwizard/commit/142f4de15aa8cfcdd55997dca3cfead295bcbd0a) Disabled form navigation on keyboard LEFT & RIGHT buttons as it skips the validation for the form and navigates to the next step change the default to `keyNavigation:false`.

- Oct 20, 2018 [ENH:7d09163](https://github.com/buttflattery/yii2-formwizard/commit/7d091630424e171d7f2ce61d8fc0a4e81adf085a) changed form info from `h3` to info `alert` bootstrap.

- Oct 20, 2018 [ENH:2d3e476](https://github.com/buttflattery/yii2-formwizard/commit/2d3e4767b50422a0c80978ad8d996e7ef7d0ae9e) fixes for the css and renamed `disabled` option to `except`.

- Oct 19, 2018 [ENH:343f942](https://github.com/buttflattery/yii2-formwizard/commit/343f942728cdbebb1ee93e915cb6f8c1325bd710) fixes for bootstrap themes.

- Oct 17, 2018 [ENH:9380d57](https://github.com/buttflattery/yii2-formwizard/commit/9380d575f23f55de76a625feb45345dc9acc9590) Code improvement, replaced the `isset()` with `ArrayHelper::getValue()` and removed several if else shorthand statements.

- Oct 10, 2018 [BUG:1d001ae](https://github.com/buttflattery/yii2-formwizard/commit/1d001aee91f8dbed7df04cf2ce4cfa38f773f1ea) added css button toolbar fix.

- Oct 10, 2018 [BUG:628e1d4](https://github.com/buttflattery/yii2-formwizard/commit/628e1d4b1b20e05bfc52c4ec0669953da3f727d3) Updated material.js with correct selectors for the theme material to apply wave effects via `observer` once loaded.

- Oct 10, 2018 [ENH:eece731](https://github.com/buttflattery/yii2-formwizard/commit/eece731284d336061eea6efb422043a03c46b9c1) added `margin-bottom:40px` in `smart_wizard_theme_dots.css`.

- Oct 10, 2018 [ENH:099416a](https://github.com/buttflattery/yii2-formwizard/commit/099416a43d50d38cb61b8661d070ca9a9761ad09) updated the section for css and added minified versions minify.

- Oct 10, 2018 [BUG:3a7bc4a](https://github.com/buttflattery/yii2-formwizard/commit/3a7bc4aefc50e0be2b597b5ffa233c55c5aa4b97) updated the sections with ajax validation fix.

<h3>Removed</h3>

- Nov 21, 2018 [BUG:30cd5d8](https://github.com/buttflattery/yii2-formwizard/commit/30cd5d85dc135084011b3e61407c940962a6ce95) remove manual setting of `formOptions['action']` in the `setDefaults()` method.
