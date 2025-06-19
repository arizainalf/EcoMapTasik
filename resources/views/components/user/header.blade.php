 <header>
     <div class="container-fluid">
         <div class="row py-3 border-bottom">

             <div class="col-sm-4 col-lg-3 text-center text-sm-start">
                 <div class="d-flex justify-content-start align-items-center">
                     <a href="{{ url('/') }}">
                         <img src="{{ asset('storage/' . getSetting()->logo) }}" alt="logo" class="img-fluid rounded"
                             width="75px">
                     </a>
                     <h4 class="ms-2 mb-0">{{ getSetting()->app_name }}</h4>
                 </div>
             </div>

             <div class="col-sm-6 offset-sm-2 offset-md-0 col-lg-5 d-none d-lg-block">

             </div>

             <div
                 class="col-sm-8 col-lg-4 d-flex justify-content-end gap-5 align-items-center mt-4 mt-sm-0 justify-content-center justify-content-sm-end">
                 <div class="support-box text-end d-none d-xl-block">
                     <span class="fs-6 text-muted">Have a question?</span>
                     <h5 class="mb-0">{{ getSetting()->phone_number }}</h5>
                 </div>

                 <ul class="d-flex justify-content-end list-unstyled m-0">
                     <li>
                         <a href="{{ url('/') }}" class="rounded-circle bg-light p-2 mx-1">
                             <svg width="24" height="24" viewBox="0 0 24 24">
                                 <use xlink:href="#home"></use>
                             </svg>
                         </a>
                     </li>
                     <li>
                         <a href="{{ route('peta.index') }}" class="rounded-circle bg-light p-2 mx-1">
                             <svg width="24" height="24" viewBox="0 0 24 24">
                                 <use xlink:href="#map-pin"></use>
                             </svg>
                         </a>
                     </li>
                     <li class="d-lg-none">
                         <a href="#" class="rounded-circle bg-light p-2 mx-1" data-bs-toggle="offcanvas"
                             data-bs-target="#offcanvasCart" aria-controls="offcanvasCart">
                             <svg width="24" height="24" viewBox="0 0 24 24">
                                 <use xlink:href="#cart"></use>
                             </svg>
                         </a>
                     </li>
                     <li class="d-lg-none">
                         <a href="#" class="rounded-circle bg-light p-2 mx-1" data-bs-toggle="offcanvas"
                             data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                             <svg width="24" height="24" viewBox="0 0 24 24">
                                 <use xlink:href="#menu"></use>
                             </svg>
                         </a>
                     </li>
                 </ul>

                 <div class="cart text-end d-none d-lg-block dropdown">
                     <button class="border-0 bg-transparent d-flex flex-column gap-2 lh-1" type="button"
                         data-bs-toggle="offcanvas" data-bs-target="#offcanvasCart" aria-controls="offcanvasCart">
                         <span class="fs-6 text-muted dropdown-toggle">
                             <svg width="24" height="24" viewBox="0 0 24 24">
                                 <use xlink:href="#cart"></use>
                             </svg>
                         </span>
                     </button>
                 </div>
             </div>

         </div>
     </div>
     <div class="container-fluid">
         <div class="row py-3">
             <div class="d-flex  justify-content-center justify-content-sm-between align-items-center">
                 <nav class="main-menu d-flex navbar navbar-expand-lg">

                     <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
                         aria-labelledby="offcanvasNavbarLabel">

                         <div class="offcanvas-header justify-content-center">
                             <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                 aria-label="Close"></button>
                         </div>

                         <div class="offcanvas-body">

                             <ul class="navbar-nav justify-content-end menu-list list-unstyled d-flex gap-md-3 mb-0">
                                 <li class="nav-item">
                                     <a href="{{ route('home') }}" class="nav-link">Home</a>
                                 </li>
                                 <li class="nav-item">
                                     <a href="{{ route('peta.index') }}" class="nav-link">Peta</a>
                                 </li>
                                 @auth
                                     <li class="nav-item">
                                         <a href="{{ route('logout') }}" class="nav-link">logout</a>
                                     </li>
                                 @endauth
                                 @guest
                                     <li class="nav-item">
                                         <a href="{{ route('login') }}" class="nav-link">Login</a>
                                     </li>
                                 @endguest
                                 @if (Auth::check() && Auth::user()->role == 'admin')
                                     <li class="nav-item">
                                         <a href="/admin" class="nav-link">Dashboard</a>
                                     </li>
                                 @endif
                             </ul>

                         </div>

                     </div>
                 </nav>
             </div>
         </div>
     </div>
 </header>
