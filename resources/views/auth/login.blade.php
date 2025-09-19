@extends('layouts/blankLayout')

@section('title', 'Login - Kaeres')

@section('page-style')
@vite([
    'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection

@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <!-- Login -->
            <div class="card px-sm-6 px-0">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center">
                        <a href="{{url('/')}}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">@include('_partials.macros',["width"=>25,"withbg"=>'var(--bs-primary)'])</span>
                            <span class="app-brand-text demo text-heading fw-bold">Kaeres</span>
                        </a>
                    </div>

                    @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <span class="badge bg-success me-2">Berhasil</span>
                        <div>{{ session('success') }}</div>
                    </div>
                    @endif
                    
                    @error('error')
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <span class="badge bg-danger me-2">Gagal</span>
                    <div class="text-danger mt-1">{{ $message }}</div>
                    </div>
                    @enderror
                    <!-- /Logo -->
                    <h4 class="mb-1">Welcome to Kaeres! 👋</h4>
                    <p class="mb-6">Sign in to manage your Kartu Rencana Studi (KRS) and schedule your courses easily.</p>

                    <form id="" class="mb-6" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-6">
                            <label for="email" class="form-label">Email or Username</label>
                            <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email or username" autofocus>
                        </div>
                        <div class="mb-6 form-password-toggle">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                        <div class="mb-8">
                            <div class="d-flex justify-content-between mt-8">
                                <div class="form-check mb-0 ms-2">
                                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember">
                                    <label class="form-check-label" for="remember-me">
                                        Remember Me
                                    </label>
                                </div>
                                <a href="{{url('auth/forgot-password-basic')}}">
                                    <span>Forgot Password?</span>
                                </a>
                            </div>
                        </div>
                        <div class="mb-6">
                            <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
                        </div>
                    </form>

                    <p class="text-center">
                        <span>New to Kaeres?</span>
                        <a href="{{url('/register')}}">
                            <span>Create your account</span>
                        </a>
                    </p>
                </div>
            </div>
        </div>
        <!-- /Login -->
    </div>
</div>
@endsection

<script type="module">
  // Import SDK
  import { initializeApp } from "https://www.gstatic.com/firebasejs/9.22.2/firebase-app.js";
  import { getAuth, signInWithEmailAndPassword } from "https://www.gstatic.com/firebasejs/9.22.2/firebase-auth.js";

  // Firebase Config
  const firebaseConfig = {
    apiKey: "{{ config('services.firebase.api_key') }}",
    authDomain: "{{ config('services.firebase.auth_domain') }}",
    projectId: "{{ config('services.firebase.project_id') }}",
  };

  const app = initializeApp(firebaseConfig);
  const auth = getAuth(app);

  document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;

    try {
      const userCredential = await signInWithEmailAndPassword(auth, email, password);
      const token = await userCredential.user.getIdToken();

      // Kirim token ke Laravel
      fetch('/firebase-login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ token })
      }).then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            window.location.href = "/dashboard";
          } else {
            alert("Login gagal");
          }
        });

    } catch (error) {
      alert(error.message);
    }
  });
</script>
