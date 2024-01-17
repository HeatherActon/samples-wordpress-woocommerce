(function ($) {
	$(document).ready(function () {
		$(document).on('click', '#remove-restricted-products', function(e){
			
			e.preventDefault();
						
			var productID = $(this).attr('data-remove-product-id');
						
			$.ajax({
				url: removerestrictedproducts.ajaxurl,
				type: "POST",
				data: {
					action: 'remove_restricted_products_from_cart',
					productID: productID,
					customer_id: removerestrictedproducts.customer_id,
					security: removerestrictedproducts.security,
				},
				success: function(response) {
					if (response.data.redirect) {
						location.href = '/shop/';
					} else {
						location.reload();
					}
				},  
				error: function (errorThrown) {
				
				},
				complete: function () { $('#lightbox-restricted-product-container').hide(); },
			});
			
		});
	});
})(jQuery);