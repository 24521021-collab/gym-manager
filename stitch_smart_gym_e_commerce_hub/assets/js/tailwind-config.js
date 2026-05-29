tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                primary: "#e31837",             /* Đỏ KOR GYM */
                "primary-container": "#ffb3b1", /* Đỏ nhạt */
                "on-primary": "#ffffff",        /* Chữ trắng trên nền đỏ */
                "surface-container-high": "#2a2a2a",
                "surface-variant": "#353535",
                background: "#131313",          /* Nền đen tuyền */
                "on-background": "#e2e2e2",     /* Chữ xám sáng */
            },
            fontFamily: {
                headline: ["Oswald", "sans-serif"],    /* Font Tiêu đề */
                body: ["Montserrat", "sans-serif"],    /* Font Nội dung */
            }
        }
    }
}