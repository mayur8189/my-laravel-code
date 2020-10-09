<!DOCTYPE html>
<html>
    <head>
        @include('includes.head')
    </head>
    <body>
        <div class="page login-page">
            <div class="container d-flex align-items-center">
                <div class="form-holder has-shadow">
                    <div class="row">
                        <!-- Logo & Information Panel-->
                        <div class="col-lg-6">
                            <div class="info d-flex align-items-center">
                                <div class="content">
                                    <div class="logo">
                                        <h1>New User Registration</h1>
                                    </div>
                                    <p>Discover thousands of live events and checkout with ease with your Ticket Broker Tools (TBT) account.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Form Panel    -->
                        <div class="col-lg-6 bg-white">
                            <div class="form d-flex align-items-center">
                                <div class="content"> 
                                    @if(session()->has('success'))
                                    <div class="alert alert-success mb-4">
                                        {{ session()->get('success') }}
                                    </div>
                                    @endif
                                    @if(session()->has('error'))
                                    <div class="alert alert-danger mb-4">
                                        {{ session()->get('error') }}
                                    </div>
                                    @endif
                                    <div class="clearfix"></div>
                                    <form action="{{ url('doregister') }}"  class="form-validate"  method="post"> 
                                        @csrf
                                        <div class="form-group">
                                            <input id="register-firstname" type="text" name="firstname" required="" data-msg="Please enter your first name" class="input-material" value="{{ old('firstname') }}">
                                            <label for="register-firstname" class="label-material">First Name</label>
                                            <div style="color:red">{{($errors->first('firstname'))}}</div>  
                                        </div>
<!--                                        <div class="form-group">
                                            <input id="register-lastname" type="text" name="lastname" required="" data-msg="Please enter your last name" class="input-material" value="{{ old('lastname') }}">
                                            <label for="register-lastname" class="label-material">Last Name</label>
                                            <div style="color:red">{{($errors->first('lastname'))}}</div>  
                                        </div>-->
                                        <div class="form-group">
                                            <input id="register-username" type="text" name="username"  required=""  data-msg="Please enter your user name" class="input-material" value="{{ old('username') }}">
                                            <label for="register-username" class="label-material">User Name</label>
                                            <div style="color:red">{{($errors->first('username'))}}</div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <input id="register-email" type="email" name="email" required="" data-msg="Please enter your email" class="input-material" value="{{ old('email') }}">
                                            <label for="register-email" class="label-material">Email</label>
                                            <div style="color:red">{{($errors->first('email'))}}</div>  
                                        </div>
                                        <div class="form-group">
                                            <input id="register-password" type="password" name="password"  required=""  data-msg="Please enter your password" class="input-material">
                                            <label for="register-password" class="label-material">Password</label>
                                            <div style="color:red">{{($errors->first('password'))}}</div>
                                        </div>
                                        <div class="form-group">
                                            <input id="register-confirm-confirmpassword" type="password" name="confirmpassword" required="" data-msg="Please enter your confirm password" class="input-material" >
                                            <label for="register-confirm-confirmpassword" class="label-material">confirm password</label>
                                            <div style="color:red">{{($errors->first('confirmpassword'))}}</div> 
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>  
                                    </form> 
                                    <!--<a href="#" class="forgot-pass">Forgot Password?</a><br>--> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
        <!-- JavaScript files-->         
        @include('includes.foot') 
    </body>
</html>