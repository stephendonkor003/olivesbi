var Common = {
    showSpinner: function (container, message) {
        // Ensure container exists and is a valid DOM element
        if (!container || !(container instanceof Element)) {
            console.error("Invalid container:", container);
            return;
        }

        // Remove existing spinner (prevent duplicates)
        Common.hideSpinner(container);

        let spinner = document.createElement("div");
        spinner.className = "spinner-overlay";
        spinner.innerHTML = `
            <div class="spinner">
                <div class="loader"></div>
                <p>${message}</p>
            </div>`;

        container.appendChild(spinner);
    },

    hideSpinner: function (container) {
        if (!container || !(container instanceof Element)) return;

        let spinner = container.querySelector(".spinner-overlay");
        if (spinner) {
            container.removeChild(spinner);
        }
    }
};
