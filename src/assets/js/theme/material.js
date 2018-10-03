$.themematerial = {};
$.themematerial.init = function () {
    $(".step-anchor>li>a").addClass('waves-effect');
    $(".sw-toolbar button").addClass('waves-effect');
    Waves.attach('.sw-toolbar button', ['waves-block']);
    Waves.attach('.step-anchor li a', ['waves-block']);
    Waves.init();
};
