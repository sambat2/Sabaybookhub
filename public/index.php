<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SabayBookHub-mini</title>
    <link rel="stylesheet" href="assets/css/main.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
      integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="bg-gray-100">
    <!--mobile navbar-->
    <header
      class="md:hidden flex justify-between items-center p-5 border-[1px] shadow-sm fixed top-0 left-0 w-full bg-white z-10"
    >
      <a class="text-2xl text-blue-500 hover:text-blue-700" href="#">
        <h1>SabayBookHub-mini</h1>
      </a>
      <button id="mobile-menu-btn" class="text-2xl text-blue-500 hover:text-blue-700">
        <i class="fa-solid fa-bars"></i>
      </button>
    </header>
    <div id="mobile-menu" class="hidden fixed top-[60px] left-0 w-full bg-white shadow-md z-10">
      <ul class="flex flex-col space-y-3 p-5 text-[1.1rem]">
        <li><a class=" text-blue-500 hover:text-blue-700" href="#">Home</a></li>
        <li><a class="text-blue-500 hover:text-blue-700" href="#books">Books</a></li>
        <li><a class="text-blue-500 hover:text-blue-700" href="#about">About</a></li>
      </ul>
    </div>
    <!--end mobile navbar-->
    <!--desktop navbar-->
    <nav id="desktop-navbar" class="hidden fixed md:flex justify-between items-center p-5 border-[1px] shadow-sm top-0 w-full bg-white z-10">
      <a class="text-2xl text-blue-500 hover:text-blue-700" href="#">
        <h1>
          SabayBookHub-mini</h1>
      </a>
      <ul class="flex space-x-5 gap-10 text-[1.1rem] items-center ">
        <li>
          <a class="text-blue-500 hover:text-blue-700" href="#">Home</a>
        </li>
        <li>
          <a class="text-blue-500 hover:text-blue-700" href="#books">Books</a>
        </li>
        <li>
          <a class="text-blue-500 hover:text-blue-700" href="#about">About</a>
        </li>
      </ul>
    </nav>
    <!--end desktop navbar-->    
    <!--hero-->
    <section class="hero bg-blue-500 text-white text-center p-10 mt-[80px] transition-all duration-300">
      <h1 class="text-4xl">Welcome to SabayBookHub-mini</h1>
      <p class="text-[1.1rem]">The best place to find your favorite books</p>
      <!--auto write-->
      <div class="mt-5">
        <span class="text-[1.1rem]">We have</span>
        <span id="auto-write" class="text-2xl font-bold"></span>
        <span class="text-[1.1rem]">books for you</span>
      </div>
    </section>
    <!--end hero-->
    <!--books-->
    <section id="books" class="books p-10 text-center">
      <h2 class="text-3xl text-center transition-all duration-300">Our Books</h2>
      <div id="book-container" class="grid grid-cols-1 md:grid-cols-3 gap-10 mt-5">
        <!-- Books will be dynamically loaded here -->
      </div>
      <!--pagination-->
      <div class="flex justify-center items-center mt-10">
        <button id="prev-btn" class="text-[1.1rem] text-blue-500 hover:text-blue-700 bg-white px-3 py-1 rounded-md border-[1px] shadow-sm mr-5 hover:bg-gray-100" disabled>
          <span>Previous</span>
        </button>
        <div id="pagination-info" class="text-[1.1rem] text-blue-500"></div>
        <button id="next-btn" class="text-[1.1rem] text-blue-500 hover:text-blue-700 bg-white px-3 py-1 rounded-md border-[1px] shadow-sm ml-5 hover:bg-gray-100">
          <span>Next</span>
        </button>
      </div>
      <!--end pagination-->
    </section>
    <!--end books-->
    <!-- Modal -->
    <div id="buyModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center">
      <div class="bg-white p-6 rounded shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-4">Purchase Book</h2>
        <form id="buyForm">
          <!-- qr code -->
          <div class="mb-4">
            <label for="qrCode" class="block text-gray-700">QR Code</label>
            <img  src="./assets/images/image.png" alt="" width="200"  class="rounded-md" />
          </div>
          <div class="mb-4">
            <label for="name" class="block text-gray-700">Name</label>
            <input type="text" id="name" name="name" class="w-full p-2 border border-gray-300 rounded" required />
          </div>
          <div class="mb-4">
            <label for="contact" class="block text-gray-700">Telegram Name or Phone Number</label>
            <input type="text" id="contact" name="contact" class="w-full p-2 border border-gray-300 rounded" required />
          </div>
          <div class="mb-4">
            <label for="receipt" class="block text-gray-700">Upload Receipt</label>
            <input type="file" id="receipt" name="receipt" class="w-full p-2 border border-gray-300 rounded" required />
          </div>
          <div class="flex justify-end">
            <button type="button" id="cancelBtn" class="mr-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
              Submit
              <!--get price-->
              <span id="bookPrice" class="ml-2"></span>
            </button>
          </div>
        </form>
      </div>
    </div>
    <!--footer-->
    <footer id="about" class="h mt-7 bg-blue-500 text-white transition-all duration-300">
      <div class="text-center">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 p-10">
          <div>
            <h3 class="text-2xl">Location</h3>
            <p class="text-[1.1rem]">Toul Kouk</p>
            <p class="text-[1.1rem]">Phnom Penh</p>
            <p class="text-[1.1rem]">Street 374</p>
          </div>
          <div>
            <h3 class="text-2xl">Contact Us</h3>
            <p>
              <i class="fa-solid fa-phone"></i>
              <span class="ml-2">+855 966 222 395</span>
            </p>
            <p>
              <i class="fa-solid fa-envelope"></i>
              <span class="ml-2">
                <a href="mailto:sabaybookhubmini@gmail.com">Email: sabaybookhubmini@gmail.com</a>
              </span>

            </p>
            <a href="#" class="flex items-center justify-center text-[1.5rem] mt-5 hover:text-gray-300">
                <i class="fa-brands fa-telegram"></i>
                <span class="text-[1rem]">@sabaybookhubmini</span>
            </a>
            <a href="#" class="flex items-center justify-center text-[1.5rem] mt-5 hover:text-gray-300">
                <i class="fa-brands fa-facebook"></i>
                <span class="text-[1rem]">SabayBookHub-mini</span>
            </a>
          </div>
          <div class="text-center ">
            <h3 class="text-2xl">About Us</h3>
            <p class="text-[1.1rem]">
              SabayBookHub-mini is a small online book store that provides a
              variety of books for all ages.
            </p>

          </div>
      </div>
    </footer>
    <!--end footer-->
    <script src="./assets/js/pagination.js"></script>
    <script src="./assets/js/script.js"></script>
  </body>
</html>
