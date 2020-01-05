@extends('layouts.gentelella')

@section('title', __('Sign In'))

@section('body')

    <div>
        <a class="hiddenanchor" id="signup"></a>
        <a class="hiddenanchor" id="signin"></a>

        <div class="login_wrapper">
            <div class="animate form login_form login_design">
                <section class="login_content">
                    <form action="{{ url('auth/admin/login') }}" method="post">
                        <input class="hidden" type="checkbox" name="remember" checked>
                        {{ csrf_field() }}
                        <h1>Login</h1>
                        <div>
                            <input type="text" class="form-control" placeholder="Email" required="" name="email" value="{{ old('email') }}">
                        </div>
                        <div>
                            <input type="password" class="form-control" placeholder="Password" required="" name="password">
                        </div>
                        <div>
                            <button class="btn btn-default submit" type="submit">{{ __('Sign In') }}</button>
                        </div>

                        <div class="clearfix"></div>

                        <div class="separator">
                            <div class="clearfix"></div>
                            <div>
                                <p>Copyright © 2020 - {{ config('app.name') }}!</p>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>

@endsection
