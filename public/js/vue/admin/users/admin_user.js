
var appVue = new Vue({
    el:'#content',
    data:{
        uid : null,
        user: { full_name: ''},
        uploaded_profile_photo: { progress : 0 },

        spinner: '<i class="icon-spinner2 spinner"></i>',
        upload_icon : '<i class="icon-upload4"></i>',

        saving: false,
        uploading :  false
    },
    methods:{
        init(){
            this.getUser();
            this.mountUploadProfilePhoto();
        },
        async getUser( ){

            let vm       = this;
            vm.searching = true;

            await axios.get( '/ajax/user', { params : { uid : vm.uid } } )
            .then(( result ) => {
                let data = result.data.data;
                vm.user  = data.user;
                vm.searching = false;
            }).catch(function (error) {
                toastr.error( 'Something went wrong' );
                vm.searching = false;
            });

        },
        openUserModal(){
            $('#editUserModal').modal();
        },
        saveUser()
        {
            let vm       = this;
            vm.saving = true;
            vm.user._token = vm.token;

            axios.post( '/ajax/user', vm.user )
            .then(( result ) => {
                let response = result.data;
                if( response.success ){
                    vm.user = response.data.user;
                    toastr.success( 'Success' )
                    $('#editUserModal').modal( 'toggle' );
                }else{
                    toastr.error( response.message )
                }
                vm.saving = false;
            }).catch(function (error) {
                toastr.error( 'Something went wrong' );
                vm.saving = false;
            });

        },
        mountUploadProfilePhoto() {
            let vm = this;
            $('#profile_photo_upload').fileupload({
                dataType: 'json',
                add: function (e, data) {
                    data.submit();
                },
                progress: function (e, data) {
                    let id = data.files[0]['name'];
                    vm.uploaded_profile_photo.progress = parseInt(data.loaded / data.total * 100, 10);
                },
                complete: function (response, textStatus, xhr) {
                    let data = response.responseJSON;
                    vm.uploading = false;
                    if (data.success) {
                        vm.uploaded_profile_photo.progress = 0;
                        vm.user = data.data.user;
                        toastr.success('Photo successfully uploaded');
                    } else {
                        toastr.error(data.message)
                    }
                },
                start: function (e) {
                    vm.uploading = true;
                },
                done: function (e, data) {
                    let id = data.files[0]['name'];
                },
                error: function (e, data) {
                    vm.progress = 0;
                    //toastr.error('Something went wrong ');
                    vm.uploading = false;
                    vm.uploaded_profile_photo.progress = 0;
                }
            });
        }
    },
    mounted:function(){
        this.uid = $('#uid').val();
        this.token = $('input[name=_token]').val();
        this.init();
    }
});

