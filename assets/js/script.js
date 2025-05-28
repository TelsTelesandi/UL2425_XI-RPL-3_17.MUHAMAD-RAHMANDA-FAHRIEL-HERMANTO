// Common JavaScript for the public pages
document.addEventListener("DOMContentLoaded", () => {
  // Add active class to current nav item
  const currentLocation = window.location.pathname
  const navLinks = document.querySelectorAll("nav ul li a")

  navLinks.forEach((link) => {
    if (link.getAttribute("href") === currentLocation) {
      link.classList.add("active")
    }
  })
})
