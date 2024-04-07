const toggleDropdown = () => {
    let dropdownMenu = document.querySelector("#dropdownMenu");

    dropdownMenu.classList.toggle("show");
};

window.onclick = (event) => {
    console.log(event.target);
    if (!event.target.matches(".btn")) {
        let dropdowns = document.getElementsByClassName("dropdown-menu");

        dropdowns.forEach((dropdown) => {
            let openDropdown = dropdown;

            if (openDropdown.classList.contains("show")) {
                openDropdown.classList.remove("show");
            }
        });
    }
};
