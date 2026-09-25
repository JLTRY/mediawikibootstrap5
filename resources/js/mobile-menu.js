(function () {
	'use strict';

	// Cassiopeia/MetisMenu-style nested toggles inside the mobile offcanvas.
	document.addEventListener('click', function (event) {
		var toggler = event.target.closest('#navbarSupportedContent .mm-toggler');
		if (!toggler) {
			return;
		}

		event.preventDefault();
		event.stopPropagation();
		var item = toggler.closest('.mm-parent');
		var expanded = item.classList.toggle('is-open');
		toggler.setAttribute('aria-expanded', expanded ? 'true' : 'false');
		var link = item.querySelector(':scope > .nav-link');
		if (link) {
			link.setAttribute('aria-expanded', expanded ? 'true' : 'false');
		}
	});
})();
