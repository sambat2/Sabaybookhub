const autoWriteElement = document.getElementById("auto-write");
const texts = ["100+", "Ethical Hacking", "Mathematics", "Programming"];
let index = 0;
let charIndex = 0;

function typeText() {
  if (charIndex < texts[index].length) {
    autoWriteElement.textContent += texts[index][charIndex];
    charIndex++;
    setTimeout(typeText, 100);
  } else {
    setTimeout(eraseText, 1000);
  }
}

function eraseText() {
  if (charIndex > 0) {
    autoWriteElement.textContent = texts[index].substring(0, charIndex - 1);
    charIndex--;
    setTimeout(eraseText, 100);
  } else {
    index = (index + 1) % texts.length;
    setTimeout(typeText, 500);
  }
}

typeText();

const mobileMenuBtn = document.getElementById("mobile-menu-btn");
const mobileMenu = document.getElementById("mobile-menu");
const desktopNavbar = document.getElementById("desktop-navbar"); // Add reference to the desktop navbar

mobileMenuBtn.addEventListener("click", () => {
    mobileMenu.classList.toggle("hidden");
    if(desktopNavbar){
        mobileMenu.classList.add("md:hidden");

    }
});

document.getElementById("buyForm").addEventListener("submit", async (event) => {
  event.preventDefault();

  const formData = new FormData(event.target);
  const bookPrice = document.getElementById("bookPrice").textContent.replace("$", "");
  formData.append("bookPrice", bookPrice);

  try {
    const response = await fetch("./api/submit-purchase.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();
    if (result.success) {
      alert("Purchase submitted successfully!");
      document.getElementById("buyModal").classList.add("hidden");
    } else {
      alert(result.message);
    }
  } catch (error) {
    console.error("Error submitting purchase:", error);
    alert("An error occurred. Please try again.");
  }
});
