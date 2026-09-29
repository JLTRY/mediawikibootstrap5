
(function () {
	'use strict';

	Joomla = {};
	Joomla.optionsStorage =
	{
		"bootstrap.dropdown" : { "interval" : 1000, "pause" : 'hover' }
	};
	Joomla.getOptions = function (key) {
		// Load options if they do not exist.
		return Joomla.optionsStorage[key] !== undefined ? Joomla.optionsStorage[key] : undefined;
	};

	/*
	 * Bootstrap's offcanvas focus handling can scroll the page when the mobile
	 * menu is opened. Restore the original position after the transition and
	 * after closing the menu.
	 */
	var menuScrollTop = null;
	var mobileMenu = document.getElementById('navbarSupportedContent');

	if (mobileMenu) {
		mobileMenu.addEventListener('show.bs.offcanvas', function () {
			menuScrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
		});

		mobileMenu.addEventListener('shown.bs.offcanvas', function () {
			if (menuScrollTop !== null) {
				window.scrollTo(0, menuScrollTop);
			}
		});

		mobileMenu.addEventListener('hidden.bs.offcanvas', function () {
			if (menuScrollTop !== null) {
				window.scrollTo(0, menuScrollTop);
				menuScrollTop = null;
			}
		});
	}
})();

/*
$( document ).ready(function() {
	$("#btn-toggler").click();
	$("#btn-toggler").click();
});*/
