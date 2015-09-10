/**
 * Part of the Platform Installer extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Installer extension
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

 (function() {

	var Install = {};

	Install.Platform = {

		init: function() {

			// Scope Helper
			this._bind = function(fn, me) {
				return function() {
					return fn.apply(me, arguments);
				};
			};

			// Safety for Transitions
			$(document.body).removeClass('preload');

			// Add Event Listeners
			this.addListeners();

		},

		addListeners: function() {

			$('#form').on('success.form.bv', function() {

				$('.loader').show();

			});

 			$('.btn-license').on('click', this.handleLicense);

			$('#choose-database-driver').on('change', this.handleStorage);

			$('#choose-database-driver').trigger('change');

		},

		handleLicense: function() {

			$('.license').fadeOut('fast', function(){
				$('.install__form').fadeIn('fast');
			});
  		},

		handleStorage: function() {

			var $chooser = $(this);

			var $selectedValue = $chooser.val();

			var $target = $('#database-driver-' + $selectedValue);

			$('.database-driver').not($target).addClass('hide');

			$target.removeClass('hide');

			if ($selectedValue !== '')
			{
				$(document.body).animate({ scrollTop: $(this).offset().top }, 500);
			}

  		},

	};

	$(function() {

		return Install.Platform.init();

	});

}).call(this);
