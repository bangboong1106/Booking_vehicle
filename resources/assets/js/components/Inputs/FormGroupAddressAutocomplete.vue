<template>
    <div class="form-group" :class="{'input-group': hasIcon,'input-error':hasError}">
        <slot name="label">
            <label v-if="label" class="control-label">
                {{label}}
            </label>
        </slot>
        <slot name="addonLeft">
      <span v-if="addonLeftIcon" class="input-group-prepend">
        <i :class="addonLeftIcon" class="input-group-text"></i>
      </span>
        </slot>
        <div class="group-input-icon">
            <input ref="autocomplete"
                    :value="value"
                    v-bind="$attrs"
                    class="form-control"
                    :type="type"
                    placeholder="Dán hoặc nhập địa chỉ vào"
                    aria-describedby="addon-right addon-left"
                    :minlength="minlength" :maxlength="maxlength" :max="max" :min="min">
            <slot></slot>
            <slot name="addonRight">
              <span v-if="addonRightIcon" class="input-group-append">
                <i :class="addonRightIcon" class="input-group-text"></i>
              </span>
            </slot>
        </div>
        <span class="text-error">{{textError}}</span>
    </div>
</template>
<script>
    export default {
        inheritAttrs: false,
        name: "fg-address",
        data() {
            return {
                error: null,
                textError: ""
            };
        },
        props: {
            label: String,
            value: [String, Number],
            addonRightIcon: String,
            addonLeftIcon: String,
            type: String,
            minlength: Number,
            maxlength: Number,
            min: Number,
            max: Number,
            fieldName:String,
            errorData:Array
        },
        mounted() {
            this.autocomplete = new google.maps.places.Autocomplete(
                (this.$refs.autocomplete),
                {types: ['geocode'],componentRestrictions: {country: 'vn'}}
            );
            this.autocomplete.addListener('place_changed', () => {
                const location = {
                    Country : "", // Quốc gia
                    Province : "", // Tỉnh thành
                    District : "", // Quận huyện
                    Wards : "", // Xã phường
                    Latitude : null,
                    Longitude : null,
                    Address : ""
                };
                const bindingfn = (placeAdress) => {
                    if(placeAdress){
                        const address = {};
                        let ac = placeAdress.address_components;
                        if(ac){
                            ac.forEach(item => {
                                if(item.types){
                                    item.types.forEach(key => {
                                        address[key] = item["short_name"];
                                    });
                                }
                            });
                        }
                        if(placeAdress.geometry){
                            location.Latitude = placeAdress.geometry.location.lat();
                            location.Longitude = placeAdress.geometry.location.lng();
                        }

                        location.Country = address.country;
                        location.Province = address.administrative_area_level_1;
                        location.District = address.administrative_area_level_2 || address.locality;
                        location.Wards = address.administrative_area_level_3 || address.sublocality;
                        location.Address = place.formatted_address ? place.formatted_address : place.name;
                        this.$emit("input", location.Address);
                        this.$emit("valueChange", {
                            data: location
                        });
                    }
                };
                const geocoder = new google.maps.Geocoder();
                const pacSelect = $(".pac-container .pac-item.pac-selected").text();
                const searchText = pacSelect ? pacSelect : $(".pac-container .pac-item:first").text();
                geocoder.geocode({address: searchText}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        bindingfn(results[0]);
                    }
                });
                let place = this.autocomplete.getPlace();
                bindingfn(place);
            });

        },
        computed: {
            hasIcon() {
                const {addonRight, addonLeft} = this.$slots;
                return addonRight !== undefined || addonLeft !== undefined || this.addonRightIcon !== undefined || this.addonLeftIcon !== undefined;
            },
            hasError(){
                if(!this.errorData){
                    return false;
                }
                this.textError = '';
                let error = this.errorData.filter(item => item.FieldName == this.fieldName);
                if(error != null && error.length > 0){
                    this.error = error[0];
                    this.textError =  this.error.ErrorText;
                }
                return error == null || (error != null && error.length ==0) ? false : true;
            }
        }
    }
</script>
<style>

</style>
