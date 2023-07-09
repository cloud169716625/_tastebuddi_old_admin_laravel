@extends( 'themes.appetiser.layouts.layout_admin' )

@section('content')
    <?php echo csrf_field() ?>
    <div class="page-header">
        <div class="brad-crumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb back-default">
                    <li class="breadcrumb-item" aria-current="page">Home</li>
                    <li class="breadcrumb-item active" aria-current="page">Settings</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="mt-4">
        <h4>Settings</h4>
    </div>


    <div class="content p-20">
        <div class="row">
            <div class="col-12">
                Servers
            </div>
        </div>
        <hr />
        <div class="row m-2">
            <div class="col-2">
                Development Server Domain :
            </div>
            <div class="col-10">
                <div class="form-group">
                    <div class="col-10" v-show="editable_entry!='dev_server_domain'"> @{{ getSettingValue( 'dev_server_domain' ) }}
                        <i class="icon-pencil3 pointer"  style="color:#CFCFCF" @click="editSetting('dev_server_domain')" ></i>
                    </div>
                    <div class="" v-show="editable_entry=='dev_server_domain'">
                        <div class="input-group mb-3">
                            <input type="text" name="" value="" id="" class="form-control col-4" v-model="setting.value" />
                            <div class="input-group-append">
                                <button @click="saveSetting" class="btn btn-secondary" type="button" style="" v-html=" saving ? spinner : 'Save' "></button>
                            </div>
                            <div class="input-group-append">
                                <button @click="cancelSetting" class="btn btn-outline-secondary" type="button" style="border: 1px solid #E5E5E5;border-top-right-radius:8px;border-bottom-right-radius:8px" v-html=" 'Cancel' "></button>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <div class="row m-2">
            <div class="col-2">
                Development Server IP :
            </div>
            <div class="col-10">
                <div class="form-group">
                    <div class="col-10" v-show="editable_entry!='dev_server_ip'"> @{{ getSettingValue( 'dev_server_ip' ) }}
                        <i class="icon-pencil3 pointer"  style="color:#CFCFCF" @click="editSetting( 'dev_server_ip' )" ></i>
                    </div>
                    <div class="" v-show="editable_entry=='dev_server_ip'">
                        <div class="input-group mb-3">
                            <input type="text" name="" value="" id="" class="form-control col-4" v-model="setting.value" />
                            <div class="input-group-append">
                                <button @click="saveSetting" class="btn btn-secondary" type="button" style="" v-html=" saving ? spinner : 'Save' "></button>
                            </div>
                            <div class="input-group-append">
                                <button @click="cancelSetting" class="btn btn-outline-secondary" type="button" style="border: 1px solid #E5E5E5;border-top-right-radius:8px;border-bottom-right-radius:8px" v-html=" 'Cancel' "></button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <hr />
        <div class="row m-2">
            <div class="col-2">
                Dev Server Subdomain :
            </div>
            <div class="col-10">
                <div class="form-group col-6">
                    <div v-show="editable_entry!='dev_subdomain'">@{{ getSettingValue( 'dev_subdomain' ) }}
                        <i class="icon-pencil3 pointer"  style="color:#CFCFCF" @click="editSetting('dev_subdomain')" ></i>
                    </div>
                    <div class="input-group mb-3" v-show="editable_entry=='dev_subdomain'">
                        <input type="text" name="" value="" id="" class="form-control col-4" v-model="setting.value" />
                        <div class="input-group-append">
                            <button @click="saveSetting" class="btn btn-secondary" type="button" style="" v-html=" saving ? spinner : 'Save' "></button>
                        </div>
                        <div class="input-group-append">
                            <button @click="cancelSetting" class="btn btn-outline-secondary" type="button" style="border: 1px solid #E5E5E5;border-top-right-radius:8px;border-bottom-right-radius:8px" v-html=" 'Cancel' "></button>
                        </div>
                    </div>
                    <div> <i style="color: #AFAFAF">@{{ getSettingValue( 'dev_subdomain' ) }}.@{{ getSettingValue( 'dev_server' ) }}</i></div>
                </div>
            </div>
        </div>
        <div class="row m-2">
            <div class="col-2">
                Staging Server Subdomain :
            </div>
            <div class="col-10">
                <div class="form-group col-6">
                    <div v-show="editable_entry!='staging_subdomain'">@{{ getSettingValue( 'staging_subdomain' ) }}
                        <i class="icon-pencil3 pointer"  style="color:#CFCFCF" @click="editSetting('staging_subdomain')" ></i>
                    </div>
                    <div class="input-group mb-3" v-show="editable_entry=='staging_subdomain'">
                        <input type="text" name="" value="" id="" class="form-control col-4" v-model="setting.value" />
                        <div class="input-group-append">
                            <button @click="saveSetting" class="btn btn-secondary" type="button" style="" v-html=" saving ? spinner : 'Save' "></button>
                        </div>
                        <div class="input-group-append">
                            <button @click="cancelSetting" class="btn btn-outline-secondary" type="button" style="border: 1px solid #E5E5E5;border-top-right-radius:8px;border-bottom-right-radius:8px" v-html=" 'Cancel' "></button>
                        </div>
                    </div>
                    <div> <i style="color: #AFAFAF">@{{ getSettingValue( 'staging_subdomain' ) }}.@{{ getSettingValue( 'dev_server' ) }}</i></div>
                </div>
            </div>
        </div>
        <div class="row m-4">
        </div>
        <div class="row m-4">
            <div class="col-12" style="text-align: right">
                <a href="javascript:" class="btn btn-primary" @click="setupSubDomains()" v-html="setting_records ? spinner : 'Setup Virtual Hosts' ">  </a>
            </div>
        </div>
    </div>
    <div id="successModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Success ! </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    Dev and staging server was successfully setup. All you need to do now is restart Apache !
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection


