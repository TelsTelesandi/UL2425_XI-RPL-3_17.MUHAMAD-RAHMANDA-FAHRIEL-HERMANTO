// JavaScript for user pages
document.addEventListener("DOMContentLoaded", () => {
  // Sidebar toggle
  const sidebarToggle = document.getElementById("sidebarToggle")
  const userSidebar = document.querySelector(".user-sidebar")

  if (sidebarToggle && userSidebar) {
    sidebarToggle.addEventListener("click", () => {
      userSidebar.classList.toggle("active")
    })
  }

  // Close sidebar when clicking outside on mobile
  document.addEventListener("click", (event) => {
    if (
      window.innerWidth <= 991 &&
      userSidebar &&
      userSidebar.classList.contains("active") &&
      !userSidebar.contains(event.target) &&
      event.target !== sidebarToggle
    ) {
      userSidebar.classList.remove("active")
    }
  })

  // Add active class to current sidebar item
  const currentLocation = window.location.pathname
  const sidebarLinks = document.querySelectorAll(".sidebar-nav ul li a")

  sidebarLinks.forEach((link) => {
    if (currentLocation.includes(link.getAttribute("href"))) {
      link.classList.add("active")
    }
  })
})
