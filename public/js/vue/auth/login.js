
var appVue = new Vue({
    el:'#content',
    data:{

        signing_in: false,
        spinner: '<i class="icon-spinner2 spinner"></i>',
        email: '',
        pwd: '',
        error:{ has_error: false, message : '' }
    },
    methods:{
        init(){

        },
        login(){
            this.signing_in = true;
        }
    },
    mounted:function(){
        this.init();
    }
});