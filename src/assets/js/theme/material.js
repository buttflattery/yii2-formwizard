$.themematerial = {};
$.themematerial.init = function () {
    $(".sw-theme-material>.step-anchor>li>a, .sw-theme-material-v>.step-anchor>li>a").addClass('waves-effect');
    $(".sw-theme-material>.sw-toolbar button, .sw-theme-material-v> .sw-toolbar button").addClass('waves-effect');
    Waves.attach('.sw-theme-material> .sw-toolbar button', ['waves-block']);
    Waves.attach('.sw-theme-material> .step-anchor li a', ['waves-block']);
    Waves.init();
};