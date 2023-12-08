<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   setcookie('user_id', create_unique_id(), time() + 60*60*24*30, '/');
   header('location:index.php');
}

if(isset($_POST['check'])){

   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   // if the hotel has total 30 rooms 
   if($total_rooms >= 30){
      $warning_msg[] = 'rooms are not available';
   }else{
      $success_msg[] = 'rooms are available';
   }

}

if(isset($_POST['book'])){

   $booking_id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $rooms = $_POST['rooms'];
   $rooms = filter_var($rooms, FILTER_SANITIZE_STRING);
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $check_out = $_POST['check_out'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_STRING);
   $adults = $_POST['adults'];
   $adults = filter_var($adults, FILTER_SANITIZE_STRING);
   $childs = $_POST['childs'];
   $childs = filter_var($childs, FILTER_SANITIZE_STRING);

   $total_rooms = 0;

   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   while($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)){
      $total_rooms += $fetch_bookings['rooms'];
   }

   if($total_rooms >= 30){
      $warning_msg[] = 'rooms are not available';
   }else{

      $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ?");
      $verify_bookings->execute([$user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);

      if($verify_bookings->rowCount() > 0){
         $warning_msg[] = 'room booked alredy!';
      }else{
         $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, rooms, check_in, check_out, adults, childs) VALUES(?,?,?,?,?,?,?,?,?,?)");
         $book_room->execute([$booking_id, $user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);
         $success_msg[] = 'room booked successfully!';
      }

   }

}

