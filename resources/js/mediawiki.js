
(function () {
  

	Joomla =  {};
	Joomla.optionsStorage =
	{		
		"bootstrap.dropdown" : { "interval" : 1000, "pause" : 'hover' }
	};
	Joomla.getOptions = function(key)  {
      // Load options if they not exists
      
      return Joomla.optionsStorage[key] !== undefined ? Joomla.optionsStorage[key] : undefined;
	}
})(); 

/*
$( document ).ready(function() {
	$("#btn-toggler").click();
	$("#btn-toggler").click();
});*/