
var appVue = new Vue({
    el:'#content',
    data:{
        token: '',
        users: [],
        user: { profile_photo_url : '/images/blank_face.jpg' , full_name: '' },
        q : '',

        /**** pagination data ***/
        page_list: [],
        current_page : 1,
        active_row : null,

        searching : false,
        saving: false,
        search_icon : '<i class="icon-search4"></i>',
        spinner: '<i class="icon-spinner2 spinner"></i>'

    },
    methods:{
        init(){
            this.getUsers( true );
        },

        getUsers( is_init = false ){
            let vm          = this;
            vm.searching    = true;
            axios.get( '/ajax/users', { params : { page: vm.current_page, q : vm.q } } )
            .then(( result ) => {
                let response = result.data;
                if( response.success ){
                    vm.users     = response.data.users;
                    vm.page_list = response.data.page_list;

                    if( is_init ){
                        if( vm.users.length ){
                            vm.user = vm.users[0];
                        }
                    }
                }else{
                    toastr.error( response.message );
                }

                vm.searching = false;
            }).catch(function (error) {
                //toastr.error( 'Something went wrong' );
                vm.searching = false;
            });
        },
        editUser( uid ){
            this.userSelected( uid );
            $('#editUserModal').modal();
        },
        userSelected( uid ){
            this.user = $.grep( this.users , function(u){
                return u.id == uid;
            })[0];
        },
        openUserDeletionModal( uid ){
            this.userSelected( uid );
            $('#deleteUserModal').modal();
        },
        deleteUser(){
            vm = this;
            axios.delete( '/ajax/user', { params : { uid : vm.user.id , _token : vm.token } } )
            .then(( result ) => {
                let response = result.data;
                if( response.success ){
                    let index = vm.users.findIndex( u => u.id == response.data.uid );
                    vm.users.splice( index , 1 );
                    alert( index );
                    $('#deleteUserModal').modal( 'toggle' );
                }else{
                    toastr.error( response.message );
                }

                vm.searching = false;
            }).catch(function (error) {
                toastr.error( 'Something went wrong' );
                vm.searching = false;
            });
        },
        openUserModal(){
            this.userSelected( uid );
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

                    let index = vm.users.findIndex( u => u.id == vm.user.id );
                    vm.user     =   response.data.user;

                    //Vue.set( vm.users, index, response.data.user );

                    toastr.success( 'Success' );
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
        goToUser( user_id ){
            location.href ='/admin/user/'+user_id;
        },
        userSelected( user_id ){
            this.user = $.grep( this.users , function( u ){
                return u.id == user_id;
            })[0];
        },
        openUserModal( user_id = null  ){
            if( ! user_id ){
                this.resetUser();
            }
            this.userSelected( user_id );
            $('#userModal').modal()
        },
        closeUserModal(){
            $('#userModal').modal( 'toggle' );
        },
        resetUser(){
            this.user = {};
        },
        search(){
            this.current_page = 1;
            this.getUsers();
        },
        goToPage( page ){
            this.current_page = page;
            this.loading = true;
            this.getUsers();
        },
        uploadPhoto(){
            toastr.error( 'Uploading photo is under development')
        }
    },
    mounted:function(){
        this.token = $('input[name=_token]').val();
        this.init();
    }
});
