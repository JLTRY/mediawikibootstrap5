function canvasupdate() {
	'use strict';
	console.log("mediawiki");
	var MWB = window.MWB = window.MWB || {};

	MWB.optionsStorage = MWB.optionsStorage || {
		"bootstrap.dropdown": { "interval": 1000, "pause": 'hover' }
	};

	MWB.getOptions = function (key) {
		return MWB.optionsStorage[key] !== undefined ? MWB.optionsStorage[key] : undefined;
	};

		try {
			console.log('DOMContentLoaded');
			var mobileMenu = document.getElementById('navbarSupportedContent');
			console.log("mobilenu");
			console.log(mobileMenu);
			if (!mobileMenu) {
				return;
			}
			console.log('DOMContentLoaded');
			var scrollTop = 0;

			mobileMenu.addEventListener('shown.bs.offcanvas', function () {
				scrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
				console.log('show.bs.offcanvas' + scrollTop);
				document.body.style.position = 'fixed';
				document.body.style.top = '0';//'-' + scrollTop + 'px';
				document.body.style.left = '0';
				document.body.style.right = '0';
				document.body.style.width = '100%';
				/*mobileMenu.style.position = 'absolute'; 
				mobileMenu.style.left = '0px'; 
				mobileMenu.style.top = '0px';*/
				
			});

			mobileMenu.addEventListener('hidden.bs.offcanvas', function () {
				document.body.style.position = '';
				document.body.style.top = '';
				document.body.style.left = '';
				document.body.style.right = '';
				document.body.style.width = '';
				window.scrollTo(0, scrollTop);
			});
		} catch (e) {
			console.error('Offcanvas body lock failed:', e);
		}
}

jQuery(document).ready(function ($) {
  canvasupdate();
});