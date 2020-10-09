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
                                        <h1>Login</h1>
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
                                    <form action="{{ url('dologin') }}"  class="form-validate"  method="post"> 
                                        @csrf
                                        <div class="form-group">
                                            <input id="login-username" type="text" name="username" required="" data-msg="Please enter your username" class="input-material" value="{{ old('username') }}">
                                            <label for="login-username" class="label-material">User Name</label>
                                            <div style="color:red">{{($errors->first('username'))}}</div>  
                                        </div>
                                        <div class="form-group">
                                            <input id="login-password" type="password" name="password"  required=""  data-msg="Please enter your password" class="input-material">
                                            <label for="login-password" class="label-material">Password</label>
                                            <div style="color:red">{{($errors->first('password'))}}</div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Login</button>  
                                    </form>
                                    <a href="{{ url('forgot') }}" class="text-primary mt-4">Forgot Password</a><br>
                                    
                                    <a href="{{ url('register') }}" class="text-primary mt-4">New User Registration</a><br>
<!--                                    <a href="#" class="forgot-pass">Forgot Password?</a><br> -->
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