/* To avoid CSS expressions while still supporting IE 7 and IE 6, use this script */
/* The script tag referencing this file must be placed before the ending body tag. */

/* Use conditional comments in order to target IE 7 and older:
	<!--[if lt IE 8]><!-->
	<script src="ie7/ie7.js"></script>
	<!--<![endif]-->
*/

(function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'formwizard\'">' + entity + '</span>' + html;
	}
	var icons = {
		'formwizard-quill-ico': '&#xe91a;',
		'formwizard-restore-ico': '&#xea2e;',
		'formwizard-checkmark-ico': '&#xe900;',
		'formwizard-check-alt-ico': '&#xe901;',
		'formwizard-x-ico': '&#xe902;',
		'formwizard-x-altx-alt-ico': '&#xe903;',
		'formwizard-denied-ico': '&#xe904;',
		'formwizard-plus-ico': '&#xe905;',
		'formwizard-plus-alt-ico': '&#xe906;',
		'formwizard-minus-ico': '&#xe907;',
		'formwizard-minus-alt-ico': '&#xe908;',
		'formwizard-arrow-left-ico': '&#xe909;',
		'formwizard-arrow-left-alt1-ico': '&#xe90a;',
		'formwizard-arrow-left-alt2-ico': '&#xe90b;',
		'formwizard-arrow-right-ico': '&#xe90c;',
		'formwizard-arrow-right-alt1-ico': '&#xe90d;',
		'formwizard-arrow-right-alt2-ico': '&#xe90e;',
		'formwizard-arrow-up-ico': '&#xe90f;',
		'formwizard-arrow-up-alt1-ico': '&#xe910;',
		'formwizard-arrow-up-alt2-ico': '&#xe911;',
		'formwizard-arrow-down-ico': '&#xe912;',
		'formwizard-arrow-down-alt1-ico': '&#xe913;',
		'formwizard-arrow-down-alt2-ico': '&#xe914;',
		'formwizard-cd-ico': '&#xe915;',
		'formwizard-first-ico': '&#xe916;',
		'formwizard-last-ico': '&#xe917;',
		'formwizard-info-ico': '&#xe918;',
		'formwizard-hash-ico': '&#xe919;',
		'0': 0
		},
		els = document.getElementsByTagName('*'),
		i, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		c = el.className;
		c = c.match(/formwizard-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());
