@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_header')
<a href="{{ route('voyager.users.index') }}" class="btn btn-info" style="margin-left: 30px">
                <i class="voyager-angle-left"></i>Back
        </a>
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> @if(isset($dataTypeContent->id)){{ 'Edit' }}@else{{ 'New' }}@endif {{ $dataType->display_name_singular }}
    </h1>

@stop

@section('content')
    <div class="page-content container-fluid">

        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">

                    <div class="panel-heading">
                        <h3 class="panel-title">@if(isset($dataTypeContent->id)){{ 'Edit' }}@else{{ 'Add New' }}@endif {{ $dataType->display_name_singular }}</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form id = "usercreate" class="form-edit-add" role="form"
                          action="@if(isset($dataTypeContent->id)){{ route('voyager.'.$dataType->slug.'.update', $dataTypeContent->id) }}@else{{ route('voyager.'.$dataType->slug.'.store') }}@endif"
                          method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if(isset($dataTypeContent->id))
                            {{ method_field("PUT") }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name"
                                    placeholder="Name" id="name"
                                    value="@if(isset($dataTypeContent->name)){{ old('name', $dataTypeContent->name) }}@else{{old('name')}}@endif">
                            </div>

                            <div class="form-group">
                                <label for="name">Email</label>
                                <input type="text" class="form-control" name="email"
                                       placeholder="Email" id="email"
                                       value="@if(isset($dataTypeContent->email)){{ old('email', $dataTypeContent->email) }}@else{{old('email')}}@endif">
                            </div>

                            <div class="form-group">
                                <label for="birthday">Birthday</label>
                                <div class='input-group date' id='birthday'>
                                    <input type='text' class="form-control" name='birthday' id='birthday' value="@if(isset($dataTypeContent->birthday)){{ old('birthday', $dataTypeContent->birthday) }}@else{{old('birthday')}}@endif"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="name">City</label>
                                <input type="text" class="form-control" name="city"
                                       placeholder="City" id="city"
                                       value="@if(isset($dataTypeContent->city)){{ old('city', $dataTypeContent->city) }}@else{{old('city')}}@endif">
                            </div>

                            <div class="form-group">
                                <label for="bio">Bio</label>
                                <textarea name="bio" id="bio" class="form-control" cols="50" rows="5">@if(isset($dataTypeContent->bio)){{ old('bio', $dataTypeContent->bio) }}@else{{old('bio')}}@endif</textarea>
                            </div>
                            <div class="form-group">
                                <label for="interesting">interesting</label>
                                <textarea name="interesting" id="interesting" class="form-control" cols="50" rows="5">@if(isset($dataTypeContent->interesting)){{ old('interesting', $dataTypeContent->interesting) }}@else{{old('interesting')}}@endif</textarea>
                            </div>

                            <div class="form-group">
                                <label for="latitude">Latitude</label>
                                <input type="textarea" class="form-control" name="latitude"
                                    placeholder="Latitude" id="latitude"
                                    value="@if(isset($dataTypeContent->latitude)){{ old('latitude', $dataTypeContent->latitude) }}@else{{old('latitude')}}@endif">
                            </div>

                            <div class="form-group">
                                <label for="longitude">Longitude</label>
                                <input type="text" class="form-control" name="longitude"
                                    placeholder="Latitude" id="longitude"
                                    value="@if(isset($dataTypeContent->longitude)){{ old('longitude', $dataTypeContent->longitude) }}@else{{old('longitude')}}@endif">
                            </div>

                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select name="gender" id="gender" class="form-control">
                                    <option value=0 @if(isset($dataTypeContent) && $dataTypeContent->gender == 0) selected @endif>{{'Man'}}</option>
                                    <option value=1 @if(isset($dataTypeContent) && $dataTypeContent->gender == 1) selected @endif>{{'Woman'}}</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                @if(isset($dataTypeContent->password))
                                    <br>
                                    <small>Leave empty to keep the same</small>
                                @endif
                                <input type="password" class="form-control" name="password"
                                       placeholder="Password" id="password"
                                       value="">
                            </div>

                            <div class="form-group">
                                <label for="avatar">Avatar</label>
                                @if(isset($dataTypeContent->avatar))
                                    <img src="{{ Voyager::image( $dataTypeContent->avatar ) }}"
                                         style="width:200px; height:auto; clear:both; display:block; padding:2px; border:1px solid #ddd; margin-bottom:10px;">
                                @endif
                                 <input type="hidden" name="avatar_url" id="avatar_url" value="{{ Voyager::image( $dataTypeContent->avatar ) }}">
                                 <input type="hidden" name="user_id" id="user_id" value="{{ $dataTypeContent->id}}">
                                <input type="file" name="avatar">
                            </div>
                            @if(isset($dataTypeContent->id))
                            <div class="form-group">
                                <label for="avatar">Profile Images</label>
                                <a href="{{ route('voyager.userimages.index', 'user_id='.$dataTypeContent->id) }}" class="btn btn-xs btn-info pull-right">Profile Images</a>
                            </div>
                            @endif

                            <div class="form-group">
                                <label for="role">User Role</label>
                                <select name="role_id" id="role" class="form-control">
                                    <?php $roles = TCG\Voyager\Models\Role::all()->sortByDesc("id"); ?>
                                    @foreach($roles as $role)
                                        <option value="{{$role->id}}" @if(isset($dataTypeContent) && $dataTypeContent->role_id == $role->id) selected @endif>{{$role->display_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status">Active Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value=1 @if(isset($dataTypeContent) && $dataTypeContent->status == 1) selected @endif>{{'Active'}}</option>
                                    <option value=0 @if(isset($dataTypeContent) && $dataTypeContent->status == 0) selected @endif>{{'Inactive'}}</option>
                                    
                                </select>
                            </div>



                        </div><!-- panel-body -->

                        <div class="panel-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                          enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                        <input name="image" id="upload_file" type="file"
                               onchange="$('#my_form').submit();this.value='';">
                        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                        {{ csrf_field() }}
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')


    <script type="text/javascript">
       (function(d, m){var s, h;       
       s = document.createElement("script");
       s.type = "text/javascript";
       s.async=true;
       s.src="https://apps.applozic.com/sidebox.app";
       h=document.getElementsByTagName('head')[0];
       h.appendChild(s);
       window.applozic=m;
       m.init=function(t){m._globals=t;}})(document, window.applozic || {});
    </script>


    <script type="text/javascript">
        $(function () {
            $('#birthday').datetimepicker({
                viewMode: 'days',
                format: 'YYYY/MM/DD'
            });
        });

    </script>

    <script>
        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();

            var nameValue = document.getElementById("name").value;
            var avatarURL = document.getElementById("avatar_url").value;

            var userId = document.getElementById("user_id").value;;
            
            if(nameValue.length > 0){
                window.applozic.init({
                    appId: '29d9da79ffdee368152253225c58ab27e',      //Get your application key from https://www.applozic.com
                    userId: userId,                     //Logged in user's id, a unique identifier for user
                    userName: nameValue,                 //User's display name
                    imageLink : avatarURL,                     //User's profile picture url
                    email : '',                         //optional
                    contactNumber: '',                  //optional, pass with internationl code eg: +13109097458
                    desktopNotification: true,
                    source: '1',                          // optional, WEB(1),DESKTOP_BROWSER(5), MOBILE_BROWSER(6)
                    notificationIconLink: 'https://www.applozic.com/favicon.ico',    //Icon to show in desktop notification, replace with your icon
                    authenticationTypeId: 1,          //1 for password verification from Applozic server and 0 for access Token verification from your server
                    accessToken: '',                    //optional, leave it blank for testing purpose, read this if you want to add additional security by verifying password from your server https://www.applozic.com/docs/configuration.html#access-token-url
                    locShare: true,
                    googleApiKey: "AIzaSyDKfWHzu9X7Z2hByeW4RRFJrD9SizOzZt4",   // your project google api key 
                    googleMapScriptLoaded : false,   // true if your app already loaded google maps script
                    mapStaticAPIkey: "AIzaSyCWRScTDtbt8tlXDr6hiceCsU83aS2UuZw",
                    autoTypeSearchEnabled : true,     // set to false if you don't want to allow sending message to user who is not in the contact list
                    loadOwnContacts : false, //set to true if you want to populate your own contact list (see Step 4 for reference)
                    olStatus: false,         //set to true for displaying a green dot in chat screen for users who are online
                    onInit : function(response) {
                       if (response === "success") {
                          // login successful, perform your actions if any, for example: load contacts, getting unread message count, etc
                       } else {
                          // error in user login/register (you can hide chat button or refresh page)
                       }
                   },
                   contactDisplayName: function(otherUserId) {
                         //return the display name of the user from your application code based on userId.
                         return "";
                   },
                   contactDisplayImage: function(otherUserId) {
                         //return the display image url of the user from your application code based on userId.
                         return "";
                   },
                   onTabClicked: function(response) {
                         // write your logic to execute task on tab load
                         //   object response =  {
                         //    tabId : userId or groupId,
                         //    isGroup : 'tab is group or not'
                         //  }
                   }
                });
            }
        });


    </script>
    <script src="{{ voyager_asset('lib/js/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ voyager_asset('js/voyager_tinymce.js') }}"></script>
@stop
