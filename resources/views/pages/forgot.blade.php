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
                                        <h1>Forgot Password</h1>
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
                                    <form action="{{ url('doforget') }}"  class="form-validate"  method="post"> 
                                        @csrf
                                        <div class="form-group">
                                            <input id="forgot-email" type="email" name="email" required="" data-msg="Please enter your Email Id" class="input-material" >
                                            <label for="forgot-emailid" class="label-material">Email Id</label>
                                            <div style="color:red">{{($errors->first('email'))}}</div>  
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>  
                                    </form>
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