@extends('layouts.app')
@section('content')

<div class="container wide">
      
            <div class="ph-intro">
                
                <div class="circle circle1"></div>
                <div class="circle circle2"></div>
                <div class="circle circle3"></div>

                <div class="ph-intro-head">
                    <h1>Your Time. <span>Perfectly Managed.</span></h1>
                    <div class="ph-intro-descr">One platform. <span>Endless use cases.</span></div>
                </div>


  <div class="ph-cases">

    <div class="ph-case">
      <div class="ph-case-i">
        <h5 class="ph-case-title">Driving Schools</h5>
        <img src="https://latepoint.com/wp-content/uploads/2025/10/driving.png">
        <a class="btn btn-primary ss">Show Use Case</a>

      </div>
    </div>

    <div class="ph-case">
      <div class="ph-case-i">
        <h5 class="ph-case-title">Yoga Studios</h5>
        <img src="https://latepoint.com/wp-content/uploads/2025/10/yoga.png">
        <a class="btn btn-primary ss">Show Use Case</a>
      </div>
    </div>

    <div class="ph-case">
      <div class="ph-case-i">
        <h5 class="ph-case-title">Photographers</h5>
        <img src="https://latepoint.com/wp-content/uploads/2025/10/photo.png">
         <a class="btn btn-primary ss">Show Use Case</a>
      </div>
    </div>

    <div class="ph-case">
      <div class="ph-case-i">
        <h5 class="ph-case-title">Private Tutors</h5>
        <img src="https://latepoint.com/wp-content/uploads/2025/10/piano.png">
         <a class="btn btn-primary ss">Show Use Case</a>
      </div>
    </div>

    <div class="ph-case">
      <div class="ph-case-i">
        <h5 class="ph-case-title">Bike Rental</h5>
        <img src="https://latepoint.com/wp-content/uploads/2025/10/bike.png">
         <a class="btn btn-primary ss">Show Use Case</a>
      </div>
    </div>

     <div class="ph-case">
      <div class="ph-case-i">
        <h5 class="ph-case-title">Bike Rental</h5>
        <img src="https://latepoint.com/wp-content/uploads/2025/10/bike.png">
         <a class="btn btn-primary ss">Show Use Case</a>
      </div>
    </div>

     <div class="ph-case">
      <div class="ph-case-i">
        <h5 class="ph-case-title">Bike Rental</h5>
        <img src="https://latepoint.com/wp-content/uploads/2025/10/bike.png">
         <a class="btn btn-primary ss">Show Use Case</a>
      </div>
    </div>
     <div class="ph-case">
      <div class="ph-case-i">
        <h5 class="ph-case-title">Bike Rental</h5>
        <img src="https://latepoint.com/wp-content/uploads/2025/10/bike.png">
         <a class="btn btn-primary ss">Show Use Case</a>
      </div>
    </div>
     <div class="ph-case">
      <div class="ph-case-i">
        <h5 class="ph-case-title">Bike Rental</h5>
        <img src="https://latepoint.com/wp-content/uploads/2025/10/bike.png">
         <a class="btn btn-primary ss">Show Use Case</a>
      </div>
    </div>

  </div>
<div class="">
      
    </div>

            </div>
        </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
        
<script>
$('.ph-cases').slick({
  slidesToShow: 7,
  slidesToScroll: 1,
  infinite: true,
  arrows: true,
  dots: false,
   prevArrow: '<button class="custom-prev "><</button>',
  nextArrow: '<button class="custom-next">></button>',
  centerMode: true,
  centerPadding: '0px',
  responsive: [
    {
      breakpoint: 1200,
      settings: { slidesToShow: 3 }
    },
    {
      breakpoint: 992,
      settings: { slidesToShow: 3 }
    },
    {
      breakpoint: 600,
      settings: { slidesToShow: 3 }
    }
  ]
});
</script>
@endsection
