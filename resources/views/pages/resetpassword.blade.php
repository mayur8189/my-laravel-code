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
                                        <h1>Reset Password</h1>
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
                                    <form action="{{ url('doreset') }}"  class="form-validate"  method="post"> 
                                        @csrf
                                        <input type="hidden" name="newtoken" value="{{ $data['newtoken'] }}">
                                        <div class="form-group">
                                            <input id="reset-password" type="password" name="newpassword" required="" data-msg="Please enter New Password" class="input-material" >
                                            <label for="forgot-emailid" class="label-material">New Password</label>
                                            <div style="color:red">{{($errors->first('email'))}}</div>  
                                        </div>
                                        <div class="form-group">
                                            <input id="reset-conpassword" type="password" name="confirmpassword" required="" data-msg="Please enter Comfirm Password" class="input-material" >
                                            <label for="forgot-confirmpassword" class="label-material">Comfirm Password</label>
                                            <div style="color:red">{{($errors->first('confirmpassword'))}}</div>  
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