if(isset($_POST['send'])){

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $message = $_POST['message'];
   $message = filter_var($message, FILTER_SANITIZE_STRING);

   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if($verify_message->rowCount() > 0){
      $warning_msg[] = 'message sent already!';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      $success_msg[] = 'message send successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- home section starts  -->

<section class="home" id="home">

   <div class="swiper home-slider">

      <div class="swiper-wrapper">

         <div class="box swiper-slide">
            <img src="images/hotel 1.jpg" alt="">
            <div class="flex">
               <h3>luxurious rooms</h3>
               <a href="#availability" class="btn">check availability</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/hotel 2.jpg" alt="">
            <div class="flex">
               <h3>luxurious rooms</h3>
               <a href="#reservation" class="btn">make a reservation</a>
            </div>
         </div>

         <div class="box swiper-slide">
            <img src="images/hotel 3.jpg" alt="">
            <div class="flex">
               <h3>luxurious rooms</h3>
               <a href="#contact" class="btn">contact us</a>
            </div>
         </div>

      </div>

      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>

   </div>

</section>

<!-- home section ends -->

<!-- availability section starts  -->

<section class="availability" id="availability">

   <form action="" method="post">
      <div class="flex">
         <div class="box">
            <p>check in <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>check out <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>adults <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1">1 adult</option>
               <option value="2">2 adults</option>
               <option value="3">3 adults</option>
               <option value="4">4 adults</option>
               <option value="5">5 adults</option>
               <option value="6">6 adults</option>
               <option value="7">7 adults</option>
               <option value="8">8 adults</option>
               <option value="9">9 adults</option>
               <option value="10">10 adults</option>
            </select>
         </div>
         <div class="box">
            <p>childs <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="-">0 child</option>
               <option value="1">1 child</option>
               <option value="2">2 childs</option>
               <option value="3">3 childs</option>
               <option value="4">4 childs</option>
               <option value="5">5 childs</option>
               <option value="6">6 childs</option>
               <option value="7">7 childs</option>
               <option value="8">8 childs</option>
               <option value="9">9 childs</option>
               <option value="10">10 childs</option>
            </select>
         </div>
         <div class="box">
            <p>rooms <span>*</span></p>
            <select name="rooms" class="input" required>
               <option value="1">Room 103 King Family Suite</option>
               <option value="2">Rooms 201 Queen Deluxe</option>
               <option value="3">Rooms 201 Queen Deluxe</option>
               <option value="4">Rooms 203 Big Family Suite</option>
               <option value="5">Rooms 205 Queen Deluxe</option>
               <option value="6">Rooms 206 King Family Suite</option>
               <option value="7">Rooms 207 Queen Deluxe</option>
               <option value="8">Rooms 208 VIP Queen Deluxe</option>
            </select>
         </div>
      </div>
      <input type="submit" value="check availability" name="check" class="btn">
   </form>

</section>

<!-- availability section ends -->

<!-- about section starts  -->

<section class="about" id="about">

   <div class="row">
      <div class="image">
         <img src="images/front desk.gif" alt="">
      </div>
      <div class="content">
         <h3>best staff</h3>
         <a href="#reservation" class="btn">make a reservation</a>
      </div>
   </div>

   <div class="row revers">
      <div class="images">
         <img src="images/tapang_taal.jpg" alt="">
      </div>
      <div class="content">
         <h3>best foods</h3>
         <a href="#contact" class="btn">contact us</a>
      </div>
   </div>


</section>

<!-- about section ends -->

<!-- services section starts  -->

<section class="services">

   <div class="box-container">

      <div class="box">
         <img src="images/cleaner.png" alt="">
         <h3>Cleaner</h3>
      </div>

      <div class="box">
         <img src="images/free-parking.png" alt="">
         <h3>Free Parking</h3>
      </div>

      <div class="box">
         <img src="images/internet.png" alt="">
         <h3>Free Wifi</h3>
      </div>

      <div class="box">
         <img src="images/information-desk.png" alt="">
         <h3>24 hr Front Desk</h3>
      </div>

      <div class="box">
         <img src="images/security-guard.png" alt="">
         <h3>24 hr Security Guard</h3>
      </div>

      <div class="box">
         <img src="images/air-conditioner.png" alt="">
         <h3>Air Condition</h3>
      </div>

   </div>

</section>

<!-- services section ends -->

<!-- reservation section starts  -->

<section class="reservation" id="reservation">

   <form action="" method="post">
      <h3>make a reservation</h3>
      <div class="flex">
         <div class="box">
            <p>your name <span>*</span></p>
            <input type="text" name="name" maxlength="50" required placeholder="Enter your name" class="input">
         </div>
         <div class="box">
            <p>your email <span>*</span></p>
            <input type="email" name="email" maxlength="50" required placeholder="Enter your email" class="input">
         </div>
         <div class="box">
            <p>your number <span>*</span></p>
            <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="Enter your number" class="input">
         </div>
         <div class="box">
            <p>rooms <span>*</span></p>
            <select name="rooms" class="input" required>
            <option value="1" selected>Room 103 King Family Suite</option>
               <option value="2">Rooms 201 Queen Deluxe</option>
               <option value="3">Rooms 201 Queen Deluxe</option>
               <option value="4">Rooms 203 Big Family Suite</option>
               <option value="5">Rooms 205 Queen Deluxe</option>
               <option value="6">Rooms 206 King Family Suite</option>
               <option value="7">Rooms 207 Queen Deluxe</option>
               <option value="8">Rooms 208 VIP Queen Deluxe</option>
            </select>
         </div>
         <div class="box">
            <p>check in <span>*</span></p>
            <input type="date" name="check_in" class="input" required>
         </div>
         <div class="box">
            <p>check out <span>*</span></p>
            <input type="date" name="check_out" class="input" required>
         </div>
         <div class="box">
            <p>adults <span>*</span></p>
            <select name="adults" class="input" required>
               <option value="1" selected>1 adult</option>
               <option value="2">2 adults</option>
               <option value="3">3 adults</option>
               <option value="4">4 adults</option>
               <option value="5">5 adults</option>
               <option value="6">6 adults</option>
               <option value="6">6 adults</option>
               <option value="7">7 adults</option>
               <option value="8">8 adults</option>
               <option value="9">9 adults</option>
               <option value="10">10 adults</option>
            </select>
         </div>
         <div class="box">
            <p>childs <span>*</span></p>
            <select name="childs" class="input" required>
               <option value="0" selected>0 child</option>
               <option value="1">1 child</option>
               <option value="2">2 childs</option>
               <option value="3">3 childs</option>
               <option value="4">4 childs</option>
               <option value="5">5 childs</option>
               <option value="6">6 childs</option>
               <option value="7">7 childs</option>
               <option value="8">8 childs</option>
               <option value="9">9 childs</option>
               <option value="10">10 childs</option>
            </select>
         </div>
      </div>
      <input type="submit" value="book now" name="book" class="btn">
   </form>

</section>

<!-- reservation section ends -->

<!-- gallery section starts  -->

<section class="gallery" id="gallery">

   <div class="swiper gallery-slider">
      <div class="swiper-wrapper">
         <img src="images/ROOM 208-VIP QUEEN SUITE.png" class="swiper-slide" alt="">
         <img src="images/ROOM 207-QUEEN DELUXE.png" class="swiper-slide" alt="">
         <img src="images/room 206-king family suite.jpg" alt="">
         <img src="images/ROOM 205-QUEEN DELUXE.png" class="swiper-slide" alt="">
         <img src="images/room 203 -big family suite.jpg" class="swiper-slide" alt="">
         <img src="images/ROOM 202-QUEEN DELUXE.png" class="swiper-slide" alt="">
         <img src="images/ROOM 201-QUEEN DELUXE.png" class="swiper-slide" alt="">
         <img src="images/ROOM 103-KING FAMILY SUITE.png" class="swiper-slide" alt="">
      </div>
      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- gallery section ends -->

<!-- contact section starts  -->

<section class="contact" id="contact">

   <div class="row">

      <form action="" method="post">
         <h3>send us message</h3>
         <input type="text" name="name" required maxlength="50" placeholder="Enter your name" class="box">
         <input type="email" name="email" required maxlength="50" placeholder="Enter your email" class="box">
         <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="Enter your number" class="box">
         <textarea name="message" class="box" required maxlength="1000" placeholder="Enter your message" cols="30" rows="10"></textarea>
         <input type="submit" value="send message" name="send" class="btn">
      </form>

      <div class="faq">
         <h3 class="title">frequently asked questions</h3>
         <div class="box active">
            <h3>How to cancel?</h3>
            <p>To cancel your reservation just click cancel to immediately cancel your reservation.</p>
         </div>
         <div class="box">
            <h3>Is there any vacancy?</h3>
            <p>Yes, if you want to know the availability of every room just click the availability that shows at the homepage and select the date you want avail.</p>
         </div>
         <div class="box">
            <h3>What are payment methods?</h3>
            <p>Just send it via Gcash, or debit/Credit Card</p>
         </div>
      </div>

   </div>

</section>

<!-- contact section ends -->

<!-- reviews section starts  -->

<section class="reviews" id="reviews">

   <div class="swiper reviews-slider">

      <div class="swiper-wrapper">
         <div class="swiper-slide box">
            <img src="images/Arellano.jpg" alt="">
            <h3>Arellano, Mariane</h3>
            <p>The staff was great. The receptionists were very helpful and answered all our questions. The room was clean and bright, and the room service was always on time. Will be coming back! Thank you so much!</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/Ebora.jpg" alt="">
            <h3>Ebora, Ivan Charles</h3>
            <p>Okay naman, Their customer service is great, isang tawag lang nandyan na agad. We’re hoping that other hotels can give good and great services like what your hotel did. Next Year we’ll be back here. Thank you.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/11.jpg" alt="">
            <h3>Maranan, Aira Mae</h3>
            <p>Friendly and personal service The staff were all very friendly and helpful and no request for assistance was a problem for them. The hotel is well situated, being close to shopping, transport, and the tourist sites. We would definitely stay there again.</p>
         </div>
         <div class="swiper-slide box">
            <img src="images/nohay.jpg" alt="">
            <h3>Nohay, Jewel Irish</h3>
            <p>Great hotel. Everything was nice. Me and my  family would surely choose it again next time we visit. Great location, great service, friendly staff. Good value for the money. Breakfast was delicious. Very comfortable. Would definitely recommend Thank you!</p>
         </div>
         
      </div>

      <div class="swiper-pagination"></div>
   </div>

</section>

<!-- reviews section ends  -->





<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

</body>
</html>