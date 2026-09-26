(function () {
	'use strict';

	function toggleItem(item, toggler) {
		var expanded = item.classList.toggle('is-open');
		toggler.setAttribute('aria-expanded', expanded ? 'true' : 'false');
		var link = item.querySelector(':scope > .nav-link');
		if (link) {
			link.setAttribute('aria-expanded', expanded ? 'true' : 'false');
		}
	}

	document.addEventListener('click', function (event) {
		var toggler = event.target.closest('#navbarSupportedContent .mm-toggler');
		var link = event.target.closest('#navbarSupportedContent .mm-parent > .nav-link');

		if (!toggler && !link) {
			return;
		}

		// Parent links are menu toggles on mobile. The child links remain normal links.
		event.preventDefault();
		event.stopPropagation();

		var item = (toggler || link).closest('.mm-parent');
		var itemToggler = item.querySelector(':scope > .mm-toggler');
		if (itemToggler) {
			toggleItem(item, itemToggler);
		}
	});
})();
