document.addEventListener("DOMContentLoaded", function () {
    const themeSwitcher = document.querySelector(".theme-switcher");
    const body = document.body;
    
    // Verificar se já há um tema salvo no localStorage
    const savedTheme = localStorage.getItem("theme") || "light-theme";
    body.classList.add(savedTheme);
    // Atualizar o texto do botão baseado no tema atual
    themeSwitcher.innerHTML = savedTheme === "dark-theme" ? `<img class="theme-switcher" src="https://img.icons8.com/?size=100&id=mMAh5QagZNle&format=png&color=FFFFFF">` : `<img class="theme-switcher" src="https://img.icons8.com/?size=100&id=9h2z6sWouiD5&format=png&color=FFFFFF">`;

    themeSwitcher.addEventListener("click", function () {
        // Alterna entre os temas claro e escuro
        if (body.classList.contains("light-theme")) {
            body.classList.replace("light-theme", "dark-theme");
            localStorage.setItem("theme", "dark-theme");
            themeSwitcher.innerHTML = `<img class="theme-switcher" src="https://img.icons8.com/?size=100&id=mMAh5QagZNle&format=png&color=FFFFFF">`;
        } else {
            body.classList.replace("dark-theme", "light-theme");
            localStorage.setItem("theme", "light-theme");
            themeSwitcher.innerHTML = `<img class="theme-switcher" src="https://img.icons8.com/?size=100&id=9h2z6sWouiD5&format=png&color=FFFFFF">`;
        }
    });
});