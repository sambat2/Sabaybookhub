let books = [];
const booksPerPage = 6;
let currentPage = 1;

async function fetchBooks() {
  try {
    const response = await fetch("./api/book.php");
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const text = await response.text();
    try {
      books = JSON.parse(text);
      renderBooks();
    } catch (jsonError) {
      throw new Error("Failed to parse JSON");
    }
  } catch (error) {
    console.error("Error fetching books:", error);
  }
}

function renderBooks() {
  const bookContainer = document.getElementById("book-container");
  bookContainer.innerHTML = "";

  const startIndex = (currentPage - 1) * booksPerPage;
  const endIndex = startIndex + booksPerPage;
  const currentBooks = books.slice(startIndex, endIndex);

  currentBooks.forEach((book) => {
    const bookElement = document.createElement("div");
    bookElement.className = "book bg-white p-5 shadow-sm";
    bookElement.innerHTML = `
      <div class="flex justify-center items-center">
        <img class="w-full h-60 object-cover" src="./${book.image}" style="width: 200px; height: 300px;" alt="${book.title}" />
      </div>
      <h3 class="text-2xl mt-5">${book.title}</h3>
      <p class="text-[1.1rem] mt-2">Author: ${book.author}</p>
      <p class="text-[1.1rem] mt-2">Price: $${book.price}</p>
      <div class="mt-5">
        <button class="buy-now-btn text-[1.1rem] text-blue-500 hover:text-blue-700 bg-white px-3 py-1 rounded-md border-[1px] shadow-sm hover:bg-gray-100" data-price="${book.price}">
          <span>Buy Now</span>
        </button>
      </div>
    `;
    bookContainer.appendChild(bookElement);
  });

  updatePaginationButtons();
}

function updatePaginationButtons() {
  const prevBtn = document.getElementById("prev-btn");
  const nextBtn = document.getElementById("next-btn");
  const paginationInfo = document.getElementById("pagination-info");

  prevBtn.disabled = currentPage === 1;
  nextBtn.disabled = currentPage * booksPerPage >= books.length;

  paginationInfo.textContent = `Page ${currentPage} of ${Math.ceil(
    books.length / booksPerPage
  )}`;
}

document.getElementById("prev-btn").addEventListener("click", () => {
  if (currentPage > 1) {
    currentPage--;
    renderBooks();
  }
});

document.getElementById("next-btn").addEventListener("click", () => {
  if (currentPage * booksPerPage < books.length) {
    currentPage++;
    renderBooks();
  }
});

// Initial load
fetchBooks();

document.addEventListener("DOMContentLoaded", () => {
  const buyModal = document.getElementById("buyModal");
  const cancelBtn = document.getElementById("cancelBtn");
  const buyForm = document.getElementById("buyForm");

  // Show the modal when "Buy Now" is clicked
  document.addEventListener("click", (event) => {
    const buyNowButton = event.target.closest(".buy-now-btn");
    if (buyNowButton) {
      const bookPrice = buyNowButton.getAttribute("data-price");
      const bookPriceSpan = document.getElementById("bookPrice");
      bookPriceSpan.textContent = `$${bookPrice}`; // Update the price in the modal

      buyModal.classList.remove("hidden"); // Show the modal
    }
  });

  // Close the modal when "Cancel" is clicked
  cancelBtn.addEventListener("click", () => {
    buyModal.classList.add("hidden"); // Hide the modal
  });

  // Handle form submission
  buyForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    const formData = new FormData(buyForm);

    try {
      const response = await fetch("/public/api/submit-purchase.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (response.ok) {
        alert(result.message || "Purchase submitted successfully!");
      } else {
        console.error("Server Response:", result);
        alert(result.message || "Failed to submit purchase.");
      }
    } catch (error) {
      console.error("Error submitting purchase:", error);
      alert("An error occurred while submitting the purchase.");
    }

    buyModal.classList.add("hidden");
  });
});
