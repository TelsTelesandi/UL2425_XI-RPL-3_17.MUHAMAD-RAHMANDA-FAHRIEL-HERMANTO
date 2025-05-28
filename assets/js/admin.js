// JavaScript for admin pages
document.addEventListener("DOMContentLoaded", () => {
  // Sidebar toggle
  const sidebarToggle = document.getElementById("sidebarToggle")
  const adminSidebar = document.querySelector(".admin-sidebar")

  if (sidebarToggle && adminSidebar) {
    sidebarToggle.addEventListener("click", () => {
      adminSidebar.classList.toggle("active")
    })
  }

  // Close sidebar when clicking outside on mobile
  document.addEventListener("click", (event) => {
    if (
      window.innerWidth <= 991 &&
      adminSidebar &&
      adminSidebar.classList.contains("active") &&
      !adminSidebar.contains(event.target) &&
      event.target !== sidebarToggle
    ) {
      adminSidebar.classList.remove("active")
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
