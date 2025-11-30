document.addEventListener("DOMContentLoaded", () => {
    // THAY ĐỔI: Lấy thẻ body thay vì nav
    const body = document.querySelector("body"); 
    const menuIcons = document.querySelectorAll(".menu-icon");
    const overlay = document.querySelector(".overlay");
    const sidebarElement = document.querySelector('.sidebar');

    if (!body) return;

    menuIcons.forEach(icon => {
        icon.addEventListener("click", () => {
            // THAY ĐỔI: Toggle class 'open' trên body
            body.classList.toggle("open"); 
            
            const opened = body.classList.contains("open");
            if (sidebarElement) {
                sidebarElement.setAttribute('aria-hidden', !opened);
            }
        });
    });

    if (overlay) {
        overlay.addEventListener("click", () => {
            // THAY ĐỔI: Xóa class 'open' trên body
            body.classList.remove("open");
            
            if (sidebarElement) {
                sidebarElement.setAttribute('aria-hidden', 'true');
            }
        });
    }
});