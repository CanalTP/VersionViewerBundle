jQuery(function() {

	// display tooltips on release file link etc ...
	$('.lnk').tooltip();
	// avoid event propagation on click
	$('.dropdown-menu input, .dropdown-menu label').click(function(e) {
		e.stopPropagation();
	});

	/**
	 * reload an application instance on click of an instance button class
	 * .refreshInstance
	 */
	$('.refreshInstance').click(function(e) {
		e.preventDefault();
		var error = $(this).parent('li').parent('ul').find('li.error');
		error.clean
		var property = $(this).parent('li').parent('ul').find('li.properties');
		var url = $(this).attr('href');
		var loading = $(this).parent('li').find('img.loading');
		var section = $(this).parentsUntil('div.row').parent();

		cleanMessages(section);

		loading.hide();
		loading.show();
		$.ajax({
			type : "GET",
			url : url,
			cache : false,
			success : function(data) {
				if (data.errors.length == 0) {

					// /clean previous properties and error
					property.empty();
					error.empty();

					// @todo better to pass only properties
					var properties = parseProperties(data);

					property.append(properties);
					
					//display warning
					if(data.warnings.length > 0){
						addMessages(section, 'warning', data.warnings);
					}

				} else {
					// display generic error message in instance
					error.text('An error occured !');
					// display detailled error message in recap
					addMessages(section, 'error', data.errors);

				}
				loading.hide();
			},
			error : function(xhr, textStatus, errorThrown) {
				// @TODO handle full message with accordion ?
				//display detailed error message in recap
				addMessages(section, textStatus, errorThrown + ':'
						+ xhr.responseText.substring(0, 300) + ' ...');
				// display generic error message in instance
				error.text('An error occured !');
				
				// hide loader
				loading.hide();
			}
		});
	});

	/**
	 * refresh all instance of an app on click on the refresh buttonn of an
	 * application class .refreshApp
	 */
	$('.refreshApp').click(
			function(e) {
				e.preventDefault();

				var url = $(this).attr('href');
				var section = $(this).parentsUntil('div.row').parent();

				// display loader in each instance
				section.find('img.loading').each(function(f) {
					$(this).show();
				});
				// clean previous error message
				cleanMessages(section);

				$.ajax({
					type : "GET",
					url : url,
					cache : false,
					success : function(data) {
						// for each instance
						for ( var env in data.instances) {
							var obj = data.instances[env];
							// find corresponding instance
							var ul = section.find("ul[id*='" + env + "']");

							if (obj.errors.length == 0) {
								// add instance color
								ul.addClass(obj.color);

								// /clean previous properties and error
								ul.find('li.properties').empty();
								ul.find('li.error').empty();

								// @todo could be better to pass only properties than entire object
								var properties = parseProperties(obj, env);
								ul.find('li.properties').append(properties);
								
								//display warning
								if(obj.warnings.length > 0){
									addMessages(section, 'warning', obj.warnings);
								}

							} else {
								// display detailled error message in recap
								addMessages(section, 'error', obj.errors);
								// display generic error message in instance
								ul.find('li.error').text('An error occured !');
							}

							ul.find('.loading').hide();
						}// end instance loop

						// for each level of error message
						for ( var level in data.messages) {
							// if there is message
							if (typeof (level) != 'undefined') {
								if (data.messages[level].length > 0) {
									addMessages(section, level,
											data.messages[level]);
								}
							}
						}

					},
					error : function(xhr, textStatus, errorThrown) {
						// @TODO handle full message with accordion ?
						addMessages(section, textStatus, errorThrown + ':'
								+ xhr.responseText.substring(0, 300) + ' ...');
						// hide loader
						section.find('img.loading').each(function(f) {
							$(this).hide();
						});
					}
				});
			});

	/**
	 * parse instance's properties and return HTML formated content to inject
	 * into container.
	 */
	function parseProperties(obj, env) {

		var dataString = '<ul>';
		for ( var prop in obj.properties) {
			if (prop != 'undefined') {
				dataString = dataString + '<li>' + "<strong> " + prop
						+ ':</strong><br/> ' + obj.properties[prop] + '</li>';
			}
		}
		dataString = dataString + '</ul>';
		return dataString;
	}
	;

	/**
	 * click on left menu propagate click on the refresh application button and
	 * refresh all instance of that application
	 */
	$('.app').click(function(e) {
		var id = $(this).find('a').attr('href').replace("#", "");
		$('.bs-docs-section h2#' + id + ' a').click();
	});

	/**
	 * On change event of the checkbox when checkbox is unchecked all instance
	 * for the corresponding environment are hidden.
	 */
	$('input[id^="checkbox-env-"]').change(function() {

		var env = this.value;

		$('div.' + env).each(function() {
			$(this).toggle(500);
		});
	});

	/**
	 * add a container for message according level in param for the application
	 * 
	 * @param DOMelement
	 *            section : section where to add the message
	 * @param String
	 *            level : level of the message (error, warning, info)
	 * @param String|Object
	 *            message : error message content. can be an object / array or a string.
	 */
	function addMessages(section, level, message) {

		// not so clean method to map BS class
		var alertClass
		if (level == 'error' || level =='parsererror') {
			alertClass = 'danger';
		} else {
			alertClass = level
		}

		// handle arrray
		var listeMessage;
		if (typeof (message) == 'object') {
			listeMessage = '<ul>';
			for ( var key in message) {
				listeMessage += '<li>' + message[key] + '</li>';
			}
			listeMessage += '</ul>';
			message = listeMessage;
		}

		section
				.find('h2 + div.message')
				.append(
						'<div class="alert alert-'
								+ alertClass
								+ '">'
								+ '<button type="button" class="close" data-dismiss="alert">&times;</button>'
								+ '<h3>' + level + ' ! </h3>' + message
								+ '</div>');
	}

	/**
	 * clean message for an application
	 * 
	 * @param DOMelement
	 *            section : container div
	 */
	function cleanMessages(section) {
		section.find('h2 +div.message').empty();
	}

	/**
	 * fix to reload modal content each time is displayed
	 */
	$('body').on('hidden.bs.modal', '.modal', function() {
		$(this).removeData('bs.modal');
	});

});
