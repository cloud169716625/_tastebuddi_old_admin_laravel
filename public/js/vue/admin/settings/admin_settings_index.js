let appVue = new Vue({
    el:'#content',
    data:{
        settings: [],
        setting: {},

        editable_entry: '',
        editable_original_value : '',

        saving : false,
        setting_records : false,
        spinner : '<i class="icon-spinner2 spinner"></i>'
    },
    methods:{
        init(){
            let vm  = this;
            axios.get( '/ajax/settings', { params : {} } )
                .then(( result ) => {
                    let response = result.data;
                    if( response.success ){
                        vm.settings     = response.data.settings;
                    }else{
                        toastr.error( response.message );
                    }
                    vm.searching = false;
                }).catch(function (error) {
                    toastr.error( 'Something went wrong' );
                    vm.searching = false;
                });
        },

        getSettingValue( key ){
            let index = this.settings.findIndex( s => s.key == key );
            if( index !== -1 ){
                return this.settings[ index ].value;
            }

            return ' --- '
        },
        saveSetting(){
            let vm  = this;
            vm.saving = true;
            axios.post( '/ajax/settings', { key : vm.editable_entry, value : vm.setting.value } )
            .then(( result ) => {
                let response = result.data;
                if( response.success ){
                    //vm.settings     = response.data.settings;
                    this.editable_original_value = '';
                }else{
                    toastr.error( response.message );
                }
                vm.saving = false;
                vm.editable_entry = '';

            }).catch(function (error) {
            toastr.error( 'Something went wrong' );
                vm.saving = false;
            });
        },
        cancelSetting(){
            this.setting.value      = this.editable_original_value;
            this.editable_entry     = null;
        },
        editSetting( key ){
            if( this.editable_original_value ){
                this.setting.value      = this.editable_original_value;
            }

            let index = this.settings.findIndex( s => s.key == key );

            if( index !== -1 ){
                this.setting =  this.settings[ index ];
            }

            this.editable_entry = key;
            this.editable_original_value = JSON.parse(JSON.stringify(this.setting.value));
        },
        setupSubDomains(){
            let vm  = this;
            vm.setting_records = true;
            axios.post( '/ajax/settings/setupsubdomains', { } )
            .then(( result ) => {
                let response = result.data;

                if( response.success ){
                    $('#successModal').modal();
                }else{
                    toastr.error( response.message );
                }
                vm.setting_records = false;
            }).catch(function (error) {
                toastr.error( 'Something went wrong' );
                vm.setting_records = false;
            });
        }
    },
    mounted:function(){
        this.init();
    }
});