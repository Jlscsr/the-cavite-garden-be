const createCategoriesButton = (element) => {
    const categories = ["Test1", "Test2", "Test3", "Test4", "Test5"];
    const container = document.querySelector(`#${element}`);

    categories.forEach((category) => {
        const buttonContainer = document.createElement("div");
        const button = document.createElement("button");

        buttonContainer.classList.add("col");
        button.type = "button";
        button.textContent = category;

        buttonContainer.appendChild(button);

        button.addEventListener("click", () => {
            console.log(category);
        });

        container.appendChild(buttonContainer);
    });
};

export { createCategoriesButton };
