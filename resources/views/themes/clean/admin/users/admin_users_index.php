<div class="mb-10" v-cloak>
    <div class="float-right">
        <i class="icon icon-plus3"></i>
    </div>
    <h3 class="breadcrumb-header"><?php echo __('Users') ?></h3>
</div>
<div id="main-wrapper">
    <div class="row">
        <div class="col-3">
            <div class="panel mb-30">
                <div class="panel-body widget-desk  p-4 m-0" >
                    <div class="mb-15">
                        <div class="input-group mb-3">

                            <input type="text" class="form-control" placeholder="Search"
                                   v-on:keyup.enter="search()" v-model="q"
                                   aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2" v-html="searching ? spinner : search_icon " @click="search()">

                                </span>
                            </div>
                        </div>
                    </div>
                    <div>
                    <table class="table table-striped">
                        <tr v-for="u in users" class="pointer" v-bind:style=" u.id == user.id ? {background:'#EFFFEF'} : '' ">
                            <td @click="userSelected( u.id )">
                                <img :src="u.profile_photo_url" class="img-fluid" style="width: 36px; border-radius: 50%;"/> {{u.full_name}}
                            </td>
                            <td style="text-align: right">
                                <div class="dropdown">
                                    <i class="icon-menu pointer" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                    <div style="z-index:999" class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="javascript:" @click="openUserModal(u.id)">Edit</a>
                                        <a class="dropdown-item" href="#">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr v-show="!users.length && !searching">
                            <td> No user found</td>
                        </tr>
                    </table>
                    </div>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination" v-show="page_list.length > 1 ">
                            <li class="page-item" v-for="p in page_list" :class=" current_page==p ? 'active' : '' ">
                                <a class="page-link" v-show="p!='...'" href="javascript:" @click="goToPage( p )" >{{p}}</a>
                                <a class="page-link" v-show="p=='...'" href="javascript:">{{p}}</a>
                            </li>
                        </ul>
                    </nav>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <div class="col-9">
            <div class="panel mb-30">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-3">
                            <img :src="user.profile_photo_url" class="img-fluid" />
                            <div style="text-align: center" class="mt-10">
                                <a href="javascript:" @click="uploadPhoto()" class="btn btn-primary">
                                    <i class="icon-upload4"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-9">
                            <div class="float-right">
                                <div class="dropdown">
                                    <i class="icon-menu pointer" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                    <div style="z-index:999" class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="javascript:" @click="openUserModal(user.id)">Edit</a>
                                        <a class="dropdown-item" href="javascript:">Delete</a>
                                    </div>
                                </div>
                            </div>
                            <h4><b>{{user.full_name}}</b></h4>
                             {{user.country}} - {{user.mobile_number}} <br />
                             {{user.user_type}} since {{user.since}}
                            <hr />


                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>

