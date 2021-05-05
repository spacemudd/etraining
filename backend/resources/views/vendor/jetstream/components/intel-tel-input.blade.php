@props(['disabled' => false])
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
<div class="w-full">
	<input x-data="{ value: @entangle($attributes->wire('model')), autonumeric: undefined }"
	       x-ref="input"
	       x-init="window.intlTelInput($refs.input, {
	          dropdownContainer: document.body,
	          geoIpLookup: function(success, failure) {
							axios.get('https://ipinfo.io?token=token_number')
									.then((response) => {
									  return success((response && response.data.country) ? response.data.country : 'id')
									})
									.catch((error) => {
									  return failure(error)
									})
						},
	          	nationalMode: false,
	          	autoHideDialCode: false,
            	utilsScript: 'js/utils.js',
            	initialCountry: 'auto'
				 })"
	       x-on:change="value = $event.target.value"
	       type="tel"
		{{ $disabled ? 'disabled' : '' }}
		{{ $attributes->merge(['class' => 'form-input block w-full sm:text-sm border-gray-200 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500']) }}
	>
</div>
