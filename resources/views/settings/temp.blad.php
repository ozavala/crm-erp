@if($setting->key === 'default_country_tax')
                                <label for="default_country_tax" class="form-label">{{ __('messages.Default Country Tax') }}</label>
                                <select class="form-select" id="default_country_tax" name="default_country_tax">
                                    <option value="ecuador" {{ old('default_country_tax', $setting->value) == 'ecuador' ? 'selected' : '' }}>Ecuador</option>
                                    <option value="spain" {{ old('default_country_tax', $setting->value) == 'spain' ? 'selected' : '' }}>España</option>
                                    <option value="mexico" {{ old('default_country_tax', $setting->value) == 'mexico' ? 'selected' : '' }}>México</option>
                                    <option value="argentina" {{ old('default_country_tax', $setting->value) == 'argentina' ? 'selected' : '' }}>Argentina</option>
                                    <option value="colombia" {{ old('default_country_tax', $setting->value) == 'colombia' ? 'selected' : '' }}>Colombia</option>
                                </select>
                            @elseif($setting->key === 'tax_includes_services')
                                <label for="tax_includes_services" class="form-label">{{ __('messages.Tax Includes Services') }}</label>
                                <select class="form-select" id="tax_includes_services" name="tax_includes_services">
                                    <option value="true" {{ old('tax_includes_services', $setting->value) == 'true' ? 'selected' : '' }}>{{ __('messages.Yes') }}</option>
                                    <option value="false" {{ old('tax_includes_services', $setting->value) == 'false' ? 'selected' : '' }}>{{ __('messages.No') }}</option>
                                </select>
                            @elseif($setting->key === 'tax_includes_transport')
                                <label for="tax_includes_transport" class="form-label">{{ __('messages.Tax Includes Transport') }}</label>
                                <select class="form-select" id="tax_includes_transport" name="tax_includes_transport">
                                    <option value="true" {{ old('tax_includes_transport', $setting->value) == 'true' ? 'selected' : '' }}>{{ __('messages.Yes') }}</option>
                                    <option value="false" {{ old('tax_includes_transport', $setting->value) == 'false' ? 'selected' : '' }}>{{ __('messages.No') }}</option>
                                </select>
                            @else
                                <label for="{{ $setting->key }}" class="form-label">{{ __('messages.Setting') }}</label>
                                <input type="text" class="form-control" id="{{ $setting->key }}" name="{{ $setting->key }}" value="{{ old($setting->key, $setting->value) }}">